<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsFilteredExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Asset::with(['category', 'subcategory', 'region', 'court', 'assignedUser']);
        
        // Apply the same filters as in the index method
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['condition'])) {
            $query->where('condition', $this->filters['condition']);
        }
        
        if (!empty($this->filters['region_id'])) {
            $query->where('region_id', $this->filters['region_id']);
        }
        
        if (!empty($this->filters['court_id'])) {
            $query->where('court_id', $this->filters['court_id']);
        }
        
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['subcategory_id'])) {
            $query->where('subcategory_id', $this->filters['subcategory_id']);
        }

        if (!empty($this->filters['assigned_type'])) {
            $query->where('assigned_type', $this->filters['assigned_type']);
        }

        if (!empty($this->filters['purchase_year'])) {
            $query->whereYear('purchase_date', $this->filters['purchase_year']);
        }

        if (!empty($this->filters['purchase_date_from'])) {
            $query->whereDate('purchase_date', '>=', $this->filters['purchase_date_from']);
        }

        if (!empty($this->filters['purchase_date_to'])) {
            $query->whereDate('purchase_date', '<=', $this->filters['purchase_date_to']);
        }

        if (!empty($this->filters['assigned_date_from'])) {
            $query->whereDate('assigned_date', '>=', $this->filters['assigned_date_from']);
        }

        if (!empty($this->filters['assigned_date_to'])) {
            $query->whereDate('assigned_date', '<=', $this->filters['assigned_date_to']);
        }

        return $query->latest('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Asset ID',
            'Asset Name',
            'Asset Tag',
            'Serial Number',
            'Brand',
            'Model',
            'Category',
            'Subcategory',
            'Region',
            'Court',
            'Status',
            'Condition',
            'Purchase Date',
            'Purchase Cost',
            'Current Value',
            'Assigned To',
            'Assigned Type',
            'Assigned Date',
            'Warranty Expiry',
            'IP Address',
            'MAC Address',
            'Created At'
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->asset_id,
            $asset->asset_name,
            $asset->asset_tag,
            $asset->serial_number,
            $asset->brand,
            $asset->model,
            $asset->category->name ?? 'N/A',
            $asset->subcategory->name ?? 'N/A',
            $asset->region->name ?? 'N/A',
            $asset->court->name ?? 'N/A',
            ucfirst($asset->status),
            ucfirst($asset->condition),
            $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : 'N/A',
            $asset->purchase_cost ? number_format($asset->purchase_cost, 2) : '0.00',
            $asset->current_value ? number_format($asset->current_value, 2) : '0.00',
            $asset->assignedUser ? $asset->assignedUser->full_name : 'N/A',
            $asset->assigned_type ? ucfirst($asset->assigned_type) : 'N/A',
            $asset->assigned_date ? $asset->assigned_date->format('Y-m-d') : 'N/A',
            $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : 'N/A',
            $asset->ip_address ?? 'N/A',
            $asset->mac_address ?? 'N/A',
            $asset->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:W' => ['autoSize' => true],
        ];
    }

    public function title(): string
    {
        return 'Filtered Assets';
    }
}