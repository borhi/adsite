<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Ad;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $allowed = array('created_at', 'price');
        $sort = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'created_at';
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $items = Ad::orderBy($sort, $order)->simplePaginate(10);
        foreach ($items as $key => $item) {
            if (count($item->images) > 0) {
                $items[$key]->link = $item->images[0]->link;
            }
        }

        return $items;
    }

    public function show(Request $request, $id)
    {
        $ad = Ad::with('images')->find($id);

        if (count($ad->images) > 0) {
            $ad->link = $ad->images[0]->link;
        }

        $fields = explode(',', $request->input('fields', null));
        $allowed = array('created_at', 'description', 'images');
        foreach ($fields as $field) {
            if (in_array($field, $allowed)) {
                $ad->makeVisible($field);
            }
        }

        return $ad;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|max:200',
            'description' => 'max:1000',
            'images.*.link' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages()->first(), 400);
        }

        if (isset($request->all()['images']) && count($request->all()['images']) > 3) {
            return response()->json('no more than 3 links to a photo', 400);
        }

        $ad = Ad::create($request->all());
        if (isset($request->all()['images'])) {
            $ad->images()->createMany($request->all()['images']);
        }

        return response()->json($ad->id, 201);
    }
}
