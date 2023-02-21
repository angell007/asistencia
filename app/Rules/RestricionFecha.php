<?php

namespace App\Rules;

use App\Models\Novedad;
use App\Traits\validateDates;
use Illuminate\Contracts\Validation\Rule;

class RestricionFecha implements Rule
{
    use validateDates;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $novedad = Novedad::where('funcionario_id', $this->data['funcionario_id'])->latest()->first();

        if ($novedad == null) {
            return true;
        }

        $this->fecha_inicio = $novedad->fecha_inicio;
        $this->fecha_fin =  $novedad->fecha_fin;

        if ($this->ComparateDates($this->data['fecha_inicio'])) {
            return false;
        }
        if ($this->ComparateDates($this->data['fecha_fin'])) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
