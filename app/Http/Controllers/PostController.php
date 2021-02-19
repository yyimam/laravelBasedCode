<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function view(Request $request)
    {
        if ($request->user()->cannot('view', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('view', Auth::user())) {
        $data = Post::all();
        return response()->json($data);
        }
    }

    public function viewSpecific($id, Request $request)
    {
        if ($request->user()->cannot('viewSpecific', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('viewSpecific', Auth::user())) {
        $post = Post::findorFail($id);
        return response()->json($post);
        }
    }

    public function add(Request $request)
    {
        if ($request->user()->cannot('add', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('add', Auth::user())) {
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
        if ($request->user()->cannot('update', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('update', Auth::user())) {
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
        if ($request->user()->cannot('delete', Auth::user())) {
            abort(403);
        }
        elseif ($request->user()->can('delete', Auth::user())) {
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
