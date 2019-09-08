<?php

namespace App\Http\Transformers;

use App\Item;
use League\Fractal;
use League\Fractal\TransformerAbstract;

class ItemCompleteTransformer extends TransformerAbstract
{
    /**
     * Transform the model into desired response
     *
     * @return void
     */
    public function transform(Item $item)
    {
        return [
            'id' => $item->id,
            'item_id' => $item->id,
            'is_completed' => (boolean)$item->is_completed,
            'checklist_id' => $item->checklist->id
        ];
    }
        
}
?>
