<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_product' => $this->order?->product_id, //searchable
            'order_month' => $this->order?->month?->name, //filterable
            'quantity' => $this->quantity,
            'maximum_batch_size' => $this->maximum_batch_size, //filterable
            'printing_date' => $this->printing_date,
            'cutting_date' => $this->cutting_date,
            'status' => $this->status, //filterable
            'tags' => $this->tags,
            'description' => $this->description, //searchable
            'created_by_id' => $this->createdBy?->id, //ignor in columns.js
            'created_by_full_name' => $this->createdBy?->full_name, //filterable
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }


    public static function setColumns()
    {
        return [
            ['name' => 'actions', 'label' => 'Actions', 'align' => 'center'],
            ['name' => 'id', 'label' => 'ID', 'field' => 'id', 'align' => 'center', 'sortable' => true],
            ['name' => 'order', 'label' => 'Order', 'field' => 'order_id', 'align' => 'center', 'sortable' => true],
            ['name' => 'product', 'label' => 'Product', 'field' => 'order_product', 'align' => 'center', 'sortable' => true],
            ['name' => 'month', 'label' => 'Month', 'field' => 'order_month', 'align' => 'center', 'sortable' => true],
            ['name' => 'quantity', 'label' => 'Quantity', 'field' => 'quantity', 'align' => 'right', 'sortable' => true],
            ['name' => 'maximum_batch_size', 'label' => 'Size', 'field' => 'maximum_batch_size', 'align' => 'right', 'sortable' => true],
            ['name' => 'printing_date', 'label' => 'Printing Date', 'field' => 'printing_date', 'align' => 'left', 'sortable' => true],
            ['name' => 'cutting_date', 'label' => 'Cutting Date', 'field' => 'cutting_date', 'align' => 'left', 'sortable' => true],
            ['name' => 'status', 'label' => 'Status', 'field' => 'status', 'align' => 'center', 'sortable' => true],
            [
                'name' => 'tags',
                'label' => 'Tags',
                'field' => 'tags',
                'align' => 'center',
                'sortable' => true
            ],
            ['name' => 'created_by', 'label' => 'Created By', 'field' => 'created_by_fullname', 'align' => 'left', 'sortable' => true],
            ['name' => 'description', 'label' => 'Description', 'field' => 'description', 'align' => 'left', 'sortable' => false],
            ['name' => 'created_at', 'label' => 'Created at', 'field' => 'created_at', 'align' => 'center', 'sortable' => true],
            ['name' => 'deleted_at', 'label' => 'Deleted at', 'field' => 'deleted_at', 'align' => 'center', 'sortable' => true],
        ];
    }
}
