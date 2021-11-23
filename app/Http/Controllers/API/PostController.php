<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Controllers\Controller;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Client as DB;

class PostController extends Controller
{

// For save Post

    public function store(CreatePostRequest $request)
    {
        try {
            $token = $request->bearerToken();
            $file_name = null;
            // converting base64 decoded image to simple image if exist
            if (!empty($request['attachment'])) {
                // upload Attachment
                $destinationPath = storage_path('\post\users\\');
                $request_type_aux = explode("/", $request['attachment']['mime']);
                $attachment_extention = $request_type_aux[1];
                $image_base64 = base64_decode($request['attachment']['data']);
                $file_name = uniqid() . '.' . $attachment_extention;
                $file = $destinationPath . $file_name;
                // saving in local storage
                file_put_contents($file, $image_base64);
            }
            $data = $request->validated();
            //$post = new Post();
            $post = (new DB)->new->Post;
            $post->attachment = $file_name;
            $post->title = $data['title'];
            $post->body = $data['body'];

            //$post = Post::make($data);
            // $post->user()->associate($data['user_id']);
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));
            $decoded_data->data->id;
            $comment['comments'] = array();
            $post->insertOne($comment);
            $post->insertOne([
                'user_id' => $decoded_data->data->id,
                "attachment" => $file_name,
                "title" => $data['title'],
                "body" => $data['body']
            ]);
            //$post->save();
            if ($post) {
                $success['message'] =  "Post Create Successfully";
                return response()->json([$success, $post]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
            //return new PostResource($post->fresh());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For show Post

    public function show($post)
    {
        $collection = (new DB)->new->Post;
        $post_data = $collection->findOne([
            '_id' => new \MongoDB\BSON\ObjectId($post)
        ]);

        return response()->json($post_data);
    }

//for show all post

    public function showAll()
    {
        $collection = (new DB)->new->Post;
        $post_data = $collection->find();
        foreach ($post_data as $document) {
            $document = [$document['_id'], "title" => $document['title'], "body" => $document['body'], "attachment" => $document['attachment'], "user_id" => $document['user_id']];
            return response()->json($document);
        }
    }

//For Update post

    public function UpdatePost(UpdatePostRequest $request)
    {
        try {
            $input = $request->validated();
            $post_exiit = (new DB)->new->Post;
            //$post_exiit = Post::find($request['id']);
            $post_exiit->title = $request->input('title');
            $post_exiit->body = $request->input('body');
            if (!empty($input['attachment'])) {
                // upload Attachment
                $destinationPath = storage_path('\post\users\\');
                $input_type_aux = explode("/", $input['attachment']['mime']);
                $attachment_extention = $input_type_aux[1];
                $image_base64 = base64_decode($input['attachment']['data']);
                $file_name = uniqid() . '.' . $attachment_extention;
                $file = $destinationPath . $file_name;
                // saving in local storage
                file_put_contents($file, $image_base64);
                $post_exiit->attachment = $file_name;
            }
            $id = new \MongoDB\BSON\ObjectId($input['id']);
            //store your file into directory and db
            $post_exiit->updateOne(
                ['_id' => $id],
                ['$set' => $post_exiit]
            );
            //$data->save();
            if ($post_exiit) {
                $success['message'] =  "Post Updated Successfully";
                return response()->json([$success, 200, $post_exiit]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Delete post

    public function DeletePost($id)
    {
        try {
            $post = (new DB)->new->Post;
            $post->findOne([
                '_id' => new \MongoDB\BSON\ObjectId("$id")
            ]);
            if ($post) {
                $delete = $post->deleteOne($post);
                if ($delete->getDeletedCount() > 0) {
                    return response()->json([
                        "success" => true,
                        "message" => "Post Deleted Successfully!!"
                    ]);
                } else {
                    return response()->json([
                        "success" => false,
                        "message" => "Post not exist"
                    ]);
                }
            }
            //$user = new Post();
            // $user = Post::find($id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
