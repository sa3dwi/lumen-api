<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Transformers\PostTransformer;

class PostController extends BaseController
{
    /**
     * @api {get} /posts (post list)
     * @apiDescription post list
     * @apiGroup Post
     * @apiPermission none
     * @apiParam {String='comments:limit(x)','user'} [include]  include
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *     "data": [
     *       {
     *         "id": 1,
     *         "user_id": 3,
     *         "title": "foo",
     *         "content": "",
     *         "created_at": "2018-03-30 15:36:30",
     *         "user": {
     *           "data": {
     *             "id": 3,
     *             "email": "foo@bar.com",
     *             "name": "",
     *             "avatar": "",
     *             "created_at": "2018-03-30 15:34:01",
     *             "updated_at": "2018-03-30 15:34:01",
     *             "deleted_at": null
     *           }
     *         },
     *         "comments": {
     *           "data": [],
     *           "meta": {
     *             "total": 0
     *           }
     *         }
     *       }
     *     ],
     *     "meta": {
     *       "pagination": {
     *         "total": 2,
     *         "count": 2,
     *         "per_page": 15,
     *         "current_page": 1,
     *         "total_pages": 1,
     *         "links": []
     *       }
     *     }
     *   }
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate();

        return $this->response->paginator($posts, new PostTransformer());
    }

    /**
     * @api {get} /user/posts (my post list)
     * @apiDescription (my post list)
     * @apiGroup Post
     * @apiPermission none
     * @apiParam {String='comments:limit(x)'} [include]  include
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *     "data": [
     *       {
     *         "id": 1,
     *         "user_id": 3,
     *         "title": "foo",
     *         "content": "",
     *         "created_at": "2018-03-30 15:36:30",
     *         "user": {
     *           "data": {
     *             "id": 3,
     *             "email": "foo@bar.com1",
     *             "name": "",
     *             "avatar": "",
     *             "created_at": "2018-03-30 15:34:01",
     *             "updated_at": "2018-03-30 15:34:01",
     *             "deleted_at": null
     *           }
     *         },
     *         "comments": {
     *           "data": [],
     *           "meta": {
     *             "total": 0
     *           }
     *         }
     *       }
     *     ],
     *     "meta": {
     *       "pagination": {
     *         "total": 2,
     *         "count": 2,
     *         "per_page": 15,
     *         "current_page": 1,
     *         "total_pages": 1,
     *         "links": []
     *       }
     *     }
     *   }
     */
    public function userIndex()
    {
        $posts = Post::where(['user_id' => $this->user()->id])
            ->paginate();

        return $this->response->paginator($posts, new PostTransformer());
    }

    /**
     * @api {get} /posts/{id} (post detail)
     * @apiDescription (post detail)
     * @apiGroup Post
     * @apiPermission none
     * @apiParam {String='comments','user'} [include]  include
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 200 OK
     *   {
     *     "data": {
     *       "id": 1,
     *       "user_id": 3,
     *       "title": "foo",
     *       "content": "",
     *       "created_at": "2018-03-30 15:36:30",
     *       "user": {
     *         "data": {
     *           "id": 3,
     *           "email": "foo@bar.com",
     *           "name": "",
     *           "avatar": "",
     *           "created_at": "2018-03-30 15:34:01",
     *           "updated_at": "2018-03-30 15:34:01",
     *           "deleted_at": null
     *         }
     *       },
     *       "comments": {
     *         "data": [
     *           {
     *             "id": 1,
     *             "post_id": 1,
     *             "user_id": 1,
     *             "reply_user_id": 0,
     *             "content": "foobar",
     *             "created_at": "2018-04-06 14:51:34"
     *           }
     *         ],
     *         "meta": {
     *           "total": 1
     *         }
     *       }
     *     }
     *   }
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        return $this->response->item($post, new PostTransformer());
    }

    /**
     * @api {post} /posts (create post)
     * @apiDescription (create post)
     * @apiGroup Post
     * @apiPermission jwt
     * @apiParam {String} title  post title
     * @apiParam {String} content  post content
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 201 Created
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->input(), [
            'title' => 'required|string|max:50',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $attributes = $request->only('title', 'content');
        $attributes['user_id'] = $this->user()->id;
        $post = Post::create($attributes);

        $location = dingo_route('v1', 'posts.show', $post->id);

        return $this->response
            ->item($post, new PostTransformer())
            //->withHeader('Location', $location)
            ->setStatusCode(201);
    }

    /**
     * @api {put} /posts/{id} (update post)
     * @apiDescription (update post)
     * @apiGroup Post
     * @apiPermission jwt
     * @apiParam {String} title  post title
     * @apiParam {String} content  post content
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 204 NO CONTENT
     */
    public function update($id, Request $request)
    {
        $post = Post::findOrFail($id);

        // forbidden
        if ($post->user_id != $this->user()->id) {
            return $this->response->errorForbidden();
        }

        $validator = \Validator::make($request->input(), [
            'title' => 'required|string|max:50',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $post->update($request->only('title', 'content'));

        return $this->response->noContent();
    }

    /**
     * @api {delete} /posts/{id} (delete post)
     * @apiDescription (delete post)
     * @apiGroup Post
     * @apiPermission jwt
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *   HTTP/1.1 204 NO CONTENT
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // forbidden
        if ($post->user_id != $this->user()->id) {
            return $this->response->errorForbidden();
        }

        $post->delete();

        return $this->response->noContent();
    }
}
