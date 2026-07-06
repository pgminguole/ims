<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
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
                'font' => ['bold' => true, 'size' => 12],
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
            'A' => 15, // first_name
            'B' => 15, // last_name
            'C' => 30, // email
            'D' => 15, // username
            'E' => 15, // phone
            'F' => 12, // access_type
            'G' => 25, // court
            'H' => 20, // location
            'I' => 30, // roles
            'J' => 12, // status
            'K' => 12, // is_approved
            'L' => 15, // password
            'M' => 25, // registry_officer
            'N' => 12, // block
            'O' => 20, // require_password_reset
        ];
    }
}