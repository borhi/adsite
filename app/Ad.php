<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  title="Ad",
 *  description="Ad model",
 *  @OA\Xml(
 *      name="Ad"
 *  ),
 *  @OA\Property(
 *      property="id",
 *      type="integer"
 *  ),
 *  @OA\Property(
 *      property="name",
 *      type="string"
 *  ),
 *  @OA\Property(
 *      property="description",
 *      type="string"
 *  ),
 *  @OA\Property(
 *      property="link",
 *      type="string"
 *  ),
 *  @OA\Property(
 *      property="price",
 *      type="number"
 *  ),
 *  @OA\Property(
 *      property="created_at",
 *      type="string"
 *  ),
 *  @OA\Property(
 *      property="images",
 *      type="array",
 *      @OA\Items(
 *         ref="#/components/schemas/Image"
 *      )
 *  )
 * )
 */

class Ad extends Model
{
    protected $fillable = ['name', 'description', 'price'];

    protected $visible = ['name', 'link', 'price'];

    /**
     * Get the images for the ad.
     */
    public function images()
    {
        return $this->hasMany('App\Image');
    }
}
