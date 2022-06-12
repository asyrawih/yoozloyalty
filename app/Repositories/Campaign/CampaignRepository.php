<?php

namespace App\Repositories\Campaign;

use App\Repositories\ApiRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Platform\Models\Campaign;

class CampaignRepository extends ApiRepository
{
    public function __construct(Campaign $model)
    {
        parent::__construct($model);
    }

    public function readUuid(string|NULL $uuid = NULL): ?Model
    {
        try {
            $campaign = $this->model->withoutGlobalScopes()
                ->whereUuid($uuid)
                ->firstOrFail();

            return $campaign;
        } catch(Exception $exception) {
            throw $exception;
        }
    }
}
