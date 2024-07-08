<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'status', 'reporting_to', 'created_by', 'uuid'];

    protected $hidden = ['id', 'deleted_at'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

}
