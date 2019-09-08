<?php

namespace App\Http\Transformers;

use App\Item;
use League\Fractal;
use League\Fractal\TransformerAbstract;

class ItemShowOneTransformer extends TransformerAbstract
{
    /**
     * Transform the model into desired response
     *
     * @return void
     */
    public function transform(Item $item)
    {
        return [
            'type' => 'item',
            'id' => $item->id,
            'attributes' => [
                'description' => $item->description,
                'is_completed' => (boolean)$item->is_completed,
                'completed_at' => $item->completed_at,
                'due' => $item->due,
                'urgency' => (int)$item->urgency,
                'updated_by' => $item->updated_by,
                'created_by' => $item->created_by,
                'checklist_id' => $item->checklist_id,
                'assignee_id' => $item->assignee_id,
                'task_id' => $item->task_id,
                'deleted_at' => $item->deleted_at,
                'updated_at' => date('c', strtotime($item->updated_at)),
                'created_at' => date('c', strtotime($item->created_at)),
            ],
            'links' => [
                'self' => route('checklists.showOne', ['id' => $item->checklist->id])
            ]
        ];
    }
        
}
?>
