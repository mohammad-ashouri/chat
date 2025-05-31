<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyFormat extends Model
{
    protected $table = "survey_formats";
    protected $fillable = [
        'id',
        'survey_id',
        'name',
    ];

    public function surveyInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Survey::class, 'id', 'survey_id');
    }
}
