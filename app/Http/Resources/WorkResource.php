<?php
//! app/Http/Resources/WorkResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->format('Y-m-d'),
            'employee_id' => $this->employee_id,
            'employee_name' => $this->employee?->name,
            'work_type' => $this->work_type,
            'description' => $this->description,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_by' => $this->created_by,
            'creator_name' => $this->creator?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }


    public static function setColumns(): array
    {
        return [
            ['name' => 'actions', 'label' => 'Actions', 'field' => 'actions', 'align' => 'center'], 
            ['name' => 'id', 'label' => 'ID', 'field' => 'id', 'align' => 'left'],
            ['name' => 'date', 'label' => 'Date', 'field' => 'date', 'align' => 'left'],
            ['name' => 'employee_name', 'label' => 'Employee', 'field' => 'employee_name', 'align' => 'left'],
            ['name' => 'work_type', 'label' => 'Type', 'field' => 'work_type', 'align' => 'left'],
            ['name' => 'status', 'label' => 'Status', 'field' => 'status', 'align' => 'left'],
            ['name' => 'created_at', 'label' => 'Created', 'field' => 'created_at', 'align' => 'left'],
        ];
    }
}
