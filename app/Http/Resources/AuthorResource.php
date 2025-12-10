<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'photo' => $this->photo,
            'books' => BookSimpleResource::collection($this->whenLoaded('books', $this->books)),
        ];
    }
}
