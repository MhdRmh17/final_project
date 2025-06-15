<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;  // ← هذا السطر أضفّه

class ProjectFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray($request)
{
    return [
        'id'           => $this->id,
        'title'        => $this->title,
        'supervisor'   => $this->supervisor,
        'submitted_at' => $this->submitted_at->toDateTimeString(),
        'pdf_url'      => Storage::url($this->pdf_path),
        'status'       => $this->status,
        'description'  => $this->description,
        'user'         => [
            'id'    => $this->user->id,
            'name'  => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ],
    ];
}

}
