<?php

namespace App\Models;

use App\Traits\GeneralClasses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class Survey extends Model
{
    use SoftDeletes, HasTags, GeneralClasses;

    protected $table = "surveys";
    protected $fillable = [
        'id',
        'title',
        'slug',
        'short_title',
        'body',
        'help',
        'roles',
        'faq',
        'participants_count',
        'meta_description',
        'status',
        'related_posts',
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

    public static function setLocale(): string
    {
        return 'fa';
    }

    public static function getLocale(): string
    {
        return 'fa';
    }

    public function mainFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id', 'model_id')->where('type', 'main_file')->where('model', 'App\Models\Survey');
    }

    public function adderInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adder', 'id');
    }

    public function editorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor', 'id');
    }

    public function surveyFormats(): HasMany
    {
        return $this->hasMany(SurveyFormat::class, 'survey_id', 'id')->orderBy('name');
    }

    public function surveyFormatNames(): string
    {
        return $this->hasMany(SurveyFormat::class, 'survey_id', 'id')->pluck('name')->implode(', ');
    }
}
