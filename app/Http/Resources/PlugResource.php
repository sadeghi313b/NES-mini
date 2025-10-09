<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlugResource extends JsonResource
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
            'type' => $this->type,
            'description' => $this->description,
            'tag' => $this->tag,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }



    public static function setColumns()
    {
        return [
            ['name' => 'actions', 'label' => 'Actions', 'field' => 'actions', 'align' => 'center'],
            ['name' => 'id', 'label' => 'ID', 'field' => 'id', 'align' => 'center', 'sortable' => true],
            ['name' => 'type', 'label' => 'Type', 'field' => 'type', 'align' => 'left', 'sortable' => true],
            ['name' => 'description', 'label' => 'Description', 'field' => 'description', 'align' => 'left', 'sortable' => false],
            ['name' => 'tag', 'label' => 'Tag', 'field' => 'tag', 'align' => 'left', 'sortable' => true],
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
