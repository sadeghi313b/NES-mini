<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date, //? $this->date->format('Y-m-d H:i') : null,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? null, 
            'quantity' => $this->quantity,
            'description' => $this->description,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'creator_name' => $this->creator->name ?? null, 
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public static function setColumns(): array 
    {
        return [ 
            [ 'name' => 'actions', 'label' => 'Actions', 'field' => 'actions', 'align' => 'center' ], 
            [ 'name' => 'id', 'label' => 'ID', 'field' => 'id', 'align' => 'left', 'sortable' => true ], 
            [ 'name' => 'date', 'label' => 'Date', 'field' => 'date', 'align' => 'left', 'sortable' => true ], 
            [ 'name' => 'product_id', 'label' => 'Product', 'field' => 'product_id', 'align' => 'left', 'sortable' => true ], 
            [ 'name' => 'quantity', 'label' => 'Quantity', 'field' => 'quantity', 'align' => 'right', 'sortable' => true ], 
            [ 'name' => 'status', 'label' => 'Status', 'field' => 'status', 'align' => 'center' ], 
            [ 'name' => 'description', 'label' => 'Description', 'field' => 'description', 'align' => 'center' ], 
            // [ 'name' => 'tags', 'label' => 'Tags', 'field' => 'tags', 'align' => 'center' ], 
            [ 'name' => 'created_at', 'label' => 'Created at', 'field' => 'created_at', 'align' => 'left' ], 
        ]; 
    }
}
