<?php

namespace App\Console\Commands;

use App\Http\Controllers\NoConformidadSinRecepcionController;
use Illuminate\Console\Command;

class SendNonConformities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendNonConformities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $NoConformidadSinRecepcionController;
    public function __construct(NoConformidadSinRecepcionController  $NoConformidadSinRecepcionController )
    {
        parent::__construct();
        $this->NoConformidadSinRecepcionController = $NoConformidadSinRecepcionController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->NoConformidadSinRecepcionController->sendNotificationEmail();
    }
}
