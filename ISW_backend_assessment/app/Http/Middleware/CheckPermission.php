<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, $permission): JsonResponse
    {
        if (Auth::check()) {
            $role_id = Auth::user()->role->id;
            $hasPermission = DB::select(
                'SELECT r.* FROM permission_role r
                        LEFT JOIN permissions p ON p.id = r.permission_id
                        WHERE r.role_id = ? AND p.code_name = ? LIMIT 1',
                [$role_id, $permission]
            );
            if(!empty($hasPermission)) return $next($request);

        }

        return response()->json([
            'code' => '403',
            'status' => "error",
            'message' => "Not permitted to view this resource"], 403);
    }
}
