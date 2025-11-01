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
            //   \...parent::toArray($request),
            'id' => $this->id,
            'product' => $this->product_id,
            'carried' => $this->carried_id,
            'idd' => $this->idd,
            'month' => $this->month?->name,
            'quantity' => $this->quantity,
            'notification_date' => $this->notification_date,
            'seen' => $this->seenLabel,
            'status' => $this->status->label(),
            'tags' => $this->tags,
            'description' => $this->description,
            'fiscal_year' => $this->fiscal_year,
            'created_by' => $this?->createdBy?->full_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
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
                'name' => 'created_by',
                'label' => 'Created By',
                'field' => 'created_by',
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
                'name' => 'deleted_at',
                'label' => 'Deleted at',
                'field' => 'deleted_at',
                'align' => 'center',
                'sortable' => true
            ],
        ];
    }
}
