<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frase extends Model
{
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    protected $table = 'frase';

}
