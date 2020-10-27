<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $with = ['category'];
    protected $appends = ['user_hobby'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_number',
        'profile_pic',
        'category_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hobbies(){
        return $this->hasMany(Hobby::class, 'user_id', 'id');
    }

    public function category(){
       return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getUserHobbyAttribute(){
        $hobby_array = array();
       foreach ($this->hobbies as $hobby){
           array_push($hobby_array, $hobby->name);
       }
        return implode(', ', $hobby_array);
//        return implode()
    }
}
