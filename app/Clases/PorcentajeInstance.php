<?php

namespace App\Clases;

use InvalidArgumentException;

class PorcentajeInstance
{
    public $objeParams;
    const PORCENTAJE_AL100 = 3;

    function __construct($objeParams)
    {
        $this->objeParams = $objeParams;
        $this->getSalario();
    }

    public function getSalario()
    {

        if ($this->objeParams->novedad->suma) {
            $this->objeParams->porcentaje = self::PORCENTAJE_AL100;
        }

        switch ($this->objeParams->porcentaje) {
            case 1:
                return new Del100Al66($this->objeParams);
                break;
            case 2:
                return new Al66($this->objeParams);
                break;
            case 3:
                return new Al100($this->objeParams);
                break;
            default:
                throw new InvalidArgumentException('No existe este porcentaje de pago');
                break;
        }
    }
}
