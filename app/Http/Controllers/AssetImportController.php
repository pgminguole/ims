<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Models\Court;
use App\Models\Office;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class AssetImportController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return view('assets.import.index', compact('regions'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'assignment_year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'region_id' => 'nullable|exists:regions,id'
        ]);

        try {
            $file = $request->file('file');
            $assignmentYear = $request->assignment_year;
            $regionId = $request->region_id;

            // Load spreadsheet
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            // Extract headers (first row)
            $headers = [];
            foreach ($data[1] as $colLetter => $cellValue) {
                if (!empty($cellValue)) {
                    $headers[$colLetter] = trim($cellValue);
                }
            }
            unset($data[1]); // Remove header row from data

            // Map headers to category types
            $categoryMapping = $this->getCategoryMapping($headers);
            
            // Check if we have any category mappings
            if (empty($categoryMapping)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No asset categories could be mapped. Please ensure categories exist in your database.',
                    'headers' => $headers,
                    'available_categories' => Category::all()->pluck('name')->toArray()
                ], 422);
            }

            // Process rows
            $previewData = [];
            $entityCache = [
                'users' => User::with('region', 'location')->get()->keyBy(fn($u) => strtolower(trim($u->name))),
                'courts' => Court::with(['region', 'location'])->get()->keyBy(fn($c) => strtolower(trim($c->name))),
                'offices' => Office::with(['region', 'location'])->get()->keyBy(fn($o) => strtolower(trim($o->name))),
                'categories' => Category::whereNull('parent_id')->get()->keyBy(fn($cat) => strtolower(trim($cat->name)))
            ];

            foreach ($data as $rowIndex => $row) {
                if (empty(array_filter($row))) continue;

                $entityName = trim($row['A'] ?? '');
                if (empty($entityName)) continue;

                $rowData = $this->processRow($row, $headers, $entityName, $categoryMapping, $entityCache, $assignmentYear, $regionId);
                
                if (!empty($rowData['assets'])) {
                    $previewData[] = $rowData;
                }
            }

            return response()->json([
                'success' => true,
                'preview' => $previewData,
                'categories' => $categoryMapping,
                'headers' => $headers,
                'assignment_year' => $assignmentYear,
                'region_id' => $regionId,
                'debug' => [
                    'total_headers' => count($headers),
                    'mapped_categories' => count($categoryMapping),
                    'unmapped_headers' => array_diff(array_values($headers), array_keys($categoryMapping))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 422);
        }
    }

    private function processRow($row, $headers, $entityName, $categoryMapping, $entityCache, $assignmentYear, $regionFilter)
    {
        // Determine entity type and find/create entity
        $entity = $this->findOrPrepareEntity($entityName, $entityCache, $regionFilter);
        
        if (!$entity) {
            return [];
        }

        $assets = [];
        
        // Process each column (category) - iterate through all headers
        foreach ($headers as $colLetter => $headerName) {
            if ($colLetter === 'A') continue; // Skip name column

            // Get the value for this column in the current row
            $cellValue = $row[$colLetter] ?? '';
            $quantity = (int)trim($cellValue);
            
            if ($quantity <= 0) continue; // Skip if no quantity or invalid

            $categoryInfo = $categoryMapping[$headerName] ?? null;
            if (!$categoryInfo) {
                // Log warning but continue processing other columns
                \Log::warning("Category not found for header: {$headerName}");
                continue;
            }

            // Create asset entries for this category
            for ($i = 0; $i < $quantity; $i++) {
                $assets[] = [
                    'id' => Str::uuid(),
                    'category_name' => $headerName,
                    'category_id' => $categoryInfo['id'],
                    'asset_name' => $headerName . ' - ' . $entity['name'],
                    'asset_id' => $this->generateAssetId($categoryInfo['code']),
                    'assigned_date' => $this->getRandomAssignmentDate($assignmentYear),
                    'status' => 'assigned',
                    'condition' => 'good'
                ];
            }
        }

        return [
            'entity' => $entity,
            'assets' => $assets
        ];
    }

    private function findOrPrepareEntity($name, $entityCache, $regionFilter)
    {
        $nameLower = strtolower(trim($name));

        // Check if it's a user (exact match first, then fuzzy)
        $user = $this->fuzzyMatchEntity($nameLower, $entityCache['users'], 'user', $regionFilter);
        if ($user) return $user;

        // Check if it's a court (exact match first, then fuzzy with location)
        $court = $this->fuzzyMatchEntity($nameLower, $entityCache['courts'], 'court', $regionFilter);
        if ($court) return $court;

        // Check if it's an office (exact match first, then fuzzy with location)
        $office = $this->fuzzyMatchEntity($nameLower, $entityCache['offices'], 'office', $regionFilter);
        if ($office) return $office;

        // Entity doesn't exist - prepare for creation
        $entityType = $this->guessEntityType($name);
        return [
            'type' => $entityType,
            'id' => null,
            'name' => $name,
            'exists' => false,
            'region_id' => $regionFilter,
            'region_name' => $regionFilter ? Region::find($regionFilter)->name : 'To be assigned'
        ];
    }

    private function fuzzyMatchEntity($searchName, $entities, $type, $regionFilter)
    {
        $searchName = strtolower(trim($searchName));
        
        // Try exact match first
        if ($entities->has($searchName)) {
            $entity = $entities->get($searchName);
            if ($regionFilter && $entity->region_id != $regionFilter) {
                return null;
            }
            $response = $this->buildEntityResponse($entity, $type);
            $response['match_info'] = 'Exact Match';
            return $response;
        }

        // Build searchable strings for each entity
        $searchableEntities = $entities->map(function($entity) use ($type) {
            $searchableStrings = [
                strtolower(trim($entity->name))
            ];
            
            // Add location name for all entity types
            if ($entity->location) {
                $locationName = strtolower(trim($entity->location->name));
                $entityName = strtolower(trim($entity->name));
                
                // Standard location variations
                $searchableStrings[] = $locationName . ' ' . $entityName;
                $searchableStrings[] = $entityName . ' ' . $locationName;
                
                // Type-specific variations
                if ($type === 'court') {
                    // Court-specific variations
                    $searchableStrings[] = $locationName . ' court';
                    $searchableStrings[] = $locationName . ' district court';
                    $searchableStrings[] = $locationName . ' high court';
                    $searchableStrings[] = $locationName . ' circuit court';
                    
                    // Add without "court" word if entity name contains it
                    if (stripos($entityName, 'court') !== false) {
                        $nameWithoutCourt = trim(str_ireplace(['court', 'district', 'high', 'circuit'], '', $entityName));
                        if (!empty($nameWithoutCourt)) {
                            $searchableStrings[] = $locationName . ' ' . $nameWithoutCourt;
                        }
                    }
                } elseif ($type === 'office') {
                    // Office-specific variations
                    $searchableStrings[] = $locationName . ' office';
                    $searchableStrings[] = $locationName . ' registry';
                    $searchableStrings[] = $locationName . ' secretariat';
                    $searchableStrings[] = $locationName . ' department';
                    
                    // Add without common office keywords
                    if (preg_match('/(office|registry|secretariat|department)/i', $entityName)) {
                        $nameWithoutKeywords = trim(preg_replace('/(office|registry|secretariat|department)/i', '', $entityName));
                        if (!empty($nameWithoutKeywords)) {
                            $searchableStrings[] = $locationName . ' ' . $nameWithoutKeywords;
                        }
                    }
                } elseif ($type === 'user') {
                    // User location variations (for users with locations)
                    $searchableStrings[] = $locationName . ' ' . $entityName;
                    
                    // User title variations (Justice, Judge, etc.)
                    if (preg_match('/(justice|judge|hon|honourable)/i', $entityName)) {
                        $nameWithoutTitle = trim(preg_replace('/(justice|judge|hon|honourable|mr|mrs|ms)/i', '', $entityName));
                        if (!empty($nameWithoutTitle)) {
                            $searchableStrings[] = $locationName . ' ' . $nameWithoutTitle;
                        }
                    }
                }
            }
            
            // Add region name for additional context
            if ($entity->region) {
                $regionName = strtolower(trim($entity->region->name));
                $entityName = strtolower(trim($entity->name));
                
                $searchableStrings[] = $regionName . ' ' . $entityName;
                
                // Region + Location combinations
                if ($entity->location) {
                    $locationName = strtolower(trim($entity->location->name));
                    $searchableStrings[] = $regionName . ' ' . $locationName . ' ' . $entityName;
                }
            }
            
            // Add code if available
            if (!empty($entity->code)) {
                $searchableStrings[] = strtolower(trim($entity->code));
            }
            
            return [
                'entity' => $entity,
                'searchable' => array_unique($searchableStrings) // Remove duplicates
            ];
        });

        // Fuzzy matching with similarity threshold
        $bestMatch = null;
        $highestSimilarity = 0;
        $threshold = 75; // Minimum 75% similarity
        $matchType = '';

        foreach ($searchableEntities as $item) {
            foreach ($item['searchable'] as $searchableString) {
                // Calculate similarity percentage
                similar_text($searchName, $searchableString, $similarity);
                $currentMatchType = 'Similar';
                
                // Check if search name is contained in searchable string or vice versa
                if (strlen($searchName) >= 5) {
                    if (stripos($searchableString, $searchName) !== false) {
                        $similarity = max($similarity, 82);
                        $currentMatchType = 'Partial Match';
                    } elseif (stripos($searchName, $searchableString) !== false) {
                        $similarity = max($similarity, 80);
                        $currentMatchType = 'Contains';
                    }
                }
                
                // Check for word-level matching (all significant words from search appear in entity)
                $searchWords = array_filter(explode(' ', $searchName), fn($w) => strlen($w) >= 3);
                $matchingWords = 0;
                foreach ($searchWords as $word) {
                    // Ignore common words
                    if (in_array($word, ['court', 'office', 'the', 'and', 'of'])) {
                        continue;
                    }
                    if (stripos($searchableString, $word) !== false) {
                        $matchingWords++;
                    }
                }
                
                $significantWords = array_filter($searchWords, fn($w) => !in_array($w, ['court', 'office', 'the', 'and', 'of']));
                
                if (count($significantWords) > 0 && $matchingWords == count($significantWords)) {
                    $similarity = max($similarity, 87);
                    $currentMatchType = 'Word Match';
                }
                
                // Check for location-based match
                if ($item['entity']->location) {
                    $locationName = strtolower(trim($item['entity']->location->name));
                    if (stripos($searchName, $locationName) !== false) {
                        $similarity = max($similarity, 85);
                        $currentMatchType = 'Location Match';
                    }
                }
                
                // Check for region-based match
                if ($item['entity']->region) {
                    $regionName = strtolower(trim($item['entity']->region->name));
                    if (stripos($searchName, $regionName) !== false) {
                        $similarity = max($similarity, 83);
                        if ($currentMatchType === 'Location Match') {
                            $currentMatchType = 'Region+Location Match';
                        } else {
                            $currentMatchType = 'Region Match';
                        }
                    }
                }
                
                if ($similarity > $highestSimilarity && $similarity >= $threshold) {
                    // Check region filter
                    if ($regionFilter && $item['entity']->region_id != $regionFilter) {
                        continue;
                    }
                    
                    $highestSimilarity = $similarity;
                    $bestMatch = $item['entity'];
                    $matchType = $currentMatchType;
                }
            }
        }

        if ($bestMatch) {
            $response = $this->buildEntityResponse($bestMatch, $type);
            $response['match_info'] = sprintf('%s (%d%%)', $matchType, round($highestSimilarity));
            $response['match_confidence'] = round($highestSimilarity);
            return $response;
        }

        return null;
    }

    private function buildEntityResponse($entity, $type)
    {
        return [
            'type' => $type,
            'id' => $entity->id,
            'name' => $entity->name,
            'exists' => true,
            'region_id' => $entity->region_id,
            'region_name' => $entity->region->name ?? 'N/A',
            'location_id' => $entity->location_id ?? null,
            'location_name' => $entity->location->name ?? null
        ];
    }

    private function guessEntityType($name)
    {
        $nameLower = strtolower($name);
        
        // Court indicators
        if (str_contains($nameLower, 'court') || 
            str_contains($nameLower, 'justice') ||
            str_contains($nameLower, 'judge')) {
            return 'court';
        }

        // Office indicators
        if (str_contains($nameLower, 'office') || 
            str_contains($nameLower, 'department') ||
            str_contains($nameLower, 'h/w') ||
            str_contains($nameLower, 'secretariat')) {
            return 'office';
        }

        // Default to user
        return 'user';
    }

    private function getCategoryMapping($headers)
    {
        $categories = Category::all();
        $mapping = [];

        foreach ($headers as $colLetter => $headerName) {
            if ($colLetter === 'A') continue; // Skip name column

            $headerLower = strtolower(trim($headerName));
            
            // Try exact match first
            $category = $categories->first(fn($cat) => strtolower(trim($cat->name)) === $headerLower);
            
            // Try partial match
            if (!$category) {
                $category = $categories->first(fn($cat) => 
                    str_contains(strtolower($cat->name), $headerLower) ||
                    str_contains($headerLower, strtolower($cat->name))
                );
            }
            
            // Try matching by common variations
            if (!$category) {
                $variations = $this->getCategoryVariations($headerLower);
                foreach ($variations as $variation) {
                    $category = $categories->first(fn($cat) => 
                        strtolower(trim($cat->name)) === $variation ||
                        str_contains(strtolower($cat->name), $variation)
                    );
                    if ($category) break;
                }
            }

            if ($category) {
                $mapping[$headerName] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code ?? 'AST'
                ];
            } else {
                // Log unmapped headers for debugging
                \Log::warning("Could not map header '{$headerName}' to any category");
            }
        }

        return $mapping;
    }

    private function getCategoryVariations($headerName)
    {
        $variations = [];
        
        // Common category name variations
        $mappings = [
            'computer' => ['computer', 'computers', 'desktop', 'desktops', 'pc', 'pcs'],
            'laptop' => ['laptop', 'laptops', 'notebook', 'notebooks'],
            'ups' => ['ups', 'uninterruptible power supply', 'power backup'],
            'printer' => ['printer', 'printers', 'printing'],
            'photocopier' => ['photocopier', 'photocopiers', 'copier', 'copiers', 'xerox'],
            'stabilizer' => ['stabilizer', 'stabilizers', 'voltage stabilizer', 'stab'],
            'scanner' => ['scanner', 'scanners', 'scanning'],
            'camera' => ['camera', 'cameras', 'cam', 'cctv'],
            'television' => ['television', 'televisions', 'tv', 'tvs', 'monitor'],
            'networking' => ['networking', 'network', 'router', 'routers', 'switch', 'switches']
        ];
        
        foreach ($mappings as $key => $variants) {
            foreach ($variants as $variant) {
                if (str_contains($headerName, $variant)) {
                    $variations = array_merge($variations, $variants);
                    break 2;
                }
            }
        }
        
        return array_unique($variations);
    }

    private function generateAssetId($categoryCode)
    {
        $prefix = strtoupper(substr($categoryCode ?? 'AST', 0, 3));
        $random = strtoupper(Str::random(4));
        $number = rand(1000, 9999);
        return "{$prefix}-{$random}-{$number}";
    }

    private function getRandomAssignmentDate($year)
    {
        $startDate = Carbon::create($year, 3, 1);
        $endDate = Carbon::create($year, 7, 31);
        
        $randomDays = rand(0, $startDate->diffInDays($endDate));
        return $startDate->addDays($randomDays)->format('Y-m-d');
    }

    public function import(Request $request)
    {
        $request->validate([
            'preview_data' => 'required|json',
            'assignment_year' => 'required|integer',
            'region_id' => 'nullable|exists:regions,id'
        ]);

        try {
            DB::beginTransaction();

            $previewData = json_decode($request->preview_data, true);
            $imported = ['users' => 0, 'courts' => 0, 'offices' => 0, 'assets' => 0];
            $errors = [];

            foreach ($previewData as $rowData) {
                try {
                    $entity = $rowData['entity'];
                    $assets = $rowData['assets'];

                    // Create entity if it doesn't exist
                    if (!$entity['exists']) {
                        $entityModel = $this->createEntity($entity);
                        $entity['id'] = $entityModel->id;
                        $imported[$entity['type'] . 's']++;
                    }

                    // Create assets
                    foreach ($assets as $assetData) {
                        $this->createAsset($assetData, $entity);
                        $imported['assets']++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Error processing {$entity['name']}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully',
                'imported' => $imported,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 422);
        }
    }

    private function createEntity($entityData)
    {
        $data = [
            'name' => $entityData['name'],
            'region_id' => $entityData['region_id'],
            'location_id' => $entityData['location_id'] ?? null,
            'is_active' => true
        ];

        switch ($entityData['type']) {
            case 'user':
                $data['email'] = Str::slug($entityData['name']) . '@judicial.gov.gh';
                $data['password'] = bcrypt('password123');
                $data['status'] = 'active';
                return User::create($data);

            case 'court':
                $data['code'] = strtoupper(Str::substr(Str::slug($entityData['name']), 0, 10));
                $data['type'] = 'high_court';
                return Court::create($data);

            case 'office':
                $data['code'] = strtoupper(Str::substr(Str::slug($entityData['name']), 0, 10));
                return Office::create($data);
        }
    }

    private function createAsset($assetData, $entity)
    {
        return Asset::create([
            'slug' => Str::slug($assetData['asset_name'] . '-' . time()),
            'asset_id' => $assetData['asset_id'],
            'asset_name' => $assetData['asset_name'],
            'category_id' => $assetData['category_id'],
            'status' => $assetData['status'],
            'condition' => $assetData['condition'],
            'assigned_date' => $assetData['assigned_date'],
            'assigned_to' => $entity['type'] === 'user' ? $entity['id'] : null,
            'assigned_type' => $entity['type'],
            'office_id' => $entity['type'] === 'office' ? $entity['id'] : null,
            'court_id' => $entity['type'] === 'court' ? $entity['id'] : null,
            'region_id' => $entity['region_id'],
            'location_id' => $entity['location_id'] ?? null
        ]);
    }
}