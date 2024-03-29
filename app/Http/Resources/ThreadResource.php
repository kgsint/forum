<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $body = app(MarkdownRenderer::class)->highlightTheme('material-theme-palenight')->toHtml($this->body);
        // search for mentioned user and highlight
        $body = preg_replace('/@(\w+)/', '<span class="mentioned-user">@$1</span>', $body);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body_markdown' => $this->body, // raw markdown in database
            'body' =>  $body, // markdown to html
            'latest_post' => $this->whenLoaded('latestPost', fn() => LatestPostResource::make($this->latestPost)),
            'no_of_posts' => $this->whenLoaded('posts', fn() => $this->posts->count()),
            'solution' => $this->whenLoaded('solution', fn() => SolutionPostResource::make($this->solution)),
            'topic' => $this->whenLoaded('topic', fn() => TopicResource::make($this->topic)),
            'user' => $this->whenLoaded('user', fn() => UserResource::make($this->user)),
            'created_at' => DateTimeResource::make($this->created_at),
            'can' => [
                'manage' => auth()->user()?->can('manage', $this->resource) ?? false,
                'update' => auth()->user()?->can('update', $this->resource) ?? false,
                'delete' => auth()->user()?->can('delete', $this->resource) ?? false,
            ]
        ];
    }
}
