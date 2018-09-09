<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = ['tracking_status' => 'array', 'sheet_range' => 'array'];

}
