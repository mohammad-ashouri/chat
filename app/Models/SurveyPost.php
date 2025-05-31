<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyPost extends Model
{
    use SoftDeletes;

    protected $table = "survey_posts";
    protected $fillable = [
        'id',
        'survey_format_id',
        'country',
        'state',
        'email',
        'mobile',
        'message',
        'status',
        'chosen',
        'editor',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function editorInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor');
    }

    public function surveyFormatInfo(): BelongsTo
    {
        return $this->belongsTo(SurveyFormat::class, 'survey_format_id');
    }

    public function countryInfo(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function stateInfo(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function files(): HasMany
    {
        return $this->HasMany(File::class, 'model_id')->where('type', 'survey_post');
    }
}
