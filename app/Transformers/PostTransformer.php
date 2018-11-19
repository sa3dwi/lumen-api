<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user', 'comments', 'recentComments'];

    public function transform(Post $post)
    {
        return $post->attributesToArray();
    }

    public function includeUser(Post $post)
    {
        if (! $post->user) {
            return $this->null();
        }

        return $this->item($post->user, new UserTransformer());
    }

    public function includeComments(Post $post, ParamBag $params = null)
    {
        $limit = 10;
        if ($params->get('limit')) {
            $limit = (array) $params->get('limit');
            $limit = (int) current($limit);
        }

        $comments = $post->comments()->limit($limit)->get();

        return $this->collection($comments, new CommentTransformer())
            ->setMeta([
                'limit' => $limit,
                'count' => $comments->count(),
            ]);
    }

    /**
     * @param Post $post
     * @return mixed
     */
    public function includeRecentComments(Post $post)
    {
        $comments = $post->recentComments->sortByDesc('id');

        return $this->collection($comments, new CommentTransformer())
            ->setMeta([
                'count' => $comments->count(),
            ]);
    }
}
