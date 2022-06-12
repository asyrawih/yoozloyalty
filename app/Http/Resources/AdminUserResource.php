<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AdminUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $currentAuth = Auth::id();
        $account = app()->make('account');

        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'deleteable' => ! ($this->id == $currentAuth || $this->id == $account->id),
            'editable' => ! ($this->id == $account->id),
        ];
    }
}
