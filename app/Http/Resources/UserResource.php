<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'gender' => $this->gender,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'status'     => $this->status,
            'description'=> $this->description,
            'created_by' => $this->created_by,

            // تاریخ‌ها
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            // روابط:
            'phone' => $this->phones->pluck('phone_no')->filter()->implode(', '),
            'roles'  => $this->roles->pluck('name')->filter()->implode(', '),
            'employee' => $this->employee?->id,
        ];
    }
}
