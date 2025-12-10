<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'book' => new BookResource($this->whenLoaded('book', $this->book)),
            'loan_date' => $this->loan_date,
            'return_date' => $this->return_date,
            'returned_at' => $this->returned_at,
        ];
    }
}
