<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = [
        'nombre',
        'descripcion',
        'icon'
    ];

    public function submenus()
    {
        return $this->hasMany(subMenu::class);
    }
}
