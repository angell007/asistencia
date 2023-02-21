<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Traits\EncuestaUpdate;
use Illuminate\Console\Command;

class UpdateEncuestas extends Command
{

    use EncuestaUpdate;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:encuestas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron job para actualizar fechas de encuestas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);
            $this->encuestas();
            tenancy()->end();
        }

        $this->info('Encuestas actualizadas exitosamente');
        return 0;
    }
}
