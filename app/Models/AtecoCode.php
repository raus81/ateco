<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtecoCode extends Model {
    use HasFactory;

    protected $table = 'ateco';
    protected $casts = [
        'info' => AsCollection::class,
    ];

    public function isCategoria(): bool
    {
        return $this->tipo === 'categoria';
    }



    public function getNota():string{


        return  $this->hasNota() ? implode(PHP_EOL,$this->info['nota']) : '';
    }

    /**
     * @return bool
     */
    public function hasNota(): bool
    {
        return isset($this->info['nota']);
    }
}
