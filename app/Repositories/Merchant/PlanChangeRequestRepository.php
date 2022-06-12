<?php

namespace App\Repositories\Merchant;

use App\Repositories\BaseRepository;
use App\User;
use App\Models\BillingInvoice;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Crypto;
use App\Http\Resources\PlanChangeRequestHistoryResource;

class PlanChangeRequestRepository extends BaseRepository
{

    private Crypto $cryptoJS;

    protected User $user;

    public function __construct(BillingInvoice $model)
    {
        parent::__construct($model);

        $this->cryptoJS = new Crypto();

        $this->user = User::query()->find(Auth::id());
    }

    /**
     * Get merchant plan change request history
     *
     * @return void
     */
    public function history(
        array $options = array(),
    ) {
        $orders = $this->model->query()
            ->where('user_id', $this->user->id)
            ->orderByDesc('created_at')
            ->paginate($options['itemsPerPage'], ['*'], 'page', $options['page']);

        return PlanChangeRequestHistoryResource::collection($orders);
    }


}