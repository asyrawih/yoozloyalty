<?php

namespace App\Http\Controllers\Merchant\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Traits\SmsApiTrait;
use App\Repositories\PointsExpiryRepository;
use App\Http\Requests\PointsExpiry\UpdateRequest;
use Illuminate\Support\Facades\Hash;

class PointsExpiryController extends Controller
{

    // use SmsApiTrait;

    private $pointsExpiry;

    public function __construct(PointsExpiryRepository $pointsExpiry)
    {
        $this->pointsExpiry = $pointsExpiry;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->apiResponse(
            $this->pointsExpiry->index(auth()->user()->id)
        );
    }

    /**
     * Update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        // Verify password
        if (
            !Hash::check($request->current_password, auth()->user()->password)
            ) 
        {
            return $this->apiResponse([
                'error' => true,
                'code' => 422,
                'message' => trans('app.current_password_incorrect'),
                'errors' => [
                    'current_password' => [
                        trans('app.current_password_incorrect')
                    ]
                ]
            ]);
        }
        return $this->pointsExpiry->update($request, auth()->user()->id);

        return $this->apiResponse(
            $this->pointsExpiry->update($request, auth()->user()->id)
        );
    }
}
