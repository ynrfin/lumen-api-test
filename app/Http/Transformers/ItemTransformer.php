<?php

namespace App\Http\Transformers;

use App\Item;
use League\Fractal;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
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
                'is_completed' => $item->is_completed,
                'completed_at' => $item->completed_at,
                'due' => $item->due,
                'urgency' => (int)$item->urgency,
                'updated_by' => $item->updated_by,
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
