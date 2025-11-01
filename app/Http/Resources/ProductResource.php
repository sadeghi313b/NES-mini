<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'cable_id' => $this->cable_id,
            'cutting_length' => $this->cutting_length,
            'tall_jacket_length' => $this->tall_jacket_length,
            'short_jacket_length' => $this->short_jacket_length,
            'remaining_blue' => $this->remaining_blue,
            'remaining_brown' => $this->remaining_brown,
            'remaining_yellow' => $this->remaining_yellow,
            'blue_insulation_length' => $this->blue_insulation_length,
            'brown_insulation_length' => $this->brown_insulation_length,
            'yellow_insulation_length' => $this->yellow_insulation_length,
            'blue_terminal_id' => $this->blue_terminal_id,
            'brown_terminal_id' => $this->brown_terminal_id,
            'yellow_terminal_id' => $this->yellow_terminal_id,
            'double_terminal_id' => $this->double_terminal_id,
            'plug_id' => $this->plug_id,
            'cord_id' => $this->cord_id,
            'extra_earth_length' => $this->extra_earth_length,
            'cord_length' => $this->cord_length,
            'ferrit_id' => $this->ferrit_id,
            'description' => $this->description,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_by' => [
                'id' => $this->createdBy?->id,
                'full_name' => $this->createdBy?->full_name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public static function setColumns()
    {
        return [
            [
                'name' => 'id',
                'label' => 'ID',
                'field' => 'id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'customer_id',
                'label' => 'Customer',
                'field' => 'customer_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'name',
                'label' => 'Name',
                'field' => 'name',
                'align' => 'left',
                'sortable' => true
            ],
            [
                'name' => 'cable_id',
                'label' => 'Cable',
                'field' => 'cable_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'cutting_length',
                'label' => 'Cutting Length',
                'field' => 'cutting_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'tall_jacket_length',
                'label' => 'Tall Jacket',
                'field' => 'tall_jacket_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'short_jacket_length',
                'label' => 'Short Jacket',
                'field' => 'short_jacket_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'remaining_blue',
                'label' => 'Remaining Blue',
                'field' => 'remaining_blue',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'remaining_brown',
                'label' => 'Remaining Brown',
                'field' => 'remaining_brown',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'remaining_yellow',
                'label' => 'Remaining Yellow',
                'field' => 'remaining_yellow',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'blue_insulation_length',
                'label' => 'Blue Insulation',
                'field' => 'blue_insulation_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'brown_insulation_length',
                'label' => 'Brown Insulation',
                'field' => 'brown_insulation_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'yellow_insulation_length',
                'label' => 'Yellow Insulation',
                'field' => 'yellow_insulation_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'blue_terminal_id',
                'label' => 'Blue Terminal',
                'field' => 'blue_terminal_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'brown_terminal_id',
                'label' => 'Brown Terminal',
                'field' => 'brown_terminal_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'yellow_terminal_id',
                'label' => 'Yellow Terminal',
                'field' => 'yellow_terminal_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'double_terminal_id',
                'label' => 'Double Terminal',
                'field' => 'double_terminal_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'plug_id',
                'label' => 'Plug',
                'field' => 'plug_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'cord_id',
                'label' => 'Cord',
                'field' => 'cord_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'extra_earth_length',
                'label' => 'Extra Earth',
                'field' => 'extra_earth_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'cord_length',
                'label' => 'Cord Length',
                'field' => 'cord_length',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'ferrit_id',
                'label' => 'Ferrit',
                'field' => 'ferrit_id',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'field' => 'description',
                'align' => 'left',
                'sortable' => false
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'field' => 'status',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'tags',
                'label' => 'Tags',
                'field' => 'tags',
                'align' => 'left',
                'sortable' => false
            ],
            [
                'name' => 'created_by',
                'label' => 'Created By',
                'field' => 'created_by.full_name',
                'align' => 'left',
                'sortable' => true
            ],
            [
                'name' => 'created_at',
                'label' => 'Created at',
                'field' => 'created_at',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'updated_at',
                'label' => 'Updated at',
                'field' => 'updated_at',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'deleted_at',
                'label' => 'Deleted at',
                'field' => 'deleted_at',
                'align' => 'center',
                'sortable' => true
            ],
        ];
    }
}