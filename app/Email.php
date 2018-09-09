<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $dates = ['created_at', 'updated_at', 'ebay_token_expired'];
}
