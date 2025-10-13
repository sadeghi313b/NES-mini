<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'color'                 => $this->color,
            'description'           => $this->description,
            'tags'                  => $this->tags ?? [],
            'created_by_id'         => $this->createdBy?->id,
            'created_by_full_name'  => $this->createdBy?->full_name,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }

    /**
     * Columns definition for Quasar tables.
     */
    public static function setColumns(): array
    {
        return [
            [
                'name' => 'actions',
                'label' => 'Actions',
                'field' => 'actions',
                'align' => 'center',
                'sortable' => false,
            ],
            [
                'name' => 'id',
                'label' => 'ID',
                'field' => 'id',
                'align' => 'center',
                'sortable' => true,
            ],
            [
                'name' => 'name',
                'label' => 'Name',
                'field' => 'name',
                'align' => 'left',
                'sortable' => true,
            ],
            [
                'name' => 'color',
                'label' => 'Color',
                'field' => 'color',
                'align' => 'left',
                'sortable' => true,
            ],
            [
                'name' => 'created_at',
                'label' => 'Created at',
                'field' => 'created_at',
                'align' => 'center',
                'sortable' => true,
            ],
        ];
    }
}
