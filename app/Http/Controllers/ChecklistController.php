<?php

namespace App\Http\Controllers;

use App\Checklist;
use App\Http\Transformers\ChecklistTransformer;
use App\Http\Transformers\ChecklistSerializer;
use App\Http\Transformers\CustomPaginator;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use DB;

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
        $checklistResponse = fractal()
            ->item($checklist)
            ->transformWith(new ChecklistTransformer())
            ->serializeWith(new ChecklistSerializer())
            ->toArray();

        return response()->json($res->toArray());
    }
}
