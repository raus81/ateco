<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtecoComune extends Model
{
    use HasFactory;
    protected $table = 'ateco_comune';

    protected $casts = [
        'info' => AsCollection::class,
    ];
}
