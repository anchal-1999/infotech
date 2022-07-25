<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class userModel extends Model implements JWTSubject
{
    use HasFactory;
    protected $table = "users";
    protected $fillable = [
        "id",
        "f_name",
        "l_name",
        "role",
        "email",
        "password",
        "status",
        "is_deleted",
        "created_at",
        "updated_at"
    ];



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
