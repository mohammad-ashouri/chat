<?php

namespace App\Models;

use App\Models\Catalogs\IranicMediaType;
use App\Models\Catalogs\PostType;
use App\Traits\GeneralClasses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use SoftDeletes, HasTags, GeneralClasses;

    protected $table = "posts";
    protected $fillable = [
        'id',
        'title',
        'slug',
        'short_title',
        'body',
        'post_type',
        'iranic_media_type',
        'meta_description',
        'hottest',
        'main_hottest',
        'special',
        'status',
        'related_posts',
        'adder',
        'editor',
    ];

    protected $hidden = [
        'status',
        'hottest',
        'main_hottest',
        'adder',
        'editor',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function setLocale(): string
    {
        return 'fa';
    }

    public static function getLocale(): string
    {
        return 'fa';
    }

    public function postTypeInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PostType::class, 'post_type', 'id');
    }

    public function iranicMediaTypeInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IranicMediaType::class, 'iranic_media_type', 'id');
    }

    public function mainImage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'id', 'model_id')->where('type', 'main_picture')->where('model', 'App\Models\Post');
    }

    public function getAudio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'id', 'model_id')->where('type', 'main_audio')->where('model', 'App\Models\Post');
    }

    public function getVideo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'id', 'model_id')->where('type', 'main_video')->where('model', 'App\Models\Post');
    }

    public function adderInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'adder', 'id');
    }

    public function editorInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'editor', 'id');
    }
    //
}
