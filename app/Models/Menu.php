<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;


class Menu extends Model
{
    
    use HasFactory , LogsActivity, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'sort_id',
        'icon',
        'auth_id',
        'parent_id'
    ];

    protected static $recordEvents = ['created','updated','deleted'];
    
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('Menu') //Set custom log name
        ->logOnly(['uuid','name','sort_id','icon','auth_id','parent_id','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "Menu {$eventName} successfully");
        
    } 


}
