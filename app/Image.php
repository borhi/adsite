<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  title="Image",
 *  description="Image model",
 *  @OA\Xml(
 *      name="Image"
 *  ),
 *  @OA\Property(
 *      property="link",
 *      type="string"
 *  ),
 * )
 */
class Image extends Model
{
    protected $fillable = ['link'];

    protected $visible = ['link'];
}
