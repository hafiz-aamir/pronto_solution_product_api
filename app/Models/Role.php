<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory , LogsActivity;

    protected $fillable = [
        'uuid',
        'role',
        'auth_id',
    ];

    protected $hidden = [
        'id',
        'auth_id',
    ];
    

    protected static $recordEvents = ['created','updated','deleted'];
    
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Role') //Set custom log name
        ->logOnly(['uuid','role','auth_id','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Role {$eventName} successfully");

    }


    public function users()
    {
        return $this->hasMany(User::class);
    }
    

}
