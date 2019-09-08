<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\User;
use App\Http\Transformers\ChecklistTransformer;
use App\Http\Transformers\ChecklistSerializer;
use App\Http\Transformers\CustomPaginator;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;
use Illuminate\Validation\Rule;
use Validator;
use App\Helpers\Validation as ValidationHelper;
use Illuminate\Support\Carbon;

class ChecklistController extends Controller
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

    public function showAll(Request $request)
    {
        $pageLimit = $request->input('page_limit' , 10);
        $pageOffset = $request->input('page_offset' , 0);
        $checklist = DB::table('checklists')->skip($pageOffset)->take($pageLimit)->get();


        $checklistResponse = fractal()
            ->collection($checklist)
            ->transformWith(new ChecklistTransformer())
            ->serializeWith(new ChecklistSerializer())
            ->paginateWith(new CustomPaginator(Checklist::paginate($pageLimit)))
            ->toArray();

        return response()->json($checklistResponse);
    }

    public function showOne(Request $request, $id)
    {
        $checklist = Checklist::findOrFail($id);
        $res = new Item($checklist, new ChecklistTransformer());
        $res =  fractal($checklist, new ChecklistTransformer());
        //dd($res);
        //$checklistResponse = fractal()
        //    ->item($checklist)
        //    ->transformWith(new ChecklistTransformer())
        //    ->serializeWith(new ChecklistSerializer())
        //    ->toArray();

        return response()->json($res->toArray());
    }

    /**
     * update resource by id
     *
     * @return void
     */
    public function patch(Request $request, $id)
    {
        $validator = Validator::make($request->input(),[
            'data' => 'required',
            'data.type' => 'required|in:checklists',
            'data.id' => 'required|integer',
            'data.attributes' => 'required',
            'data.attributes.object_domain' => 'required',
            'data.attributes.object_id' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.is_completed' => 'boolean',
            'data.attributes.created_at' => 'required|string',
            'data.attributes.updated_by' => 'string',
            'data.attributes.due' =>[function($attribute, $value, $fail){
                if(null == $value || Carbon::hasFormat($value, "Y:m:d H:i:s")){
                }else{
                    $fail($attribute . "not null and wrong format");
                }
            }],
            'data.links.self' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $checklist = Checklist::findOrFail($id);
        $attributes = $request->input("data.attributes");

        if(array_key_exists('created_at', $attributes)){
            unset($attributes['created_at']);
        }
        if(array_key_exists('updated_at', $attributes)){
            unset($attributes['updated_at']);
        }
        if(array_key_exists('is_completed', $attributes)){
            if(true == $attributes['is_completed']){
                $attributes['completed_at'] = date('Y-m-d H:i:s');
            }
        }

        unset($attributes["last_update_by"]);
        $attributes['updated_by'] = $request->user()->email;
        $checklist->update($attributes);

        $res =  fractal($checklist, new ChecklistTransformer());
        return response()->json($res);

    }

    /**
     * delete by id
     *
     * @return json
     */
    public function delete(Request $requets)
    {
        $resource = Checklist::findOrFail($id);

        $resource->delete();

        return response()->json(null, 204);
    }

}
