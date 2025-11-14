<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $headers;
    protected $sampleData;

    public function __construct(array $headers, array $sampleData)
    {
        $this->headers = $headers;
        $this->sampleData = $sampleData;
    }

    public function array(): array
    {
        return $this->sampleData;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // asset_name
            'B' => 15, // asset_tag
            'C' => 18, // serial_number
            'D' => 18, // model
            'E' => 15, // brand
            'F' => 15, // manufacturer
            'G' => 20, // category
            'H' => 20, // subcategory
            'I' => 18, // region
            'J' => 25, // court
            'K' => 18, // location
            'L' => 12, // status
            'M' => 12, // condition
            'N' => 15, // purchase_date
            'O' => 15, // received_date
            'P' => 15, // assigned_date
            'Q' => 15, // purchase_cost
            'R' => 15, // current_value
            'S' => 20, // supplier
            'T' => 18, // warranty_period
            'U' => 18, // warranty_expiry
            'V' => 30, // warranty_information
            'W' => 15, // ip_address
            'X' => 18, // mac_address
            'Y' => 30, // specifications
            'Z' => 30, // description
            'AA' => 20, // assigned_to
            'AB' => 15, // assigned_type
            'AC' => 20, // depreciation_method
            'AD' => 25, // maintenance_schedule
            'AE' => 18, // last_maintenance
            'AF' => 18, // next_maintenance
            'AG' => 30, // maintenance_notes
        ];
    }
}