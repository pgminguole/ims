<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        $reportType = $this->reportData['reportType'];

        if ($reportType === 'assets') {
            return $this->reportData['assets'];
        } elseif ($reportType === 'dts') {
            return $this->reportData['dtsSystems'];
        }

        return collect([]);
    }

    public function headings(): array
    {
        $reportType = $this->reportData['reportType'];

        if ($reportType === 'assets') {
            return [
                'Asset Name',
                'Category',
                'Region',
                'Court',
                'Status',
                'Condition',
                'Assigned To'
            ];
        } elseif ($reportType === 'dts') {
            return [
                'DTS Name',
                'Court',
                'Region',
                'Monitors',
                'Splitters',
                'HDMI Short Cables (5M)',
                'HDMI Long Cables (20M)',
                'Extension Boards',
                'Trucking',
                'Sony Recorders',
                'Status'
            ];
        }

        return [];
    }

    public function map($row): array
    {
        $reportType = $this->reportData['reportType'];

        if ($reportType === 'assets') {
            $assignedTo = 'Not Assigned';
            if ($row->assigned_type === 'user' && $row->assignedUser) {
                $assignedTo = $row->assignedUser->name;
            } elseif ($row->office) {
                $assignedTo = $row->office->name;
            } elseif ($row->court) {
                $assignedTo = $row->court->name;
            }

            return [
                $row->asset_name,
                $row->category->name ?? 'N/A',
                $row->region->name ?? 'N/A',
                $row->court->name ?? 'N/A',
                ucfirst($row->status),
                ucfirst($row->condition),
                $assignedTo
            ];
        } elseif ($reportType === 'dts') {
            return [
                $row->name,
                $row->court->name,
                $row->court->region->name ?? 'N/A',
                $row->monitors_count,
                $row->splitters_count,
                $row->hdmi_short_cables_count,
                $row->hdmi_long_cables_count,
                $row->extension_boards_count,
                $row->trucking_count,
                $row->sony_recorders_count,
                $row->is_available ? 'Available' : 'Unavailable'
            ];
        }

        return [];
    }

    public function title(): string
    {
        return ucfirst($this->reportData['reportType']) . ' Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}