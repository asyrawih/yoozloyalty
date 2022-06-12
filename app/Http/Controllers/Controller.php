<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function apiResponse($request)
    {

        $data = [];

        if($request['error']) {

            $message = 'Error unknown';
            $responseCode = 422;
            $errors = null;
            $isRedirect = false;

            if(isset($request['data'])) {
                $data = $request['data'];
            }

            if(isset($request['message'])) {
                $message = $request['message'];
            }

            if(isset($request['code'])) {
                $responseCode = $request['code'];
            }

            if(isset($request['isRedirect'])) {
                $isRedirect = $request['isRedirect'];
            }

            if(isset($request['errors'])) {
                $errors = $request['errors'];
            }

            return $this->errorResponse($message, $data, $responseCode, $errors, $isRedirect);

        }

        if(isset($request['data'])) {
            $data = $request['data'];
        }

        $isRedirect = false;
        if(isset($request['isRedirect'])) {
            $isRedirect = $request['isRedirect'];
        }

        $message = 'Success.';
        if(isset($request['message'])) {
            if($request['message'] != null) {
                $message = $request['message'];
            }
        }

        // \DB::commit();
        return $this->successResponse($message, $data, $isRedirect);
    }

    /*
    * Success response
    */
    public function successResponse($message = null, $data = [], $isRedirect = false)
    {
        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    /*
    * Error response
    */
    public function errorResponse($message = null, $data = [], $code = 422, $errors = null, $isRedirect = false)
    {

        return response()->json([
            'code' => $code,
            'success' => false,
            'is_redirect' => $isRedirect,
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
        ], $code);
    }
}
