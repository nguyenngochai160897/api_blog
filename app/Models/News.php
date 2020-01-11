<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'short_description', 'image', 'description', 'category_id',
    ];
    public function category(){
        return $this->belongsTo("App\Models\Category");
    }
}
