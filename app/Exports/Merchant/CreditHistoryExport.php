<?php

namespace App\Exports\Merchant;

use App\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Platform\Models\History;

class CreditHistoryExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    private User $user;

    public function __construct()
    {
        $this->user = User::query()->find(Auth::id());
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return History::withoutGlobalScopes()
            ->with([
                'customer:id,name',
                'campaign:id,name',
                'staff:id,name'
            ])
            ->where('created_by', $this->user->id)
            ->whereNull('reward_id')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Website',
            'Customer',
            'Event',
            'Staff',
            'Point',
            'Date'
        ];
    }

    public function map($row): array
    {
        return [
            $row->campaign->name,
            $row->customer->name,
            $row->event,
            ($row->staff) ? $row->staff->name : null,
            $row->points,
            $row->created_at->format('M d, Y H:i A')
        ];
    }
}
