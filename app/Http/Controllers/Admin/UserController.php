<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $page = $request->input('page', 1);
        $search = $request->input('search', null);

        $users = User::withoutGlobalScopes()
            ->whereIn('role', [1, 2])
            ->when($search, function ($query) use($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate($itemsPerPage, ['id', 'name', 'role', 'email'], 'page', $page);

        return AdminUserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->only(['role', 'name', 'email', 'password']),
            [
                'role' => 'required',
                'name' => 'required',
                'email' => 'required|email:rfc,dns,spoof|unique:users,email',
                'password' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = new User;

            $user->role = $request->role;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->language = 'en';
            $user->locale = 'en';
            $user->currency_code = 'TTD';
            $user->timezone = env('APP_TIMEZONE');

            $user->save();

            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Admin has been created.'
            ]);
        }  catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return new AdminUserResource(User::query()->find($id));
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->only(['role', 'name', 'email']),
            [
                'role' => 'required',
                'name' => 'required',
                'email' => [
                    'required',
                    'email:rfc,dns,spoof',
                    Rule::unique('users', 'email')->ignore($id, 'id')
                ]
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ]);
        }

        DB::beginTransaction();

        try {
            $user = User::withoutGlobalScopes()->findOrFail($id);

            $user->role = $request->role;
            $user->name = $request->name;
            $user->email = $request->email;

            if (! empty($request->password)) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data admin has been updated.'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $currentAuth = Auth::id();
        $account = app()->make('account');

        if ($currentAuth == $id && $account->id == $id) {
            return response()->json([
                'status' => 'error',
                'message' => "This account admin can't be deleted."
            ]);
        }

        try {
            $user = User::withoutGlobalScopes()->findOrFail($id);

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data admin has been deleted.'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }
}
