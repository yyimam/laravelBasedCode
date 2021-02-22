<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class PostController extends Controller
{
    public function view(Request $request)
    {
                
        if (Gate::denies('check',"post-view")) {
            abort(403);
        }
        else if (Gate::allows('check',"post-view")) {
            $data = Post::all();
            return response()->json($data);
        }
    }

    public function viewSpecific($id, Request $request)
    {          
        if (Gate::denies('check',"post-viewspecific")) {
            abort(403);
        }
        else if (Gate::allows('check',"post-viewspecific")) {
            $post = Post::findorFail($id);
            return response()->json($post);
        }
    }

    public function add(Request $request)
    {
        if (Gate::denies('check',"post-add")) {
            abort(403);
        }
        else if (Gate::allows('check',"post-add")) {
            $post = new Post;
            $post->author = $request->author;
            $post->title = $request->title;
            $post->description = $request->description;
            $check = $post->save();
            if ($check) {
                return response("Added",201);
            } else {
                return response("something went wrong",500);
            }
        }
    }

    public function update($id, Request $request)
    {
        if (Gate::denies('check',"post-update")) {
            abort(403);
        }
        else if (Gate::allows('check',"post-update")) {
            $post = Post::findorFail($id);
            $post->author = $request->author;
            $post->title = $request->title;
            $post->description = $request->description;
            $check = $post->save();

            if ($check) {
                return response("Updated",200);
            } else {
                return response("something went wrong",500);
            }
            return response()->json($post);
        }
    }

    public function delete($id, Request $request)
    {
        if (Gate::denies('check',"post-delete")) {
            abort(403);
        }
        else if (Gate::allows('check',"post-delete")) {
        $post = Post::findorFail($id);

        $check = $post->delete();
            if ($check) {
                return response("Deleted",200);
            } else {
                return response("something went wrong",500);
            }
        }
    }
}
