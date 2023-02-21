<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Traits\Encuesta;
use Illuminate\Console\Command;

class Encuestas extends Command
{

    use Encuesta;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encuestas:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'enviar encuestas ';

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
        $this->info('Encuestas exitosamente');
        return 0;
    }
}
