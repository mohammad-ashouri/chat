<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_group',
        'user_id'
    ];

    protected $casts = [
        'is_group' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GroupHistory::class);
    }

    public function isGroup(): bool
    {
        return (bool)$this->is_group;
    }

    public function otherUser()
    {
        if (!$this->is_group) {
            return $this->users()->where('users.id', '!=', auth()->id())->first();
        }
        return null;
    }

    public function unreadMessagesCount()
    {
        return $this->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereDoesntHave('readBy', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->count();
    }
}
