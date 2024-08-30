<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Permission extends Model
{
    use HasFactory , LogsActivity;

    protected $fillable = [
        'uuid',
        'permission',
        'auth_id',
    ];

    protected $hidden = [
        
        'id',
        'auth_id',
        'status',

    ];

    protected static $recordEvents = ['created','updated','deleted'];

    
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Permission') // Set custom log name
        ->logOnly(['uuid','permission','auth_id','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Permission {$eventName} successfully");  

    }

}
