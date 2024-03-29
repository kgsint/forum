<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Contracts\PostInterface;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Jobs\NotifyThreadOwner;
use App\Models\User;
use App\Notifications\SomeoneReplyYourThread;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function __construct(
        private PostInterface $postRepo,
    ){}

    // store
    public function store(PostStoreRequest $request, Thread $thread)
    {
        $post = $this->postRepo->store(
            $this->getPostStoreData($request, $thread)
        );

        $redirectUrl = route('forum.show', ['thread' => $thread, 'post' => $post->id]);

        // notify when other user reply to your thread excluding nested reply
        if($request->user()->isNot($thread->user) && is_null($request->parent_id)) {
            (new NotifyThreadOwner($request->user(), $thread, $redirectUrl))
                                                                            ->handle();
        }

        return redirect($redirectUrl);
    }

    // update
    public function update(PostUpdateRequest $request, Thread $thread, Post $post)
    {
        $this->postRepo->update(
            $post,
            $this->getPostUpdateData($request)
        );

        return redirect()->route('forum.show', ['thread' => $thread, 'post' => $post->id]);
    }

    // delete
    public function destroy(Thread $thread, Post $post)
    {
        // authorize
        $this->authorize('delete', $post);

        $this->postRepo->delete($post);

        return redirect()->route('forum.show', $thread);
    }

    // getter for storing post data
    private function getPostStoreData(PostStoreRequest $request, Thread $thread): array
    {
        return [
            'body' => $request->body,
            'thread_id' => $thread->id,
            'parent_id' => $request->parent_id,
            'user_id' => $request->user()->id,
        ];
    }

    // getter for updating post data
    private function getPostUpdateData(PostUpdateRequest $request): array
    {
        return [
            'body' => $request->body,
        ];
    }
}
