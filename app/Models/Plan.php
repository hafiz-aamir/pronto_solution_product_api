<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Plan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'type',
        'amount',
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
        ->useLogName('Plan') // Set custom log name
        ->logOnly(['uuid', 'name', 'description', 'type', 'amount', 'status', 'auth_id','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Plan {$eventName} successfully");  

    }

}
