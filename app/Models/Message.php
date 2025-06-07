<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jalalian;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_system',
        'original_message_id',
        'original_sender_id',
        'reply_to_id'
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function originalMessage()
    {
        return $this->belongsTo(Message::class, 'original_message_id');
    }

    public function originalSender()
    {
        return $this->belongsTo(User::class, 'original_sender_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class, 'message_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    public function deletedBy()
    {
        return $this->belongsToMany(User::class, 'deleted_messages')
            ->withTimestamps();
    }

    public function isDeletedForUser($userId)
    {
        return $this->deletedBy()->where('user_id', $userId)->exists();
    }

    public function markAsDeletedForUser($userId)
    {
        $this->deletedBy()->attach($userId);
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

    public function getJalaliCreatedAtAttribute()
    {
        return \Morilog\Jalali\Jalalian::fromDateTime($this->created_at)->format('H:i');
    }
}
