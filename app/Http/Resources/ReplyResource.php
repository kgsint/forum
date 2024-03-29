<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user'),
            'excerpt' => Str::limit($this->body, 120),
            'thread' => $this->whenLoaded('thread'),
            'spam_report_count' => $this->whenLoaded('spamReports', fn() => $this->spamReports->count()),
        ];
    }
}
