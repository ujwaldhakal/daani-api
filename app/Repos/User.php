<?php

namespace App\Repos;

use App\Repos\Media;
use App\Services\Mail\Interfaces\CanEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone_number',
        'fb_id',
        'api_token',
        'email_verified_at',
        'district_id',
        'requested_verification_link_count'
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


    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    public function scopeByToken($q,$token)
    {
        return $q->where('api_token',$token);
    }



    public function isVerified() : bool
    {
        return !empty($this->email_verified_at);
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function scopeByFbId($q,$fbId)
    {
        return $q->where('fb_id', $fbId);
    }

    public function scopeByEmail($q,$email)
    {
        return $q->where('email', $email);
    }

    public function generateAuthToken() : String
    {
        return Str::random(20);
    }

    public function completeSuccessfulLogin() : User
    {
        $user = Auth::user();
        $user->api_token = $this->generateAuthToken();
        $user->save();

        return $user;

    }

    public function canRequestForEmailVerificationLink() : bool
    {
        return $this->getRequestedVerificationLinkCount() < 3;
    }

    public function getRequestedVerificationLinkCount() : int
    {
        return $this->requested_verification_link_count;
    }


    public function products()
    {
        return $this->hasMany(Product::class,'user_id','id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'subject');
    }

}
