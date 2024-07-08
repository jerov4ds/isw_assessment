<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page_size = ($request->page_size ?? 10);
        $page_num = ($request->page_num ?? 1);
        $sort_by = ($request->sort_by ?? 'created_at');
        $sort_type = ($request->sort_type ?? 'DESC');

        $posts = Post::where(function ($query) use ($request) {
                if(!empty($request->title))
                    $query->where('title', 'LIKE', '%'.$request->title.'%');
                if(!empty($request->user)){
                    $user = User::where('uuid', $request->user)->first();
                    $query->where('user_id', $user->id);
                }
                if(!empty($request->from) && !empty($request->to))
                    $query->whereBetween('created_at', date('Y-m-d', strtotime($request->from)). " 00:00:00",  date('Y-m-d', strtotime($request->to))." 23:59:59");
                elseif (!empty($request->from))
                    $query->where('created_at', '>=', date('Y-m-d', strtotime($request->from)). " 00:00:00");
                elseif (!empty($request->to))
                    $query->where('created_at', '<=', date('Y-m-d', strtotime($request->to)). " 23:59:59");
            })
            ->with(['user'])
            ->orderBy($sort_by, $sort_type)
            ->paginate($page_size, ['*'], 'page', $page_num);

        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'data'=> $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function view($uuid = null): JsonResponse
    {
        if(!$uuid){
            return response()->json([
                'code'=>400,
                'status'=>'error',
                'message'=>'Please provide project unique id'
            ], 400);
        }
        $post = Post::where('uuid', $uuid)
            ->with('user', 'comments')->first();
        if(empty($post)){
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>'Post not found'
            ], 404);
        }
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'data'=> $post
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param null $uuid
     * @return JsonResponse
     */
    public function store(Request $request, $uuid = null): JsonResponse
    {
        if(!empty($uuid)){
            $post = Post::where('uuid', $uuid)
                ->first();
            if(!$post)
                return response()->json([
                    'code'=>400,
                    'status'=>'error',
                    'message'=>'Post not found'
                ], 400);
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'text' => 'required|string',
                'images' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => $validator->messages()], 403);
            }
            $post = new Post();
            $post->user_id = Auth::user()->id;
        }
        if(!empty($request->title)) {
            $post->title = $request->title;
        }

        if(!empty($request->text)) $post->text = $request->text;
        if(!empty($request->image)) $post->image = self::saveImage('projects/', $request->image);
        $post->save();

        $audit = new AuditLog();
        $audit->request = json_encode($request->except('images'));
        $audit->action = (!empty($uuid))? "UPDATE_POST": "CREATE_POST";
        $audit->username = Auth::user()->username;
        $audit->saveAudit($audit);

        return response()->json([
            'code'=>201,
            'status'=>'success',
            'data'=>$post
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        if(!$uuid){
            return response()->json([
                'code'=>400,
                'status'=>'error',
                'message'=>'Please provide project unique id'
            ], 400);
        }
        $post = Post::where('uuid', $uuid)
            ->first();
        if(empty($post)){
            return response()->json([
                'code'=>404,
                'status'=>'error',
                'message'=>'Post not found'
            ], 404);
        }
        $post->delete();
        $audit = new AuditLog();
        $audit->request = $uuid;
        $audit->action = "POST_DELETED";
        $audit->saveAudit($audit);
        return response()->json([
            'code'=> '200',
            'status'=> 'success'
        ]);
    }
}
