<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class questionModel extends Model
{
    use HasFactory;
    protected $table = "question";
    protected $fillable = [
        "id",
        "title",
        "description",
        "q_type",
        "status",
        "creation_date",
        "updation_date",
        "created_at",
        "updated_at",
        "option"
    ];


}
