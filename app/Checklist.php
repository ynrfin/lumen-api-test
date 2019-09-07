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
        'object_domain', 'object_id', 'description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
