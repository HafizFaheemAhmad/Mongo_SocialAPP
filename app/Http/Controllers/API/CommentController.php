<?php

namespace App\Http\Controllers\API;

use App\Post;
use App\Comment;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Controllers\Controller;
use MongoDB\Client as DB;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CommentController extends Controller
{

// for show comment

    public function show()
    {
        $comment = $collection = (new DB)->new->Comment;
        $comment_data = $collection->findOne([
            '_id' => new \MongoDB\BSON\ObjectId($comment)
        ]);
        return response()->json($comment_data);
    }

//for show all comment

    public function showAll()
    {
        $collection = (new DB)->new->Post;
        $post_data = $collection->find();
        foreach ($post_data as $document) {
            $document = [$document['_id'], "title" => $document['title'], "body" => $document['body'], "attachment" => $document['attachment'], "user_id" => $document['user_id']];
            return response()->json($document);
        }
    }

//For save Comment

    public function store(CreateCommentRequest $request)
    {
        $data = $request->validated();

        try {
            $collection = (new DB)->new->Post;

            $token = $request->bearerToken();
            $decoded_data = JWT::decode($token, new Key('example', 'HS256'));

            $comment['user_id'] = $decoded_data->data->id;
            $comment['post_id'] = $data['post_id'];
            $comment['comment'] = $data['comment'];
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
            $comment['attachment'] = $data['attachment'];
            $collection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($data['post_id'])],
                [
                    '$push' => ['comments' => $comment]
                ]
            );
            //$comment->save();
            if ($comment) {
                $success['message'] =  "Comment Create Successfully";
                return response()->json([
                    $success, 200
                ]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
            return new CommentResource($comment->fresh());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Update Comment

    public function updateComment(UpdateCommentRequest $request)
    {
        try {
            $input = $request->validated();
            $comment_exit = (new DB)->new->Post;
            //$data = Comment::find($request['id']);
            $comment_exit->comment = $request->input('comment');
            //store your file into directory and db
            $user_id = new \MongoDB\BSON\ObjectId($input['user_id']);
            // $post_id = new \MongoDB\BSON\ObjectId($input['id']);
            $comment_exit->updateOne(
                ['_id' => $user_id],
                // ['_id' => $post_id],
                ['$set' => $comment_exit]
            );
            //$data->save();
            if ($comment_exit) {
                $success['message'] =  "Comment Update Successfully";
                return response()->json([$success, 200, $comment_exit]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Delete Comment

    public function DeleteComment($id)
    {
        try {
            $comment = (new DB)->new->Post;
            $comment->findOne([
                '_id' => new \MongoDB\BSON\ObjectId("$id")
            ]);
            // $user = new Comment();
            // $user = Comment::find($id);
            if ($comment) {
                //$comment->delete();
                $delete = $comment->deleteOne($comment);
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
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
