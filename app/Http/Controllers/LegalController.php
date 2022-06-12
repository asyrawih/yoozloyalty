<?php

namespace App\Http\Controllers;

use App\Models\SettingLegal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Platform\Models\Campaign;

class LegalController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $uuid = $request->input('uuid');

        $query = SettingLegal::query();

        $legal = null;

        if (! Auth::check() && ! $uuid) {
            $query = $query->where('user_id', 1);
        } else {
            $campaign = Campaign::query()->whereUuid($uuid)->first();

            $userId = $campaign ? $campaign->created_by : Auth::id();

            $query = $query->where('user_id', $userId);
        }

        if ($type) {
            $legal = $query->where('type', $type)->first();
        } else {
            $legal = $query->get();
        }

        if(! $legal){
            return response()->json([
                'status' => 'error',
                'data' => [
                    'content' => "No $type page yet"
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $legal
        ]);
    }

    public function store(Request $request)
    {
        $user_id = Auth::id();
        $type = $request->input('type', null);
        $content = $request->input('content', null);

        $legal = SettingLegal::query()
            ->where('user_id', $user_id)
            ->where('type', $type)
            ->first();

        if (! $legal) {
            SettingLegal::query()->create(compact(
                'user_id',
                'type',
                'content'
            ));
        } else {
            $legal->update(compact('content'));
        }

        return response()->json([
            'status' => 'success'
        ]);
    }
}
