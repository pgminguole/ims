<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['role', 'location', 'registry', 'inviter'])
                    ->withCount(['assignedAssets']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['role_id'])) {
            $query->where('role_id', $this->filters['role_id']);
        }

        if (!empty($this->filters['location_id'])) {
            $query->where('location_id', $this->filters['location_id']);
        }

        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Role',
            'Status',
            'Location',
            'Registry',
            'Assigned Assets',
            'Asset Requests',
            'Invited By',
            'Last Login',
            'Created At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            $user->role ? $user->role->name : 'N/A',
            ucfirst($user->status),
            $user->location ? $user->location->name : 'N/A',
            $user->registry ? $user->registry->name : 'N/A',
            $user->assigned_assets_count,
            $user->asset_requests_count,
            $user->inviter ? $user->inviter->name : 'N/A',
            $user->login_at ? $user->login_at->format('Y-m-d H:i') : 'Never',
            $user->created_at->format('Y-m-d H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:L' => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 25, // Name
            'C' => 30, // Email
            'D' => 15, // Phone
            'E' => 20, // Role
            'F' => 12, // Status
            'G' => 20, // Location
            'H' => 20, // Registry
            'I' => 15, // Assigned Assets
            'J' => 15, // Asset Requests
            'K' => 20, // Invited By
            'L' => 18, // Last Login
            'M' => 18, // Created At
        ];
    }
}