<?php


namespace Nikservik\SimpleSupport\Models;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportMessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'read_at' => $this->read_at,
        ];
    }
}
