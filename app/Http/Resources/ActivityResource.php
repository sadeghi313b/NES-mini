<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'zone' => $this->zone,
            'common_standard_tv_time' => $this->common_standard_tv_time,
            'common_standard_ref_time' => $this->common_standard_ref_time,
            'interchangeable_bundle' => $this->{'interchangeable-bundle'},
            'description' => $this->description,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public static function setColumns(): array
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
            ],
            [
                'name' => 'name',
                'label' => 'Name',
                'field' => 'name',
                'align' => 'left',
            ],
            [
                'name' => 'alias',
                'label' => 'Alias',
                'field' => 'alias',
                'align' => 'left',
            ],
            [
                'name' => 'zone',
                'label' => 'Zone',
                'field' => 'zone',
                'align' => 'center',
            ],
            [
                'name' => 'common_standard_tv_time',
                'label' => 'TV-Time',
                'field' => 'common_standard_tv_time',
                'align' => 'center',
            ],
            [
                'name' => 'common_standard_ref_time',
                'label' => 'REF-Time',
                'field' => 'common_standard_ref_time',
                'align' => 'center',
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'field' => 'status',
                'align' => 'center',
            ],
            [
                'name' => 'tags',
                'label' => 'Tags',
                'field' => 'tags',
                'align' => 'center',
            ],
            [
                'name' => 'created_at',
                'label' => 'Created At',
                'field' => 'created_at',
                'align' => 'center',
            ],
            [
                'name' => 'updated_at',
                'label' => 'Updated At',
                'field' => 'updated_at',
                'align' => 'center',
            ],
        ];
    }
}
