<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $table = "comments";
    protected $fillable = [
        'id',
        'model',
        'model_id',
        'name',
        'message',
        'headers',
        'ip_address',
        'status',
        'seconder',
    ];

    protected $hidden = [
        'id',
        'model',
        'model_id',
        'headers',
        'ip_address',
        'status',
        'seconder',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function seconderInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'seconder');
    }
}
