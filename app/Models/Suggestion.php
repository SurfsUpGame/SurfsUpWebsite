<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'upvotes',
        'downvotes',
        'score',
        'converted_to_task',
        'task_id',
    ];

    protected $casts = [
        'converted_to_task' => 'boolean',
    ];

    protected $appends = ['user_vote'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(RoadmapTask::class, 'task_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(SuggestionVote::class);
    }

    public function getUserVoteAttribute()
    {
        if (!auth()->check()) {
            return null;
        }

        $vote = $this->votes()->where('user_id', auth()->id())->first();
        return $vote ? $vote->vote : null;
    }

    public function updateScore()
    {
        $this->score = $this->upvotes - $this->downvotes;
        $this->save();
    }
}