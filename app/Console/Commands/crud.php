<?php

namespace App\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;

class crud extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:crud {pagename} {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold CRUD files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $crudcomplete = false;
        //Generate migration for tablename
        $this->info('Creating scaffold for CRUD operation');
        $crudcomplete = $this->createform();
        $crudcomplete ? $this->info('Scaffolding complete') : $this->error('Scaffolding failed');
        // Artisan::call('')
    }

    public function migration() {
        Artisan::call('make:model', array('name' => $this->argument('tablename'), '-m' => 'default'));
        $this->info('Migration file generated');
        return true;
    }

    public function createform() {
        $createformreturn = true;
        $pagename         = $this->argument('pagename');
        $tablename        = $this->argument('tablename');

        //Check if migration exists
        $allmigrationlist = \scandir('database/migrations');
        foreach ($allmigrationlist as $key => $index) {
            if (strpos($index, "create_" . $tablename . "s_table.php") !== false) {
                $this->error("Migration file {$index} already exists!");
                $createformreturn = false;
            }
        }

        //Check if controller exists
        $controllerpath = $this->controllerpath($pagename);
        if (File::exists($controllerpath)) { //check if controller already exists
            $this->error("Controller file {$controllerpath} already exists!");
            $createformreturn = false;
        }

        //Check if blade exists
        $viewpath = $this->viewPath($pagename);
        if (File::exists($viewpath)) { //Check if blade exists
            $this->error("Blade file {$viewpath} already exists!");
            $createformreturn = false;
        }

        //Check if js exists
        $jspath = $this->jspath($pagename);
        if (File::exists($jspath)) { //check if controller already exists
            $this->error("Javascript file {$jspath} already exists!");
            $createformreturn = false;
        }

        if ($createformreturn) {
            //Create migration
            $this->migration();

            //Create controller
            Artisan::call('make:controller', array('name' => $this->argument('pagename') . 'Controller', '--api' => 'default'));
            $this->info('Controller creation complete');

            //Create Blade
            File::put($viewpath, '');
            $this->info('Blade creation complete');

            //Create JS
            File::put($jspath, '');
            $this->info('JS creation complete');

            $file = ('resources/js/app.js');
            file_put_contents($file, "require('./" . $pagename . "');\n" . file_get_contents($file));
        }

        return $createformreturn;
    }

    /**
     * Get the view full path.
     *
     * @param string $view
     *
     * @return string
     */
    public function viewPath($name) {
        $view = str_replace('.', '/', $name) . '.blade.php';
        $path = "resources/views/{$view}";
        return $path;
    }

    /**
     * Get the controller full path.
     *
     * @param string $view
     *
     * @return string
     */
    public function controllerpath($name) {
        $controller = str_replace('.', '/', $name) . 'Controller.php';
        $path       = "app/Http/Controllers/{$controller}";
        return $path;
    }

    public function jspath($name) {
        $js   = str_replace('.', '/', $name) . '.js';
        $path = "resources/js/{$js}";
        return $path;
    }
}
