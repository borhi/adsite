<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Ad;

class AdController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ads",
     *     summary="Get list of ads",
     *     tags={"Ads"},
     *     @OA\Parameter(
     *          name="sort",
     *          description="item sort",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"created_at", "price"}
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="order",
     *          description="sort order",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"asc", "desc"}
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             title="AdResponse",
     *             description="Ad response",
     *             @OA\Property(
     *                 property="current_page",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/Ad"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="first_page_url",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="from",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 property="next_page_url",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 property="prev_page_url",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="to",
     *                 type="integer",
     *             ),
     *         )
     *     ),
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/ads/{id}",
     *     summary="Get ad",
     *     tags={"Ads"},
     *     @OA\Parameter(
     *         name="id",
     *         description="Ad id",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="fields",
     *          description="Optional fields (created_at, description, images) comma separated",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Ad")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(Request $request, $id)
    {
        $ad = Ad::with('images')->find($id);
        if (!$ad) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

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

    /**
     * @OA\Post(
     *     path="/ads",
     *     summary="Create ad",
     *     tags={"Ads"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ad")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
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
