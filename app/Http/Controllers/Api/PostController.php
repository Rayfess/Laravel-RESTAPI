<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $posts = Post::latest()->paginate(5);

        if ($posts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No posts found.',
                'data' => [],
            ], 404);  // 404 jika tidak ada data
        }
        //return collection of posts as a resource 
        // 
        return response()->json([
            'success' => true,
            'message' => "List Data Posts",
            'data' => $posts
        ]);
        /*
         alternatif:
         harus menggunakan **use Illuminate\Http\Resources\Json\AnonymousResourceCollection; untuk terhindar dari warning yang sebenarnya tidak berpengaruh atau tidak penting
         return new PostResource(true, 'List Data Posts', $posts);
         
            atau

        return PostResource::collection($posts)->additional([
             'success' => true,
             'message' => 'List Data Posts',
         ]);
        */
    }
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validasi gagal",
                'errors' => $validator->errors(),
            ], 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        //return response
        return response()->json([
            'status' => true,
            'message' => "Data Post Berhasil Ditambahkan",
            'data' => $post
        ], 201);
    }

    public function show($id)
    {
        $post =  Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => "Post not found",
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Detail Data Post",
            'data' => $post
        ], 201);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validasi gagal",
                'errors' => $validator->errors(),
            ], 422);
        }

        //find post by ID
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => "Post not found",
                'data' => null
            ], 404);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/' . basename($post->image));

            //update post with new image
            $post->update([
                'image'     => $image->hashName(), //basename($imagePath)
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        //return response
        // return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
        return response()->json([
            'status' => true,
            'message' => "Data berhasil diubah",
            'data' => $post,
        ]);
    }

    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => "Post not found",
                'data' => null
            ], 404);
        }

        Storage::delete('public/posts' . basename($post->image));
        $post->delete();
        return response()->json([
            'status' => true,
            'message' => "Data Telah Dihapus",
            'data' => null,
        ]);
    }
}
