<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Traits\Congratulation;
use Illuminate\Console\Command;

class CongratulationsUsers extends Command
{

    use Congratulation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'congratulations:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar correos y emails a los fucionarios en sus cumpleaños';

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
            $this->congratulations();
            tenancy()->end();
        }
        $this->info('Felicitaciones enviadas exitosamente');
        return 0;

        
    }
}
