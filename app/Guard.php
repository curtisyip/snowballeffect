<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guard extends Model
{
    protected $connection = 'mysql';
    protected $collection = 'guards';
}
