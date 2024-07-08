<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function addComment(Request $request, string $postUuid): JsonResponse
    {

        $post = Post::where('uuid', $postUuid)
            ->first();
        if(empty($post))
            return response()->json([
                'code' => '404',
                'status' => "error",
                'message' => 'Post not found'], 404);

        if(!empty($request->commentUuid)){
            $comment = Comment::where('uuid', $request->commentUuid)->first();
            if(empty($comment))
                return response()->json([
                    'code' => '404',
                    'status' => "error",
                    'message' => 'Comment not found'], 404);
        } else {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string',
                'attachment' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => $validator->messages()], 403);
            }
            $comment = new Comment();
            $comment->user_id = Auth::user()->id;
        }
        if(!empty($request->comment)) $comment->comment = $request->comment;
        if(!empty($request->attachment)) $comment->attachment = self::saveImage('/attachment', $request->attachment);
        if($post->comments()->save($comment)){
            $audit = new AuditLog();
            $audit->request = json_encode($request->except('attachment'));
            $audit->action = (!empty($uuid))? "UPDATE_COMMENT": "ADD_COMMENT";
            $audit->username = Auth::user()->username;
            $audit->saveAudit($audit);
            return response()->json([
                'code'=> '200',
                'status'=> 'success',
                'data' => $comment
            ]);
        }
        return response()->json([
            'code'=> '500',
            'status'=> 'error',
            'message'=> 'Comment not saved. An error occurred. Please try again'
        ]);
    }

    public function fetchComments(string $postUuid): JsonResponse
    {
        $company_id = Auth::user()->company_id;
        $post = Post::where('uuid', $postUuid)
            ->first();
        if(!$post)
            return response()->json([
                'code' => '404',
                'status' => "error",
                'message' => 'Post not found'], 404);

        $comments = $post->comments;
        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'data' => $comments
        ]);
    }

    public function deleteComment(string $postUuid, string $commentUuid): JsonResponse
    {
        $comment = Comment::where('uuid', $commentUuid)->first();
        $comment->delete();
        $audit = new AuditLog();
        $audit->request = json_encode(['taskUuid'=>$postUuid, 'comment'=>$commentUuid]);
        $audit->action = "COMMENT_DELETED";
        $audit->saveAudit($audit);
        return response()->json([
            'code'=> '200',
            'status'=> 'success'
        ]);
    }

}
