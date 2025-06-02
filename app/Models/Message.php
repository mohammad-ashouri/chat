<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_system'
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->readBy()->attach(auth()->id(), ['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return $this->readBy()->where('user_id', auth()->id())->exists();
    }
}
