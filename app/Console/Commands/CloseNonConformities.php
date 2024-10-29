<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NoConformidadSinRecepcionController;

class CloseNonConformities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CloseNonConformities';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes Non Conformities';

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
     * @return int
     */
    public function handle()
    {
        $this->NoConformidadSinRecepcionController->markNonConformanceClosed();
    }
}
