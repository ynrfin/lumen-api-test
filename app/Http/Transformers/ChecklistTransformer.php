<?php

namespace App\Http\Transformers;

use App\Checklist;
use League\Fractal;
use League\Fractal\TransformerAbstract;

class ChecklistTransformer extends TransformerAbstract
{
    /**
     * Transform the model into desired response
     *
     * @return void
     */
    public function transform(Checklist $checklist)
    {
        return [
            'type' => 'checklists',
            'id' => $checklist->id,
            'attributes' => [
                'object_domain' => $checklist->object_domain,
                'object_id' => (string)$checklist->id,
                'description' => $checklist->description,
                'is_completed' => $checklist->is_completed,
                'due' => $checklist->due,
                'urgency' => (int)$checklist->urgency,
                'completed_at' => $checklist->completed_at,
                'last_update_by' => $checklist->updated_by,
                'updated_at' => date('c', strtotime($checklist->updated_at)),
                'created_at' => date('c', strtotime($checklist->created_at)),
            ],
            'links' => [
                'self' => route('checklists.showOne', ['id' => $checklist->id])
            ]
        ];
    }
        
}
?>
