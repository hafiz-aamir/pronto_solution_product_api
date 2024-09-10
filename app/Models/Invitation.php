<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Invitation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'invitations';

    protected $fillable = [
        'uuid',
        'auth_id',
        'team_email',
        'team_role',
        'status'
    ];

    protected $hidden = [
        
        'id',
        'auth_id'

    ];


    protected static $recordEvents = ['created','updated','deleted'];

    
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Invitation') // Set custom log name
        ->logOnly(['uuid', 'auth_id', 'team_email', 'team_role', 'status', 'created_at', 'updated_at', 'deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Invitation {$eventName} successfully");

    }


}
