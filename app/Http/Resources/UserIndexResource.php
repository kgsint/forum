<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $accountType = match($this->type) {
            User::DEFAULT => 'user',
            User::MODERATOR => 'moderator',
            User::ADMIN => 'admin',
        };

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'type' => $accountType,
            'avatar' => $this->getAvatar(),
            'is_banned' => $this->isBanned(),
            'banned_reason' => $this->banned_reason,
            'joined_at' => DateTimeResource::make($this->created_at),
            'can' => [
                'ban' => auth()->user()->can('ban', $this->resource),
                'delete' => auth()->user()->can('delete', $this->resource),
            ]
        ];
    }
}
