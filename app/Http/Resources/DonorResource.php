<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray($request): array
{
    return [
        'id'=>$this->id,
        'name'=>$this->name,
        'email'=>$this->email,
        'phone'=>$this->phone,
        'address'=>$this->address,
    ];
}
}
