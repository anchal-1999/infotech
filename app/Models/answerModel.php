<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class answerModel extends Model
{
    use HasFactory;

    protected $table = "answer";
    protected $fillable = [
        "id",
        "staffId",
        "questionid",
        "title",
        "question",
        "answer",
        "is_deleted",
        "creation_date",
        "updation_date",
        "created_at",
        "updated_at",
        "status"
    ];
}
