<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AspectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->__get("id"),
            "name" => $this->__get("name"),
            "description" => $this->__get("description"),
            "icon_path" => $this->__get("icon_path"),
            'icon_url' => $this->__get("icon_path")
                ? Storage::disk('public')->url($this->__get("icon_path"))
                : null,
        ];
    }
}
