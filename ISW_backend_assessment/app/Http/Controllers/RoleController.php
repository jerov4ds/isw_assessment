<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/v1/roles",
     *     operationId="getRoles",
     *     tags={"Roles"},
     *     summary="Get list of roles",
     *     description="Returns list of roles with pagination",
     *     @OA\Parameter( name="page_size", in="query", required=false, @OA\Schema(type="integer"), description="Page size for pagination"),
     *     @OA\Parameter( name="page_number", in="query", required=false, @OA\Schema(type="integer"), description="Page number for pagination"),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string"), description="Field to sort query by. E.G. code_name" ),
     *     @OA\Parameter(name="sort_type", in="query", required=false, @OA\Schema(type="string"), description="ASC or DESC." ),
     *     @OA\Parameter(name="title", in="query", required=false, @OA\Schema(type="string"), description="Fileter roles by title" ),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string"), description="Fileter roles by status" ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles listed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ApiPaginationResponse", @OA\Property(property="data", ref="#/components/schemas/Pagination"))
     *     ),
     *     @OA\Response(response=500, description="Server error"),
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $company_id = Auth::user()->company_id;
        $page_size = (isset($request->page_size) ? $request->page_size : 10);
        $page_num = (isset($request->page_num) ? $request->page_num : 1);
        $sort_by = (isset($request->sort_by) ? $request->sort_by : 'id');
        $sort_type = (isset($request->sort_type) ? $request->sort_type : 'ASC');

        $roles = Role::where(function ($query) use ($request) {
            if(!empty($request->title))
                $query->whereRaw("title LIKE '%".$request->title."%'");
            if(!empty($request->status))
                $query->whereRaw("status LIKE '%".$request->status."%'");
            if(!empty($request->from) && !empty($request->to))
                $query->whereBetween('created_at', date('Y-m-d', strtotime($request->from)). " 00:00:00",  date('Y-m-d', strtotime($request->to))." 23:59:59");
            elseif (!empty($request->from))
                $query->where('created_at', '>=', date('Y-m-d', strtotime($request->from)). " 00:00:00");
            elseif (!empty($request->to))
                $query->where('created_at', '<=', date('Y-m-d', strtotime($request->to)). " 23:59:59");
        })
            ->where(function ($query) use ($company_id){
                if($company_id != null){
                    $query->where('company_id', $company_id);
                }
            })
            ->with('supervisor', 'createdBy')
            ->withCount('users')
            ->orderBy($sort_by, $sort_type)
            ->paginate($page_size, ['*'], 'page', $page_num);

        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'data'=> $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, string $uuid = null): JsonResponse
    {
        if(!empty($uuid)){
            $role = Role::where('uuid', $uuid)->first();
            if(empty($role)){
                return response()->json([
                    'code'=> '404',
                    'status'=> 'error',
                    'message' => 'Role not found'
                ]);
            }
            if(!empty($request->reporting_to)) {
                $reporting = User::where('uuid', $request->reporting_to)->first();
                $role->reporting_to = $reporting->id;
            }
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'permissions' => 'required|array',
                'permissions.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => $validator->messages()], 403);
            }
            $existing_role = Role::where('title', $request->title)
                ->first();
            if(!empty($existing_role))
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => "Role with title " . $request->title . " already exists."], 403);
            $role = new Role();
            $role->created_by = Auth::user()->id;
            if(!empty($request->reporting_to)) {
                $reporting = User::where('uuid', $request->reporting_to)->first();
                $role->reporting_to = $reporting->id;
            } else {
                $role->reporting_to = Auth::user()->id;
            }
        }
        try {
            if(!empty($request->title)) $role->title = $request->title;
            $role->save();
            if(!empty($request->permissions)){
                foreach ($request->permissions as $perm){
                    $permission = Permission::where('code_name', $perm)->first();
                    if(!empty($permission)) $role->permissions()->attach($permission);
                }
            }
            $audit = new AuditLog();
            $audit->request = json_encode($request);
            $audit->action = (!empty($uuid))? "UPDATE_ROLE": "CREATE_ROLE";
            $audit->username = Auth::user()->username;
            $audit->saveAudit($audit);
            return response()->json([
                'code'=> '200',
                'status'=> 'success',
                'data'=> $role
            ]);
        } catch (Exception $exception){
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid)
    {
        if(!$uuid){
            return response()->json([
                'code'=>400,
                'status'=>'error',
                'message'=>'Please provide role unique id'
            ], 400);
        }
        $company_id = Auth::user()->company_id;
        $role = Role::where('uuid', $uuid)
            ->with('permissions', 'supervisor', 'createdBy', 'users')
            ->first();
        if(empty($role)){
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>'Role does not exist'
            ], 404);
        }
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'data'=> $role
        ]);
    }

    /**
     * Add or remove permissions from a role.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addOrRemovePermissions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string',
            'action' => 'required|string|in:add,remove',
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => $validator->messages()], 403);
        }
        $role = Role::where('uuid', $request->uuid)->first();
        if(empty($role)) {
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>'Role does not exist'
            ], 404);
        }
        foreach ($request->permissions as $perm){
            $permission = Permission::where('code_name', $perm)->first();
            if(!empty($permission)) {
                if ($request->action == 'add') {
                    $role->permissions()->attach($permission);
                } else {
                    $role->permissions()->detach($permission);
                }
            }
        }
        return \response()->json([
            'code'=>200,
            'status'=>'Success',
            'message'=>'Operation successful'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    /**
     * @OA\Delete(
     *     path="/api/v1/roles/{uuid}",
     *     operationId="deleteRole",
     *     tags={"Roles"},
     *     summary="Delete Single role",
     *     description="Deletes a single role",
     *     @OA\Parameter( name="uuid", in="path", required=true, @OA\Schema(type="string"), description="role uuid"),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully",
     *         @OA\Response(
     *          response=200,
     *          description="Role deleted successfully",
     *      ),
     *     ),
     *     @OA\Response(response=404, description="Not found error"),
     *     @OA\Response(response=500, description="Server error"),
     * )
     */
    public function destroy(string $uuid)
    {
        $role = Role::where('uuid', $uuid)->first();
        if(empty($role)){
            return response()->json([
                'code'=> '404',
                'status'=> 'error',
                'message' => 'Role not found'
            ]);
        }
        $role->delete();
        $audit = new AuditLog();
        $audit->request = $uuid;
        $audit->action = "DELETE_ROLE";
        $audit->saveAudit($audit);
        return response()->json([
            'code'=> '200',
            'status'=> 'success'
        ]);
    }
}
