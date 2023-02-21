<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Festivo extends Model
{
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    protected $table = 'festivo';

    protected $appends = ['fecha_original'];

    public function getFechaAttribute()
    {
        $partialDate = Carbon::parse($this->attributes['fecha'])->toFormattedDateString();
        $porciones = explode(" ", $partialDate);
        return  $this->getMonth($porciones[0]) . ' ' . str_replace(",", "", $porciones[1]);
    }
    public function getFechaOriginalAttribute()
    {
        return  Carbon::parse($this->attributes['fecha']);
    }

    public function getDay($day)
    {
        str_replace("", "", $day);
    }

    public function getMonth($mes)
    {
        switch ($mes) {
            case 'Jan':
                return ' enero ';
                break;
            case 'Feb':
                return ' febrero ';
                break;
            case 'Mar':
                return ' marzo ';
                break;
            case 'Apr':
                return ' abril ';
                break;
            case 'May':
                return ' mayo ';
                break;
            case 'Jun':
                return ' junio ';
                break;
            case 'Jul':
                return ' julio ';
                break;
            case 'Aug':
                return ' agosto ';
                break;
            case 'Sep':
                return ' septiembre ';
                break;
            case 'Oct':
                return ' octubre ';
                break;
            case 'Nov':
                return ' noviembre ';
                break;
            case 'Dec':
                return ' diciembre ';
                break;
        }
    }
}
