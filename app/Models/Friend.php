<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Friend extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'userid_1',
        'userid_2',
    ];

    protected $table = 'friends_request';
}
