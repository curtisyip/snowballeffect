<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $connection = 'mysql';
    protected $collection = 'schedules';

    protected $fillable = [
        'guard_id', 'start_event','end_event'
    ];

    public function guard_info()
    {
        return $this->hasOne('App\Guard','id','guard_id');
    }
}
