<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thread' => ThreadResource::make($this->thread),
            'user' => UserResource::make($this->user),
            'body' => $this->body,
            'created_at' => DateTimeResource::make($this->created_at),
            'replies' => PostResource::collection($this->replies),
        ];
    }
}