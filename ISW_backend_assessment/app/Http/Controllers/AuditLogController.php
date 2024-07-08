<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page_size = (isset($request->page_size) ? $request->page_size : 10);
        $page_num = (isset($request->page_num) ? $request->page_num : 1);
        $sort_by = (isset($request->sort_by) ? $request->sort_by : 'id');
        $sort_type = (isset($request->sort_type) ? $request->sort_type : 'ASC');
        //
        $auditLogs = AuditLog::where(function ($query) use ($request) {
            if(!empty($request->action))
                $query->whereRaw('action LIKE %'. $request->action .'%');
            if(!empty($request->username))
                $query->whereRaw('username LIKE %'. $request->username .'%');
        })
            ->orderBy($sort_by, $sort_type)
            ->paginate($page_size, ['*'], 'page', $page_num);

        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'data'=> $auditLogs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        //
        $auditLog = new AuditLog();
            $auditLog->fill($request->all());
            $auditLog->save();
    }
}
