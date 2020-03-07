<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = "videos";

    protected $hidden = ["watermark"];

    protected $guarded = [];
}
