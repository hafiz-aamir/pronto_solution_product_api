<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , LogsActivity;

    // protected static $logAttributes = ['*'];
    protected static $recordEvents = ['created','updated','deleted'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [

        'uuid',
        'first_name',
        'last_name',
        'email',
        'role_id',
        'auth_id',
        'ip',
        'image'

    ];

    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()
        ->useLogName('User') // Set custom log name
        ->logOnly(['uuid','first_name','last_name','email','role_id','auth_id','ip','image','created_at','updated_at','deleted_at'])
        ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName} Successfully"); 
        
    } 


    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    protected $hidden = [
        
        'password',
        'remember_token',
        'id',
        'ip',
        'email_verified_at',
        'auth_id'

    ];
    

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



}
