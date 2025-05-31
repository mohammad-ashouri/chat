<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_group'
    ];

    protected $casts = [
        'is_group' => 'boolean'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->messages()->latest()->first();
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
}
