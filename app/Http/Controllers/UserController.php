<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Password_reset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    // public function __construct()
    // {
    //     if (Gate::allows('Onlyadmin', Auth::user())) {

    //     }    
    //     else if (Gate::denies('Onlyadmin', Auth::user())) {
    //         abort(403);
    //     }
    // }

    public function login(Request $request)
    {
        // VALIDATION
        $rules = array(
            "email" => ["required","email"],
            "password" => ["required","min:8","alpha_num"],
        );

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            // Detailed Invalid Data information
            // return response(['message' => $validator->errors()], 422);
            return response([ 'message' => "Invalid Data"], 422);
        }

        // Data Processing
        $user= User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response('These credentials do not match our records.',404);
        } else {

            $current_user = User::where('email', $request->email)->first();
            $current_role_id = $current_user->roles()->first()->id;
            $current_role = $current_user->roles()->first();
            $current_permissions = Role::where('id', $current_role_id)->with("permissions")->first()->permissions->pluck("slug");

            return response()->json(["user" => $current_user, "role" => $current_role, "permissions" => $current_permissions],200);
        }
    }
    
    public function forgotPassword(Request $request)
    {
        $rules = array(
            "email" => ["required","email","exists:users,email"]
        );

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            // Detailed Invalid Data information
            // return response(['message' => $validator->errors()], 422);
            return response(["Invalid Data"], 422);
        }

        $pr = Password_reset::where("email",$request->email)->first();

        if ($pr != null && $pr->count() > 0) {        
            $to = Carbon::createFromFormat('Y-m-d H:s:i', $pr->created_at);
            $from = Carbon::createFromFormat('Y-m-d H:s:i', date('Y-m-d H:s:i'));
            $diff_in_hours = $to->diffInHours($from);

            if ($diff_in_hours > 0) {
                $pr->delete();

                $reset_token = str_replace(".","0",Hash::make(Str::random(59)));
                $reset_token = str_replace("/","A",$reset_token);
                $reset_token = str_replace("\\","x",$reset_token);
                $email = $request->email;

                $pr = new Password_reset;
                $pr->email = $email;
                $pr->token = $reset_token;
                $pr->save(); 
            }
            else {
                $reset_token = $pr->token;
                $email = $request->email;
            }
        } else {
            $reset_token = str_replace(".","0",Hash::make(Str::random(59)));
            $reset_token = str_replace("/","A",$reset_token);
            $reset_token = str_replace("\\","x",$reset_token);
            $email = $request->email;

            $pr = new Password_reset;
            $pr->email = $email;
            $pr->token = $reset_token;
            $pr->save(); 
        }

        $reset_link = "http://localhost:8000/resetpassword". "/" . $reset_token;
        $details = ['link' => $reset_link];
        Mail::to($request->email)->send(new \App\Mail\MyTestMail($details));
       
        return response("reset link has been sent via Email",200);
    }

    public function emailVerification()
    {

    }

    public function resetPassword($token, Request $request)
    {
        // VALIDATION
        $rules = array(
            "password" => ["required","min:8"],
            "confirm_password" => ["required","min:8"]
        );

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            return response(["Invalid Data"], 422);
        }

        // Processing Data
        $pr = Password_reset::where("token",$token)->count();
        $prr = Password_reset::where("token",$token)->first();

        if ($pr > 0) {
            $to = Carbon::createFromFormat('Y-m-d H:s:i', $prr->created_at);
            $from = Carbon::createFromFormat('Y-m-d H:s:i', date('Y-m-d H:s:i'));
            $diff_in_hours = $to->diffInHours($from);
            
            if ($diff_in_hours > 0) {
                Password_reset::where("email",$prr->email)->delete();
                return response("Link Expired",410);
            } else {
                $user = User::where("email",$prr->email)->first();
                $user_update = User::findorFail($user->id);
                $user_update->password =  Hash::make($request->password);
                $user_update->save();
                return response("", 200);
            }
        } else {
            abort(404);
        }
    }
    
    public function view(Request $request)
    {
        if ($request->user()->cannot('view', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('view', Auth::user())) {
            $user= User::all();
            return response()->json($user);
        }

    }

    public function viewSpecific($id, Request $request)
    {
        if ($request->user()->cannot('viewSpecific', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('viewSpecific', Auth::user()))
        {
            // VALIDATION
            if (!ctype_digit($id)) {
                return response([ 'message' => "Invalid Data"], 422);
            }
            // Data Processing
            $user = User::findorFail($id);
            return response()->json($user);
        }
    }

    public function add(Request $request)
    {      
        if ($request->user()->cannot('add', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('add', Auth::user())) {

            // VALIDATION
            $rules = array(
                "name" => ["required","min:3"],
                "email" => ["required","email","unique:users,email"],
                "password" => ["required","min:8","alpha_num"],
                "role" => ["required","integer"]
            );
    
            $validator = Validator::make($request->all(),$rules);
            
            if ($validator->fails()) {
                // Detailed Invalid Data information
                // return response(['message' => $validator->errors()], 422);
                return response([ 'message' => "Invalid Data"], 422);
            }

            // Data Processing
            $token = Hash::make(Str::random(59));
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->api_token = $token;
            $check = $user->save();

            $user->roles()->attach($request->role);
            $user->save();

            return response("Added",201);
        }
    }

    public function update($id, Request $request)
    {
        // VALIDATION
        $rules = array(
            "name" => ["required","min:3"],
            "email" => ["required","email","unique:users,email"],
            "password" => ["required","min:8","alpha_num"],
            "role" => ["required","integer"]
        );

        $validator = Validator::make($request->all(),$rules);
        
        if ($validator->fails()) {
            // Detailed Invalid Data information
            // return response(['message' => $validator->errors()], 422);
            return response([ 'message' => "Invalid Data"], 422);
        }

        // VALIDATING ID
        if (!ctype_digit($id)) {
            return response([ 'message' => "Invalid Data"], 422);
        }

        // Data Processing
        if ($request->user()->cannot('update', Auth::user())) {
            abort(403);
        }     
        elseif ($request->user()->can('update', Auth::user())) {
            $user = User::findorFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $check = $user->save();

            $user->roles()->detach();
            $user->roles()->attach($request->role);
            $user->save();
            return response("Updated",200);
        }


    }

    public function delete($id, Request $request)
    {
        if ($request->user()->cannot('delete', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('delete', Auth::user())) {

            // VALIDATING ID
            if (!ctype_digit($id)) {
                return response([ 'message' => "Invalid Data"], 422);
            }

            $user = User::findorFail($id);
            $user->roles()->detach();
            $user->delete();

            response("Deleted",200);
        }

    }

}
