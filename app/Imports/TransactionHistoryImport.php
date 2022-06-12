<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Platform\Models\Campaign;
use Platform\Models\History;

class TransactionHistoryImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $campaign = Campaign::withoutGlobalScopes()
            ->whereUuid($this->uuid)
            ->first();

        foreach ($collection as $item) {
            if (empty($item['identifier'])) {
                $customer = Customer::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('customer_number', $item['customer_number'])
                    ->first();

                if ($customer) {
                    $history = new History;

                    $history->customer_id = $customer->id;
                    $history->campaign_id = $campaign->id;
                    $history->points = $item['amount'];
                    $history->bill_number = "00000";
                    $history->bill_amount = abs($item['amount']);

                    if ((int) $item['amount'] > 0) {
                        $history->event = 'Credited by merchant';
                        $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
                    } else {
                        $history->reward_title = $item['reason'];
                        $history->event = 'Redeemed with customer number';

                        $this->assignPointsUsage(abs($item['amount']), $customer->id);
                    }

                    $history->created_by = $campaign->created_by;

                    $history->save();
                }
            } else if (! History::query()->where('bill_number', $item['identifier'])->first()) {
                $customer = Customer::query()
                    ->where('campaign_id', $campaign->id)
                    ->where('customer_number', $item['customer_number'])
                    ->first();

                if ($customer) {
                    $history = new History;

                    $history->customer_id = $customer->id;
                    $history->campaign_id = $campaign->id;
                    $history->points = $item['amount'];
                    $history->bill_number = $item['identifier'];
                    $history->bill_amount = abs($item['amount']);

                    if ((int) $item['amount'] > 0) {
                        $history->event = 'Credited by merchant';
                        $history->points_expired_date = Carbon::now()->addDays($campaign->user->points_expiry->points_expiry ?? config('system.default_points_expiry'));
                    } else {
                        $history->reward_title = $item['reason'];
                        $history->event = 'Redeemed with customer number';

                        $this->assignPointsUsage(abs($item['amount']), $customer->id);
                    }

                    $history->created_by = $campaign->created_by;

                    $history->save();
                }
            }
        }
    }

    private function assignPointsUsage($points_cost, $customer_id)
    {
        $remaining_cost = $points_cost;

        $histories = History::query()->whereRaw('(points - points_usage) > 0')
            ->where('reward_id', NULL)
            ->where('customer_id', $customer_id)
            ->where('points_expired_date', '>', Carbon::now())
            ->orderBy('points_expired_date', 'ASC')
            ->get();

        foreach($histories as $history) {
            if ($remaining_cost <= 0) {
                break;
            }

            $remaining_points = $history->points - $history->points_usage;

            if ($remaining_points > 0) {
                // If there is still remaining points
                if ($remaining_points < $remaining_cost) {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_points + $history->points_usage
                        ]);

                    $remaining_cost -= $remaining_points;
                } else {
                    History::query()
                        ->where('id', $history->id)
                        ->update([
                            'points_usage' => $remaining_cost + $history->points_usage
                        ]);

                    $remaining_cost = 0;
                }
            }
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
