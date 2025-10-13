<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    //. -------------------------------------------------------------------------- */
    //.                                   toArray                                  */
    //. -------------------------------------------------------------------------- */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request), //TODO: parse this line
            'status' => $this->status->label(),
            // 'seen' => $this->seen ? 'Seen' : 'unSeen',
            'seen' => $this->seenLabel,
            'product' => $this->product?->id,
            'month' => $this->month?->name,
            'created_by' => $this?->createdBy?->full_name,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                 setColumns                                 */
    //. -------------------------------------------------------------------------- */
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
                'name' => 'product',
                'label' => 'Product',
                'field' => 'product',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'month',
                'label' => 'Month',
                'field' => 'month',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'quantity',
                'label' => 'Quantity',
                'field' => 'quantity',
                'align' => 'right',
                'sortable' => true
            ],
            [
                'name' => 'notification_date',
                'label' => 'Notification Date',
                'field' => 'notification_date',
                'align' => 'left',
                'sortable' => true
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'field' => 'status',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'seen',
                'label' => 'Seen',
                'field' => 'seen',
                'align' => 'center',
                'sortable' => true
            ],
            [
                'name' => 'created_by',
                'label' => 'Created By',
                'field' => 'created_by',
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
                'name' => 'created_at',
                'label' => 'Created at',
                'field' => 'created_at',
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
