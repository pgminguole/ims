<?php

namespace App\Exports;

use App\Models\Court;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CourtsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Court::withDeviceCounts()
                    ->with(['region', 'location', 'presidingJudge', 'registryOfficer']);

        // Apply filters
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (!empty($this->filters['region_id'])) {
            $query->where('region_id', $this->filters['region_id']);
        }

        if (!empty($this->filters['location_id'])) {
            $query->where('location_id', $this->filters['location_id']);
        }

        if (!empty($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }

        if (!empty($this->filters['search'])) {
            $query->search($this->filters['search']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Code',
            'Type',
            'Region',
            'Location',
            'Address',
            'Presiding Judge',
            'Registry Officer',
            'Total Assets',
            'Computers',
            'Laptops',
            'Printers',
            'Scanners',
            'Photocopiers',
            'UPS',
            'Stabilizers',
            'DTS Systems',
            'Cameras',
            'Televisions',
            'Child Friendly',
            'Networking',
            'Status',
            'Created At'
        ];
    }

    public function map($court): array
    {
        return [
            $court->id,
            $court->name,
            $court->code,
            $court->type_formatted,
            $court->region ? $court->region->name : 'N/A',
            $court->location ? $court->location->name : 'N/A',
            $court->address,
            $court->presidingJudge ? $court->presidingJudge->name : 'N/A',
            $court->registryOfficer ? $court->registryOfficer->name : 'N/A',
            $court->total_assets,
            $court->computers,
            $court->laptops,
            $court->printers,
            $court->scanners,
            $court->photocopiers,
            $court->ups,
            $court->stabilizers,
            $court->dts_assets,
            $court->cameras,
            $court->televisions,
            $court->child_friendly,
            $court->networking,
            $court->is_active ? 'Active' : 'Inactive',
            $court->created_at->format('Y-m-d H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:X' => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // Name
            'C' => 12,  // Code
            'D' => 15,  // Type
            'E' => 20,  // Region
            'F' => 20,  // Location
            'G' => 30,  // Address
            'H' => 25,  // Presiding Judge
            'I' => 25,  // Registry Officer
            'J' => 12,  // Total Assets
            'K' => 12,  // Computers
            'L' => 12,  // Laptops
            'M' => 12,  // Printers
            'N' => 12,  // Scanners
            'O' => 12,  // Photocopiers
            'P' => 8,   // UPS
            'Q' => 12,  // Stabilizers
            'R' => 12,  // DTS Systems
            'S' => 10,  // Cameras
            'T' => 12,  // Televisions
            'U' => 15,  // Child Friendly
            'V' => 12,  // Networking
            'W' => 10,  // Status
            'X' => 18,  // Created At
        ];
    }
}