<?php

namespace Schoutentech\Permissions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class UserHistory extends Eloquent
{
    use HasFactory;

    protected $table = "user_history";
    protected $fillable = ['user_key', 'link'];
}
