<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SmsApiTrait;
use App\Repositories\{
    SmsTemplateRepository
};
use App\Http\Requests\SmsTemplate\{
    StoreRequest,
    UpdateRequest,
    TestRequest
};
use Illuminate\Support\Facades\Hash;

class SmsTemplateController extends Controller
{

    use SmsApiTrait;

    private $smsTemplate;

    public function __construct(SmsTemplateRepository $smsTemplate)
    {
        $this->smsTemplate = $smsTemplate;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->apiResponse(
            $this->smsTemplate->index(auth()->user()->id)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
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

        return $this->apiResponse(
            $this->smsTemplate->store($request, auth()->user()->id)
        );
    }
}
