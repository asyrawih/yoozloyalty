<?php

namespace App\Exports\Merchant;

use App\User;
use Platform\Models\History;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RewardHistoryExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping
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
                'staff:id,name',
                'reward:id,title'
            ])
            ->where('created_by', $this->user->id)
            ->whereNotNull('reward_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Website',
            'Customer',
            'Reward',
            'Cost',
            'Event',
            'Staff',
            'Date'
        ];
    }

    public function map($row): array
    {
        return [
            $row->campaign->name,
            $row->customer->name,
            $row->reward->title,
            abs((int) $row->points),
            $row->event,
            ($row->staff) ? $row->staff->name : null,
            $row->created_at->format('M d, Y H:i A')
        ];
    }
}
