<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourtsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
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
            'A' => 30, // name
            'B' => 15, // code
            'C' => 20, // type
            'D' => 20, // region
            'E' => 20, // location
            'F' => 35, // address
            'G' => 25, // presiding_judge
            'H' => 25, // registry_officer
            'I' => 12, // is_active
        ];
    }
}