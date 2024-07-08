<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $hidden = ['id', 'deleted_at'];
    protected $fillable = ['post_id', 'user_id', 'attachment', 'comment', 'uuid'];


    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}

