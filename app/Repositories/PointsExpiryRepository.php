<?php

namespace App\Repositories;

use App\Models\PointsExpiry;
use App\Traits\ExceptionHandlingTrait;
use Exception;

class PointsExpiryRepository extends BaseRepository
{
    use ExceptionHandlingTrait;
    /**
     * PointsExpiryRepository constructor.
     *
     * @param PointsExpiry $model
     */
    public function __construct(PointsExpiry $model)
    {
        parent::__construct($model);
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PointsExpiry::class;
    }

    public function index($userId)
    {
        try {
            $points_expiry = $this->model()::where('merchant_user_id', $userId)->firstOrFail();

            $data = $points_expiry;

            return [
                'error' => false,
                'message' => 'Points Expiry Setting.',
                'data' => $data,

            ];
        } catch (\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

    public function updateOrCreate($user_id)
    {
        try {
            $this->model()::updateOrCreate(
                [
                    'merchant_user_id' => $user_id,
                ],
                [
                    'points_expiry' => config('system.default_points_expiry'),
                ]
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function update($request, $userId)
    {
        try {
            $points_expiry = $this->model()::updateOrCreate(
                //check exists merchants user id
                [
                    'merchant_user_id' => $userId
                ],
                //field to be updated.
                [
                    'points_expiry' => $request->points_expiry
                ]
            );

            // TODO:
            // search for
            // - Unused points in history table (usage < points); and
            // - Where the created_at is more than `points_expiry` days ago.
            // then update the status to `E` (Expired).

            if ($points_expiry) {

                \Illuminate\Support\Facades\Log::debug(
                    sprintf(
                        "User %d changed points expiry to %d days.",
                        $userId,
                        $points_expiry->points_expiry
                    )
                );

                return [
                    'code' => 200,
                    'success' => true,
                    'message' => 'Data has been updated.'
                ];
            }

            return [
                'error' => true,
                'message' => 'Error update.'
            ];
        } catch (\Exception $e) {
            return $this->showException($e->getMessage());
        }
    }

}
