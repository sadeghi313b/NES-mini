<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * usage:   OrderResource::collection($orders); //wrapped with 'data'. having 'meta','link' fields if paginated.
     *          new OrderResource($order); OrderResource::make($order); //no wrapping, data, link, meta.
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request), //TODO: parse this line
            'status' => $this->status->label(),
            // 'seen' => $this->seen ? 'Seen' : 'unSeen',
            'seen' => $this->seenLabel,
            'product'=> $this->product?->id,
            'month'=> $this->month?->name,
            'created_by'=> $this?->createdBy?->full_name,
        ];
    }
}
