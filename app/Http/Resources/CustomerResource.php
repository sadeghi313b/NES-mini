<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
            'status' => $this->status,
            'created_by_id' => $this->createdBy?->id,
            'created_by_full_name' => $this->createdBy?->full_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
    //searchable columns: name, description
    //filterable columns: tags,
    //ignor in setColumns: created_by_id,

    public static function setColumns()
    {
        return [
            [
                'name' => 'actions',
                'label' => 'Actions',
                'field' => 'actions',
                'align' => 'center'
            ],
            [
                'name' => 'id',
                'label' => 'ID',
                'field' => 'id',
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
                'name' => 'description',
                'label' => 'Description',
                'field' => 'description',
                'align' => 'left',
                'sortable' => false
            ],
            [
                'name' => 'tags',
                'label' => 'Tags',
                'field' => 'tags',
                'align' => 'left',
                'sortable' => true
            ],
            [
                'name' => 'status',
                'label' => 'status',
                'field' => 'status',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'created_by_full_name',
                'label' => 'Created By',
                'field' => 'created_by_full_name',
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
