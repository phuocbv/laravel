<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['order_at'];
    protected $dates = ['order_at', 'created_at', 'updated_at'];
    protected $casts = ['fulfillments' => 'array'];

    public function email()
    {
        return $this->belongsTo('App\Email', 'mailacc');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function lineitems(){
        return $this->hasMany('App\LineItem', 'order_id');
    }
}
