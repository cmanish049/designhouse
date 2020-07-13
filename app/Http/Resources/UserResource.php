<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'designs' => DesignResource::collection($this->whenLoaded('designs')),
            'created_at_dates' => [
                'created_at' => $this->created_at,
                'created_at_humans' => $this->created_at->diffForHumans(),
            ],
            'formatted_address' => $this->formatted_address,
            'location' => $this->location,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'available_to_hire' => $this->available_to_hire,
        ];
    }
}
