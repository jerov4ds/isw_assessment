<?php

namespace App\Http\Controllers;

use app\Helpers\Helper;
use App\Mail\SendResetPasswordMail;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'country' => 'required|string',
            'state' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => $validator->messages()], 403);
        }

        $role = Role::where('title', 'admin')->first();
        $user = new User();
        $user->name = $request->input('name');
        $user->mobile = $request->input('mobile');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->role_id = $role->id;
        $user->password = bcrypt($request->password);
        $user->status = 'active';
        $user->save();

        $token = JWTAuth::fromUser($user);
        $audit = new AuditLog();
        $audit->request = json_encode($request);
        $audit->action = "REGISTER_USER";
        $audit->username = $user->email;
        $audit->saveAudit($audit);

        return response()->json([

            "code"=>200,
            "status"=>"Success",
            "data"=>compact('user', 'token'),
        ], 200);

    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'invalid credentials',
                    'code' => 400,
                    'status'=> 'error'
                ], 400);
            }
            $user = User::where('email', $request->email)->with('role.permissions')->first();
            if($user->status == 'pending'){
                $user->status = 'active';
                $user->save();
            }

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Oh snag! Something went wrong. We are fixing it.',
                'code' => 500,
                'status'=> 'error'
            ], 500);
        }

        $audit = new AuditLog();
        $audit->request = json_encode($request->except('password'));
        $audit->action = "USER_LOGIN";
        $audit->username = $user->email;
        $audit->saveAudit($audit);

        return response()->json([
            "code"=>200,
            "status"=>"Success",
            "data"=>compact( 'user', 'token'),
        ]);

    }

    public function getAuthenticatedUser(): JsonResponse
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $logged = User::with(['role.permissions'])->find($user->id);

        return response()->json(compact('logged'));
    }


    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'mobile' => 'string',
            'profile_image' => 'string',
            'email' => 'string|email|max:255',
            'password' => 'string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => $validator->messages()], 403);
        }
        $user = Auth::user();
        if(!empty($request->name)) {
            //check duplicate user name
            $duplicateName = User::where('name', $request->name)->first();
            if(!empty($duplicateName)){
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => "User with name exists previously"], 403);
            }
            $user->name = $request->name;
        }

        if(!empty($request->email)) {
            //check duplicate user email
            $duplicateEmail = User::where('email', $request->email)->first();
            if(!empty($duplicateEmail)){
                return response()->json([
                    'code' => '403',
                    'status' => "error",
                    'message' => "User with email exists previously"], 403);
            }
            $user->email = $request->email;
            if($user->role->title == 'owner')
                $user->email = $request->email;
        }
        if(!empty($request->mobile)) $user->mobile = $request->mobile;
        if(!empty($request->profile_image)) {
            $user->profile_image = self::saveImage('profiles/', $request->profile_image);
        }
        if($request->password) $user->password = bcrypt($request->password);
        $user->save();

        $audit = new AuditLog();
        $audit->request = json_encode($request->except('password', 'profile_image'));
        $audit->action = "UPDATE_PROFILE";
        $audit->username = $user->email;
        $audit->saveAudit($audit);

        return response()->json([
            "code"=>200,
            "status"=>"Success",
            "data"=>compact( 'user'),
        ]);
    }

    public function requestReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => $validator->messages()], 403);
        }

        $user = User::where('email', $request->email)->first();
        $otp = rand(000000, 999999);
        $user->otp = $otp;
        $user->otp_sent = now();
        $user->save();
        try {
            $details = [
                'name' => $user->name,
                'otp' => $otp,
                'url' => env('APP_URL') . '/reset'
            ];

            Mail::to($user->email)->send(new SendResetPasswordMail($details));
            $audit = new AuditLog();
            $audit->request = json_encode($request);
            $audit->action = "PASSWORD_RESET_REQUEST";
            $audit->username = $user->email;
            $audit->saveAudit($audit);

            return response()->json([
                "code"=>200,
                "status"=>"Success",
            ]);
        } catch (\Exception $ex) {
            Log::info($ex);
            return response()->json([
                "code"=>500,
                "status"=>"error",
                "message"=>$ex->getMessage()
            ]);
        }
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => '403',
                'status' => "error",
                'message' => $validator->messages()], 403);
        }

        $user = User::where('email', $request->email)->first();
        if(!$user)
            return response()->json([
                'code' => '400',
                'status' => "error",
                'message' => "Invalid user"], 400);
        if($user->otp != $request->otp) {
            return response()->json([
                'code' => '400',
                'status' => "error",
                'message' => "OTP is incorrect"], 400);
        }
//        $otpSentTime = $user->otp_sent;

        $otpSentTime = Carbon::createFromTimeString($user->otp_sent);
        // Add 30 minutes to the OTP sent time
        $expirationTime = $otpSentTime->addMinutes(30);

        // Get the current time
        $currentTime = Carbon::now();

        // Check if the current time is before the expiration time
        if ($currentTime->greaterThan($expirationTime))
            return response()->json([
                'code' => '400',
                'status' => "error",
                'message' => "OTP expired. Please request again"], 400);
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            "code"=>200,
            "status"=>"Success",
        ]);
    }
}

