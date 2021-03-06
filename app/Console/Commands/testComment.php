<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:action {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

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
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        switch ($action){
            case 'start':
                $this->actionStart();
        }
    }

    private function actionStart()
    {
        echo 'start';
    }
}
