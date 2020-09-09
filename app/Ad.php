<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
