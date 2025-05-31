<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use SoftDeletes;

    protected $table = "contact_us";
    protected $fillable = [
        'id',
        'name',
        'email',
        'mobile',
        'message',
        'headers',
        'ip_address',
        'status',
        'seconder',
    ];

    protected $hidden = [
        'id',
        'name',
        'email',
        'mobile',
        'message',
        'headers',
        'ip_address',
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
