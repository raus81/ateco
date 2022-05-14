<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ParseAteco extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:parse-ateco';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    private function getJson()
    {
        $fileJson = "elenco-attivita.json";


        $data = Storage::get($fileJson);
        $json = json_decode($data);
        return $json;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $json = $this->getJson();
        // print_r($json);
        $this->parseJson($json, null);
    }


    /**
     * @param $json
     * @return void
     */
    private function parseJson($json, $parent): void
    {
        foreach ($json as $level) {
            $code = $level->id;
            $nome = $level->nome;
            $categoria = $level->type;

            echo ($parent ?? "") . ":" . $level->id . PHP_EOL;

            $data = collect($level);

            if (isset($level->orderedChildren)) {
                $this->parseJson($level->orderedChildren, $level->id);
            }


            $level->orderedChildren = null;
            unset($level->orderedChildren);
            unset($level->nome);
            unset($level->type);

            $data = json_encode($level);


            DB::insert("INSERT OR IGNORE INTO ateco(code, parent, nome, tipo, info) VALUES(?, ?, ?, ?, ?);",
                [trim($code), trim($parent), trim($nome), trim($categoria), $data]);

            // echo $data . PHP_EOL;
        }
    }
}
