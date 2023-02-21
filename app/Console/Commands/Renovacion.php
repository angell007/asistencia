<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\Tenant;
use Illuminate\Console\Command;
use App\Traits\RenovacionContrato;
use Illuminate\Support\Facades\Config;

class Renovacion extends Command
{

    use RenovacionContrato;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renovacion:contratos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cron job para renovar contratos';

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
            $this->renovaciones();
            tenancy()->end();
        }

        $this->info('Contratos renovados exitosamente');
        return 0;
    }
}
