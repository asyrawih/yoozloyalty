<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Platform\Models\Reward;

class RewardImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private User $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            $reward = new Reward;
            $reward->title = $item['title'];
            $reward->points_cost = $item['points_to_redeem'];
            $reward->reward_value = $item['reward_value'] * 100;
            $reward->customer_types = [1, 2, 3, 4];
            $reward->requires_validation = $item['requires_validation'];
            $reward->delivery_by_coupun = $item['delivery_by_coupon'];
            $reward->multiple_time = $item['multiple_time'];
            $reward->active = 1;
            $reward->active_from = Carbon::parse($item['active_from']);
            $reward->expires_at = Carbon::parse($item['expires_at']);
            $reward->active_monday = 1;
            $reward->active_tuesday = 1;
            $reward->active_wednesday = 1;
            $reward->active_thursday = 1;
            $reward->active_friday = 1;
            $reward->active_saturday = 1;
            $reward->active_sunday = 1;
            $reward->created_by = $this->user->id;
            $reward->updated_by = $this->user->id;
            $reward->save();
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 250;
    }
}
