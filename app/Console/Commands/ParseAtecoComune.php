<?php

namespace App\Console\Commands;

use App\Models\AtecoCode;
use App\Models\AtecoComune;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Jenner\SimpleFork\Process;

class ParseAtecoComune extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:comune {action}';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $action = $this->argument('action');

        if ($action == "download") {

            $this->downloadData();
        } elseif ($action == 'parse') {

            $this->parseData();
        } elseif ($action == 'test') {
            $this->testData();
        }
        return Command::SUCCESS;


    }

    /**
     * @return array
     */
    private function getComuni(): array
    {
        return array_map('trim', file('data/prov'));
    }

    /**
     * @param $ateco
     * @param array $comuni
     * @return void
     */
    private function parseAteco($ateco, array $comuni): void
    {
        if (!file_exists('data/ateco-comuni/' . $ateco)) {
            mkdir('data/ateco-comuni/' . $ateco, 0777, true);
        }
        foreach ($comuni as $comune) {
            $url = "https://ateco.infocamere.it/ateq20/beServices/normativa.json?v=1653194484050&cc=$comune&id=$ateco";
//                $count = AtecoComune::query()->whereComune($comune)->whereCode($ateco)->count();
//                if( $count > 0 ){
//                    echo "$ateco - $comune: già presente" . PHP_EOL;
//                    continue;
//                }


            // file_get_contents($url);
            if (file_exists("data/ateco-comuni/$ateco/$comune")) {
                echo "data/ateco-comuni/$ateco/$comune Già presente\r\n";
                continue;
            } else {
                echo $url . PHP_EOL;
            }
            file_put_contents("data/ateco-comuni/$ateco/$comune", file_get_contents($url));
//                try {
//                    $data = file_get_contents($url);
//                } catch (\Exception $e) {
//                    continue 2;
//                }
//                DB::insert("INSERT INTO ateco_comune(code, comune, info)VALUES(?, ?, ?);",
//                    [$ateco, $comune, $data]);

        }
    }

    /**
     * @return void
     */
    private function downloadData(): void
    {
        $comuni = $this->getComuni();
        $atecos = $this->getAtecos();


        $pool = new \Jenner\SimpleFork\Pool();
        //$comuni = ["F205","H501"];
        //$atecos = ["01.41"];
        $tasks = [];
        foreach ($atecos as $ateco) {
            $pool->execute(new MyProcess($ateco, $comuni));
            sleep(1);
        }

        $pool->wait();

        echo "Finito\r\n";
    }

    private function parseData()
    {
        $atecos = $this->getAtecos();
        foreach ($atecos as $code) {
            $this->parseAtecoCode($code);
        }
    }

    /**
     * @return array
     */
    private function getAtecos(): array
    {
        $atecos = AtecoCode::query()//->limit(120)
        ->get()->map(function ($a) {
            return $a->code;
        })->toArray();
        return $atecos;
    }

    /**
     * @param $code
     * @return void
     */
    private function parseAtecoCode($code): void
    {
        echo "Parsing $code \r\n";
        $filesCode = collect(array_diff(scandir('data/ateco-comuni/' . $code), array('.', '..')));

        $files = $filesCode->map(function ($file) use ($code) {
            return json_decode(file_get_contents('data/ateco-comuni/' . $code . '/' . $file));
        })->flatMap(function ($item) {
            return $item->attivita;
        });

        $data = [];
        foreach ($files as $file) {
            $attivita = preg_replace("@\s{2,}@", " ", str_replace("\r\n", " ", strip_tags($file->descrizione)));
            foreach ($file->regioni as $regione) {
                $tipo = $regione->tipo;
                $luogo = ucwords(strtolower($regione->descrizione));
                $data[$attivita][$tipo][$luogo] = $regione;
            }
        }
        foreach ($data as $attivita => $subData) {
            foreach ($subData as $tipo => $subSubData) {
                foreach ($subSubData as $luogo => $content) {
                    //      echo "$attivita $tipo $luogo\r\n";
                    $sql = "INSERT OR IGNORE INTO attivita(code, attivita, livello, luogo, \"data\")VALUES(?, ?, ?, ?, ?);";
                    DB::insert($sql, [$code, $attivita, $tipo, $luogo, json_encode($content)]);


// DB::insert("INSERT INTO ateco_comune(code, comune, info)VALUES(?, ?, ?);",
//                    [$ateco, $comune, $data]);
                }
            }
        }
        // exit;
    }

    private function testData()
    {

        $atecos = $this->getAtecos();
        foreach ($atecos as $code) {
            echo "Codice ATECO $code\r\n";
            $filesCode = collect(array_diff(scandir('data/ateco-comuni/' . $code), array('.', '..')));

            $adempimentiStd = $filesCode->map(function ($file) use ($code) {
                return json_decode(file_get_contents('data/ateco-comuni/' . $code . '/' . $file));
            })->map(function ($item) {
                return $item->schedaAdempimentoStandard->listaAdempimenti;
            })->first();

            $sql = "INSERT INTO adempi_std(codice, \"data\")VALUES(?, ?)";

            dump($adempimentiStd);
        }
    }
}

class MyProcess extends Process {
    private $comuni;
    private $ateco;

    /**
     * @param $comuni
     * @param $ateco
     */
    public function __construct($ateco, $comuni)
    {
        parent::__construct();
        echo "Starting $ateco\r\n";
        $this->comuni = $comuni;
        $this->ateco = $ateco;
    }

    public function run()
    {
        echo "Downloading " . $this->ateco . PHP_EOL;
        $ateco = $this->ateco;
        if (!file_exists('data/ateco-comuni/' . $ateco)) {
            mkdir('data/ateco-comuni/' . $ateco, 0777, true);
        }
        foreach ($this->comuni as $comune) {
            $url = "https://ateco.infocamere.it/ateq20/beServices/normativa.json?v=1653194484050&cc=$comune&id=$ateco";

            // file_get_contents($url);
            if (file_exists("data/ateco-comuni/$ateco/$comune")) {
                //echo "data/ateco-comuni/$ateco/$comune Già presente\r\n";
                continue;
            } else {

            }
            try {
                $data = file_get_contents($url);
                echo $url . PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
            file_put_contents("data/ateco-comuni/$ateco/$comune", $data);

        }
        echo "End Downloading " . $this->ateco . PHP_EOL;
    }

    public function __destruct()
    {


        // echo "Finished " . $this->ateco . PHP_EOL;
    }

}
