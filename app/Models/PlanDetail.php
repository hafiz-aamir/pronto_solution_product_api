<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class PlanDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'plan_details';

    protected $fillable = [
        'uuid',
        'name',
        'plan_id',
        'status',
        'auth_id'
    ];

    protected $hidden = [
        
        'id',
        'auth_id'

    ];


    protected static $recordEvents = ['created','updated','deleted'];

    
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Plan Detail') // Set custom log name
        ->logOnly(['uuid', 'name', 'plan_id', 'status', 'auth_id', 'created_at', 'updated_at', 'deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Plan Detail {$eventName} successfully"); 

    }


}
