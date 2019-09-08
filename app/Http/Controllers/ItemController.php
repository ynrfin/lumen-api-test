<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Http\Transformers\ItemShowOneTransformer;
use App\Http\Transformers\ItemTransformer;
use App\Http\Transformers\ItemCompleteTransformer;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Validator;
use DB;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Create an item given body & checklistId
     *
     * @return Item
     */
    public function create(Request $request, $checklistId)
    {
        $validator = Validator::make($request->input(),[
            'data' => 'required',
            'data.attributes' => 'required',
            'data.attributes.description' => 'required|string',
            'data.attributes.is_completed' => 'boolean',
            'data.attributes.due' =>[function($attribute, $value, $fail){
                if(null == $value || Carbon::hasFormat($value, "Y-m-d H:i:s")){
                }else{
                    $fail($attribute . " not null and wrong format");
                }
            }],
            'data.attributes.assignee_id' => 'integer',
            'data.attributes.urgency' => 'integer',
            'data.attributes.task_id' => 'integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $checklist = Checklist::findOrFail($checklistId);

        $validatedInput = $validator->valid();

        $newItemAttributes = $validatedInput['data']['attributes'];

        $newItemAttributes['created_by'] = $request->user()->id;
        $newItemAttributes['task_id'] = $checklist->id;
        $newItemAttributes['checklist_id'] = $checklist->id;

        $item = Item::create($newItemAttributes);
        $res =  fractal($item, new ItemTransformer());
        return response()->json($res);
    }

    /**
     * get an item
     *
     * @return void
     */
    public function showOne(Request $request, $checklistId, $itemId)
    {
        $item = Item::where([
            ['id', '=', $itemId],
            ['checklist_id', '=',$checklistId]
        ])->firstOrFail();

        $res =  fractal($item, new ItemShowOneTransformer());
        return response()->json($res);

    }

    /**
     * delete item by id
     *
     * @return 204
     */
    public function delete($checklistId, $itemId)
    {
        $item = Item::where([
            ['id', '=', $itemId],
            ['checklist_id', '=',$checklistId]
        ])->firstOrFail();

        $item->delete();

        return response()->json("", 204);
    }

    /**
     * turn the item as completed item
     *
     * @return mixed
     */
    public function complete(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'data.*.item_id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->valid();
        $itemIds = $validatedData['data'];

        $updated = DB::table('items')
            ->whereIn('id', $itemIds)
            ->update(['is_completed' => true, 'completed_at' => date("Y-m-d H:i:s")]);

        $items = Item::whereIn('id', $itemIds)->get();

        $itemsResponse = fractal()
            ->collection($items)
            ->transformWith(new ItemCompleteTransformer())
            ->toArray();

        return response()->json($itemsResponse);
    }
}
