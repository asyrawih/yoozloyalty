<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanChangeRequestHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'merchant' => $this->merchant_name,
            'previous_plan' => $this->previous_plan_name,
            'plan' => $this->plan_name,
            'payment_method' => $this->payment_method_formatted($this->payment_method),
            'amount' => $this->amount_formatted,
            'merchant_bank_name' => $this->merchant_bank_name,
            'merchant_identifier' => $this->merchant_identifier,
            'amount_paid' => $this->amount_paid_formatted,
            'paid_at' => $this->paid_at ? $this->paid_at->format('l, jS F Y') : null,
            'status' => $this->status_formatted($this->status),
            'created_at' => $this->created_at->format('l, jS F Y'),
            'is_confirm' => ($this->status === 'confirm'),
            'is_cheque' => ($this->payment_method === 'cheque'),
            'is_bank_transfer' => ($this->payment_method === 'bank_transfer'),
            'is_lynx' => ($this->payment_method === 'lynx')
        ];
    }

    private function status_formatted(string $selected): array
    {
        $options = array(
            'pending' => array('color' => 'orange', 'text' => 'Pending' ),
            'confirm' => array('color' => 'primary', 'text' => 'Confirm' ),
            'approved' => array('color' => 'success', 'text' => 'Approved' ),
            'rejected' => array('color' => 'error', 'text' => 'Rejected' ),
            'canceled' => array('color' => 'error', 'text' => 'Canceled')
        );

        return $options[$selected];
    }

    private function payment_method_formatted(string $selected): string
    {
        $options = array(
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'lynx' => 'Lynx',
            'yooz_pg' => 'Yooz Payment Gateway'
        );

        return $options[$selected];
    }
}
