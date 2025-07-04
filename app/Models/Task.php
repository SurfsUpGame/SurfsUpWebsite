<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'due_date',
        'created_by',
        'archived_at',
        'sprint_id',
        'epic_id',
        'priority',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due_date' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function epic()
    {
        return $this->belongsTo(Epic::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'task_labels');
    }

    public function votes()
    {
        return $this->hasMany(TaskVote::class);
    }

    public function upvotes()
    {
        return $this->votes()->where('vote', 1);
    }

    public function downvotes()
    {
        return $this->votes()->where('vote', -1);
    }

    public function getVoteScoreAttribute()
    {
        return $this->votes()->sum('vote');
    }

    public function getUpvoteCountAttribute()
    {
        return $this->upvotes()->count();
    }

    public function getDownvoteCountAttribute()
    {
        return $this->downvotes()->count();
    }

    public function getUserVoteAttribute()
    {
        if (!auth()->check()) {
            return null;
        }
        
        $vote = $this->votes()->where('user_id', auth()->id())->first();
        return $vote ? $vote->vote : null;
    }

    public function hasUserVoted($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->votes()->where('user_id', $userId)->exists();
    }
}
