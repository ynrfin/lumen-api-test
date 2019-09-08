<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $perPage = 10;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_domain', 
        'object_id', 
        'description',
        'is_completed', 
        'completed_at',
        'updated_by',
        'updated_at',
        'created_at',
        'due',
        'urgency'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
