<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $table = "files";
    protected $fillable = [
        'id',
        'title',
        'type',
        'model',
        'model_id',
        'src',
        'duration',
        'status',
        'adder',
        'editor',
    ];

    protected $hidden = [
        'status',
        'adder',
        'editor',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function adderInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'adder');
    }

    public function editorInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'editor');
    }
    //
}
