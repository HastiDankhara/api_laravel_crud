<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ResponseController as ResponseController;

class PostController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();
        return $this->sendSuccess($data['posts'], 'All Post Data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]
        );
        if ($validate->fails()) {
            return $this->sendError('Validation Error', $validate->errors()->all());
        }
        $img = $request->image;
        $extension = $img->getClientOriginalExtension();
        $imagename = time() . '.' . $extension;
        $img->move(public_path('images'), $imagename);
        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagename,
        ]);
        return $this->sendSuccess($post, 'Post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )
            ->where('id', $id)
            ->get();
        return $this->sendSuccess($data['post'], 'Post Data');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]
        );
        if ($validate->fails()) {
            return $this->sendError('Validation Error', $validate->errors()->all());
        }
        $postimage = Post::select('id', 'image')->where(['id' => $id])->get();
        // return $postimage[0]->image;
        if ($request->image != '') {
            $path = public_path() . '/images/';
            if ($postimage[0]->image != '' && $postimage[0]->image != null) {
                $oldfile = $path . $postimage[0]->image;
                if (file_exists($oldfile)) {
                    unlink($oldfile);
                }
                $img = $request->image;
                $extension = $img->getClientOriginalExtension();
                $imagename = time() . '.' . $extension;
                $img->move(public_path('images'), $imagename);
            }
        } else {
            $imagename = $postimage->image;
        }
        $post = Post::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagename,
        ]);
        return $this->sendSuccess($post, 'Post updated successfully');
    }

    // public function update(Request $request, string $id)
    // {
    //     $validate = Validator::make($request->all(), [
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     if ($validate->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation Error',
    //             'errors' => $validate->errors()->all()
    //         ], 401);
    //     }

    //     $post = Post::where('id', $id)->first();

    //     if (!$post) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Post not found'
    //         ], 404);
    //     }

    //     $imagename = $post->image;

    //     if ($request->hasFile('image')) {
    //         $path = public_path() . '/images/';
    //         if ($imagename && file_exists($path . $imagename)) {
    //             unlink($path . $imagename);
    //         }

    //         $img = $request->file('image');
    //         $imagename = time() . '.' . $img->getClientOriginalExtension();
    //         $img->move($path, $imagename);
    //     }

    //     $post->update([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'image' => $imagename,
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Post updated successfully',
    //         'data' => $post,
    //     ], 200);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagepath = Post::select('image')->where('id', $id)->get();

        $filepath = public_path() . '/images/' . $imagepath[0]['image'];
        unlink($filepath);
        $post = Post::where('id', $id)->delete();
        return $this->sendSuccess($post, 'Post deleted successfully');
    }
}
