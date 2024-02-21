<?php

namespace App\Console\Commands\System;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class ScoutControl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scout-control
        {model : Model name without prefix App\\Models}
        {action : action, can be index,re-index,delete}
        {--range= : id range}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parts index control. Actions :
     - delete : delete index
     - re-index : delete and re-create index then import
     - flush : delete document in index
     - import : import document to index
    ';

    protected $model = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->model = 'App\\Models\\' . $this->argument('model');
        $action = $this->argument('action');

        $pipe = match ($action) {
            're-index' => ['delete', 'import'],
            'flush' => ['flush'],
            'import' => ['import'],
            'delete' => ['delete'],
            default => throw new \Exception("Action not supported : " . $action),
        };

        foreach ($pipe as $action) {
            $this->{$action . 'Index'}();
        }

        return Command::SUCCESS;
    }

    protected function flushIndex()
    {
        $this->call('scout:flush ' . $this->model);
    }

    protected function importIndex()
    {
        $this->call("scout:import $this->model");
    }

    protected function  deleteIndex()
    {
        try {
            if(app(Client::class)->indices()->existsAlias(['name' => $this->get_index_name()])->asBool()){
                $result = app(Client::class)->indices()->getAlias(['name' => $this->get_index_name()]);
                $result = $result->asArray();
                if(count($result)){
                    foreach ($result as $index => $value){
                        app(Client::class)->indices()->delete(['index' => $index]);
                        $this->warn("Deleted index " . $index);
                        break;
                    }
                }
            } elseif (app(Client::class)->indices()->exists(['index' => $this->get_index_name()])->asBool()) {
                app(Client::class)->indices()->delete(['index' => $this->get_index_name()]);
                $this->warn("Deleted index " . $this->get_index_name());
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }
    }

    protected function get_index_name(){
        return match ($this->model){
            Song::class => config('scout.prefix') . 'songs',
            Album::class => config('scout.prefix') . 'albums',
            Artist::class => config('scout.prefix') . 'artists',
        };
    }
}
