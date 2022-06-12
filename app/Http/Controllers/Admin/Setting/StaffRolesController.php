<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class StaffRolesController extends Controller
{
    private Collection $roles, $permissions;

    public function __construct()
    {
        $this->roles = collect(json_decode(file_get_contents(storage_path('json/staff/roles.json')), true));
        $this->permissions = collect(json_decode(file_get_contents(storage_path('json/staff/permissions.json')), true));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->roles->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = $this->roles->last();

        $this->roles->add([
            'id' => $role['id'] + 1,
            'name' => $request->name,
            'permissions' => $request->permissions,
            'editable' => true,
            'deleteable' => true
        ]);

        File::put(storage_path('json/staff/roles.json'), $this->roles->toJson(JSON_PRETTY_PRINT));

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been created.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->roles->firstWhere('id', '=', $id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $roles = $this->roles->map(function ($role) use($request, $id) {
            if ($role['id'] == $id) {
                $role['name'] = $request->name;
                $role['permissions'] = $request->permissions;
            }

            return $role;
        });

        File::put(storage_path('json/staff/roles.json'), $roles->toJson(JSON_PRETTY_PRINT));

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $roles = $this->roles->filter(function ($role) use ($id) {
            return $role['id'] != $id;
        });

        File::put(storage_path('json/staff/roles.json'), $roles->toJson(JSON_PRETTY_PRINT));

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been deleted.'
        ]);
    }
}
