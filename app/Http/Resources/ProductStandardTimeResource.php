<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductStandardTimeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'product_id'           => $this->product_id,
            'activity_id'          => $this->activity_id,
            'standard_time'        => $this->standard_time,
            'description'          => $this->description,
            'status'               => $this->status,
            'tags'                 => $this->tags,

            // ----------------------------
            //  Relationships (!!! flattened !!!)
            // ----------------------------
            'creator_id'            => $this->createdBy?->id,            
            'creator_full_name'     => $this->createdBy?->full_name,     
            'updater_id'            => $this->updatedBy?->id,            
            'updater_full_name'     => $this->updatedBy?->full_name,     
            'product_id'            => $this->product?->id,            
            'activity_id'           => $this->activity?->id,           
            'activity_name'         => $this->activity?->name,           
            'activity_zone'         => $this->activity?->zone,           
        ];
    }

    /**
     * Get column definitions for tables (frontend usage)
     *
     * @return array<int, array<string, mixed>>
     */
    public static function setColumns(): array
    {
        return [
            [ 'name' => 'actions', 'label' => 'Actions', 'field' => 'actions', 'align' => 'center'],
            ['name' => 'id',                'label' => 'ID',               'field' => 'id',               'sortable' => true],
            ['name' => 'product_id',        'label' => 'Product',          'field' => 'product_id',       'sortable' => true],
            ['name' => 'activity_name',     'label' => 'Activity',         'field' => 'activity_name',    'sortable' => true],
            ['name' => 'activity_zone',     'label' => 'Zone',             'field' => 'activity_zone',    'sortable' => true],
            ['name' => 'standard_time',     'label' => 'Standard Time',    'field' => 'standard_time',    'sortable' => true],
            ['name' => 'description',       'label' => 'Description',      'field' => 'description',      'sortable' => false],
            ['name' => 'status',            'label' => 'Status',           'field' => 'status',           'sortable' => true],
            ['name' => 'tags',              'label' => 'Tags',             'field' => 'tags',             'sortable' => false],
            ['name' => 'creator_full_name', 'label' => 'Created By',       'field' => 'creator_full_name','sortable' => true],
            ['name' => 'updater_full_name', 'label' => 'Updated By',       'field' => 'updater_full_name','sortable' => true],
        ];
    }
}
