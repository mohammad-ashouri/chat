<?php

namespace App\Models\Catalogs;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    protected $table = "post_types";
    protected $fillable = [
        'id',
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
