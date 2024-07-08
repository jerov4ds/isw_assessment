<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $page_size = (isset($request->page_size) ? $request->page_size : 10);
        $page_num = (isset($request->page_num) ? $request->page_num : 1);
        $sort_by = (isset($request->sort_by) ? $request->sort_by : 'id');
        $sort_type = (isset($request->sort_type) ? $request->sort_type : 'ASC');
        $permission = (isset($request->permission) ? $request->permission : '');

        $permissions = Permission::where(function ($query) use ($permission) {
            if(!empty($permission))
                $query->whereRaw('code_name LIKE %'.$permission.'%');
            })
            ->orderBy($sort_by, $sort_type)
            ->paginate($page_size, ['*'], 'page', $page_num);

        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'data'=> $permissions
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request, $uuid = null): JsonResponse
    {
        if(Auth::user()->role->title != 'admin'){
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => "Not permitted to view this resource"], 403);
        }
        if(!empty($uuid)){
            $permission = Permission::where('uuid', $uuid)->first();
            if(empty($permission)){
                return response()->json([
                    'code'=> '404',
                    'status'=> 'error',
                    'message' => 'Permission not found'
                ]);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'code_name' => 'required|string|unique:permissions',
                'display_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => $validator->messages()], 403);
            }
            $permission = new Permission();
        }
        try {
            if(!empty($request->code_name)) $permission->code_name = $request->code_name;
            if(!empty($request->display_name)) $permission->display_name = $request->display_name;
            $permission->save();
            $audit = new AuditLog();
            $audit->request = json_encode($request);
            $audit->action = "CREATE_PERMISSION";
            $audit->saveAudit($audit);
            return response()->json([
                'code'=> '200',
                'status'=> 'success',
                'data'=> $permission
            ]);
        } catch (Exception $exception){
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function assignPermissionsToAdmin(): JsonResponse
    {
        if(Auth::user()->role->title != 'admin'){
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => "Not permitted to view this resource"], 403);
        }
        //$excluded = ['create_role', 'create_user', 'create_project'];
        $permissions = Permission::get();
        $role = Role::where('title', 'owner')->first();
        foreach ($permissions as $permission){
            $permission->roles()->attach(Auth::user()->role->id);
            $permission->roles()->attach($role->id);
        }
        return response()->json([
            'code'=> '200',
            'status'=> 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     */
    public function destroy($uuid = null): JsonResponse
    {
        if(Auth::user()->role->title != 'admin'){
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => "Not permitted to view this resource"], 403);
        }
        $permission = Permission::where('uuid', $uuid)->first();
        if(empty($permission)){
            return response()->json([
                'code'=> '404',
                'status'=> 'error',
                'message' => 'Permission not found'
            ]);
        }
        $permission->delete();
        $audit = new AuditLog();
        $audit->request = $uuid;
        $audit->action = "DELETE_PERMISSION";
        $audit->saveAudit($audit);
        return response()->json([
            'code'=> '200',
            'status'=> 'success'
        ]);
    }
}
