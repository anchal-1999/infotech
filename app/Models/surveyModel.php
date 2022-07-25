<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surveyModel extends Model
{
    use HasFactory;

    protected $table = "survey";
    protected $fillable = [
        "id",
        "title",
        "description",
        "status",
        "is_deleted",
        "creation_date",
        "updation_date",
        "created_at",
        "updated_at"
    ];
}
