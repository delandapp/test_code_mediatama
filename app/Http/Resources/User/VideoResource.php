<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail' => $this->thumbnail,
            'tanggal' => $this->created_at,
            'access' => $this->access,
            'url' => $this->url,
            'jumlah_like' => $this->whenLoaded('likes') ? $this->likes->where('status', 1)->count() : 0,
            'jumlah_dislike' => $this->whenLoaded('dislikes') ? $this->dislikes->where('status', 1)->count() : 0,
            'jumlah_komentar' => $this->whenLoaded('komentars') ? $this->komentars->count() : 0,
            'simpan' => $this->whenNotNull($this->simpan),
        ];
    }
}
