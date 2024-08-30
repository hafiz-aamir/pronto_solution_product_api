<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// use Illuminate\Database\Eloquent\SoftDeletes;

 
class Permission_assign extends Model 
{
    use HasFactory , LogsActivity;

    protected $fillable = [
        'uuid',
        'role_id',
        'permission_id', 
        'menu_id', 
        'auth_id',  
    ];

    
    protected $hidden = [
        
        'id',
        'auth_id',
        'status',
        'role_id',
        'permission_id',
        'menu_id'

    ];

    protected static $recordEvents = ['created','updated','deleted'];

    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Permission Assign') // Set custom log name
        ->logOnly(['uuid','role_id','permission_id','menu_id','auth_id','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Permission Assign {$eventName} successfully");  

    }


}
