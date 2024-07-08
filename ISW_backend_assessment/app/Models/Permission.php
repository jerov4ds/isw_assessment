<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class  Permission extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code_name','display_name', 'uuid'];

    protected $hidden = ['id', 'deleted_at'];

    public function roles(){
        return $this->belongsToMany(Role::class);
    }
}
