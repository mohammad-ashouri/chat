<?php

namespace App\Models\Catalogs;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IranicMediaType extends Model
{
    use SoftDeletes;

    protected $table = "iranic_media_types";
    protected $fillable = [
        'id',
        'name',
        'is_active',
        'adder',
        'editor',
    ];

    protected $hidden = [
        'is_active',
        'adder',
        'editor',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function adderInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'adder', 'id');
    }

    public function editorInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'editor', 'id');
    }
}
