<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

use App\User;
use App\Post;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;

class EditPostController extends Controller
{
    //
    public function __invoke(Request $request, $id) {
        $post=Post::findorFail($id);
        $user = Auth::user();
        if($post->user_id==$user->id||$user->can('edit')){
            $post->title = $request->title;
            $post->content = $request->content;

            if($request->url!="") $url = Str::slug($request->url);
                else $url=Str::slug($request->title);

            if($request->date!="" && $request->time!="") {
                $timestamp = Carbon::create($request->date . ' ' . $request->time);
                if($timestamp>Carbon::now()) $post->public_at = $timestamp;
            } else{
                $timestamp = Carbon::create(2010, 1, 1, 0, 0, 0);
                $post->public_at = $timestamp;
            }
            
            $post->url = $url;
            $post->save();
            Cache::flush();
        } else return redirect::intended('/admin')->with('error', 'You dont have permission to edit this post.');
        if(is_null($post)) return back()->with('error', 'Something went wrong! Please try again later');
        return redirect::intended('/admin')->with('success', 'Post edited successfully');
    }
}
