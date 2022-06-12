<?php

namespace App\Exports\Merchant;

use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Platform\Models\Reward;

class RewardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private User $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Reward::query()->where('created_by', $this->user->id)->get();
    }

    public function headings(): array
    {
        return [
            'Offer Name',
            'Reward Value',
            'Of Points',
            'Redemptions',
            'Requires Validation',
            'Delivery by coupon',
            'Multiple Time',
            'Active',
        ];
    }

    public function map($row): array
    {
        $prefix =  $this->user->getCurrencyFormat('symbol');
        $fraction_digits = $this->user->getCurrencyFormat('fraction_digits');
        $multiplier = intval(str_pad(1, $fraction_digits + 1, 0));
        $reward_value = (is_numeric($row->reward_value)) ? ($row->reward_value/ $multiplier) : $row->reward_value;

        return [
            $row->title,
            "{$prefix} {$reward_value}",
            $row->points_cost,
            $row->number_of_times_redeemed,
            $row->validation_required ? 'Yes' : 'No',
            $row->delivery_by_coupon ? 'Yes' : 'No',
            $row->multiple_time ? 'Yes' : 'No',
            $row->active ? 'Yes' : 'No',
        ];
    }
}
