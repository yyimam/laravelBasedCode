<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;


class RoleController extends Controller
{
    public function view(Request $request)
    {
        if (Gate::denies('check',"role-view")) {
            abort(403);
        }
        else if (Gate::allows('check',"role-view")) {
            $data = Role::all();
            return response()->json($data);
        }
    }

    public function viewSpecific($id, Request $request)
    {
        if (Gate::denies('check',"role-viewspecific")) {
            abort(403);
        }
        else if (Gate::allows('check',"role-viewspecific")) {
          
            // VALIDATING ID
            if (!ctype_digit($id)) {
                return response([ 'message' => "Invalid Data"], 422);
            }

            $role = Role::findorFail($id);
            return response()->json($role);            
        }

    }

    public function add(Request $request)
    {
        if (Gate::denies('check',"role-add")) {
            abort(403);
        }
        else if (Gate::allows('check',"role-add")) {

            // VALIDATION
            $rules = array(
                "name" => ["required","min:3"],
                "slug" => ["required","min:3"],
                "permission" => ["required","min:3"],
            );

            $validator = Validator::make($request->all(),$rules);
            
            if ($validator->fails()) {
                // Detailed Invalid Data information
                // return response(['message' => $validator->errors()], 422);
                return response([ 'message' => "Invalid Data"], 422);
            }

            $role = new Role;
            $role->name = $request->name;
            $role->slug = $request->slug;
            $role->save();

            $listOfPermission = explode(',', $request->permission);
            foreach ($listOfPermission as $Permission) {
                $create_permission_name = $Permission;
                $create_permission_slug = strtolower(str_replace(" ","-", $Permission));

                $finding_permissions = Permission::where('slug',$create_permission_slug)->first();
                if ($finding_permissions != null && $finding_permissions->count() > 0)
                { 
                    $role->permissions()->attach($finding_permissions->id);
                    $role->save();
                }
                else {
                    $permissions = new Permission;
                    $permissions->name = $Permission;
                    $permissions->slug = strtolower(str_replace(" ","-", $Permission));
                    $permissions->save();
    
                    $role->permissions()->attach($permissions->id);
                    $role->save();
                }

        }
        return response("Added",201);
        }
    }

    public function update($id, Request $request)
    {
        if (Gate::denies('check',"role-update")) {
            abort(403);
        }
        else if (Gate::allows('check',"role-update")) {
            
            // VALIDATION
            $rules = array(
                "name" => ["required","min:3"],
                "slug" => ["required","min:3"],
                "permission" => ["required","min:3"],
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

            $role = Role::findorFail($id);
            $role->name = $request->name;
            $role->slug = $request->slug;
            $role->save();

            $listOfPermission = explode(',', $request->permission);
            foreach ($listOfPermission as $Permission) {
                if (Permission::where("slug", strtolower(str_replace(" ","-", $Permission)))->first() != null) 
                {
                    if (Permission::where("slug", strtolower(str_replace(" ","-", $Permission)))->first()->count() > 0) {
                        $p = Permission::where("slug", strtolower(str_replace(" ","-", $Permission)))->first();
                        $p->name = $Permission;
                        $p->slug = strtolower(str_replace(" ","-", $Permission));
                        $p->save();
                    }
                }
                else {                
                    $permissions = new Permission; 
                    $permissions->name = $Permission;
                    $permissions->slug = strtolower(str_replace(" ","-", $Permission));
                    $permissions->save();

                    $role->permissions()->attach($permissions->id);
                    $role->save();
                }

            }
            return response("Updated",200);
        }
    }

    public function delete($id, Request $request)
    {
        if (Gate::denies('check',"role-delete")) {
            abort(403);
        }
        else if (Gate::allows('check',"role-delete")) {

        // VALIDATING ID
        if (!ctype_digit($id)) {
            return response([ 'message' => "Invalid Data"], 422);
        }
        
        $role = Role::findorFail($id);
        $role->permissions()->delete();
        $role->delete();
        $role->permissions()->detach();
        
        return response("Deleted",200);
        }
    }

}
