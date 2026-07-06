<?php

namespace App\Exports;

use App\Models\Office;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class OfficesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Office::with(['region', 'location'])
                    ->withCount(['users', 'assets']);

        // Apply filters
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
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('address', 'like', '%' . $this->filters['search'] . '%');
            });
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
            'Contact Person',
            'Contact Email',
            'Contact Phone',
            'Total Users',
            'Total Assets',
            'Status',
            'Created At'
        ];
    }

    public function map($office): array
    {
        return [
            $office->id,
            $office->name,
            $office->code,
            $office->type,
            $office->region ? $office->region->name : 'N/A',
            $office->location ? $office->location->name : 'N/A',
            $office->address,
            $office->contact_person,
            $office->contact_email,
            $office->contact_phone,
            $office->users_count,
            $office->assets_count,
            $office->is_active ? 'Active' : 'Inactive',
            $office->created_at->format('Y-m-d H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:N' => ['alignment' => ['wrapText' => true]],
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
            'H' => 25,  // Contact Person
            'I' => 25,  // Contact Email
            'J' => 15,  // Contact Phone
            'K' => 12,  // Total Users
            'L' => 12,  // Total Assets
            'M' => 10,  // Status
            'N' => 18,  // Created At
        ];
    }
}