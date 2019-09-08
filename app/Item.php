<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $perPage = 10;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 
        'is_completed', 
        'completed_at',
        'due',
        'urgency',
        'updated_by',
        'assignee_id',
        'task_id',
        'checklist_id',
        'created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * parent checklist relationship
     *
     * @return Checklist
     */
    public function checklist()
    {
        return $this->belongsTo('App\Checklist');
    }
    
}
