<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Export_progress extends Model
{
    protected $table = 'export_progress';

    public $timestamps = false;

    public function video() {
        return $this->belongsTo('App\Video', 'idVideo', 'id');
    }
}
