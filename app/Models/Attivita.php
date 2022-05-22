<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attivita extends Model
{
    use HasFactory;
    protected $table = 'attivita';

    protected $casts = [
        'data' => AsCollection::class,
    ];
}
