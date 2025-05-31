<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes;

    protected $table = "sliders";
    protected $fillable = [
        'id',
        'title',
        'subtitle',
        'type',
        'link',
        'status',
        'adder',
        'editor',
    ];

    protected $hidden = [
        'adder',
        'editor',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function adderInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adder');
    }

    public function editorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor');
    }

    public function file(): BelongsTo
    {
        return $this->BelongsTo(File::class, 'id', 'model_id')->where('type', 'slider');
    }
}
