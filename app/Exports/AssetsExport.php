<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function collection()
    {
        return Asset::with(['category', 'subcategory', 'region', 'court', 'assignedUser'])->get();
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
            'Created At',
            'Last Updated'
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
            $asset->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Auto-size columns
            'A:W' => [
                'autoSize' => true
            ],
        ];
    }

    public function title(): string
    {
        return 'All Assets';
    }
}