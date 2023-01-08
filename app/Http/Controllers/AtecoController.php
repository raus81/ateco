<?php

namespace App\Http\Controllers;

use App\Models\AtecoCode;
use App\Models\AtecoComune;
use App\Models\Attivita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AtecoController extends Controller {

    public function aboutUs(){
        return view('about-us');
    }

    public function testCode($code)
    {
        dump(getcwd());
        $filesCode = collect(array_diff(scandir('../data/ateco-comuni/' . $code), array('.', '..')));

        $files = $filesCode->map(function ($file) use ($code) {
            return json_decode(file_get_contents('../data/ateco-comuni/' . $code . '/' . $file));
        })->flatMap(function ($item) {
            return $item->attivita;
        });

        $adempimentiStandard = $filesCode->map(function ($file) use ($code) {
            return json_decode(file_get_contents('../data/ateco-comuni/' . $code . '/' . $file));
        })->map(function ($item) {
            return $item->schedaAdempimentoStandard;
        })->groupBy('tipologiaNorma');

        $data = [];
        foreach ($files as $file) {
            $attivita = $file->descrizione;
            //$data[$attivita] = $file;
            foreach ($file->regioni as $regione) {
                $tipo = $regione->tipo;
                $luogo = $regione->descrizione;
                $data[$attivita][$tipo][$luogo] = $regione;
            }
        }
        dump($adempimentiStandard);
        dump($data);

    }

    public function showImage($code)
    {
        $ateco = AtecoCode::query()->whereCode($code)->first();

        $array = explode( "\n", wordwrap( strtoupper($ateco->nome), 40));
        //dd( $array);

        $data = Storage::get('template.svg');
        $data = str_replace("#COD#", $code,  $data);
        for( $i = 0; $i < 3; $i++ ){
            $data = str_replace("#LINE$i#",$array[$i]??"", $data);
        }

        return response($data)->header('Content-Type', 'image/svg+xml');

    }

    public function showCode($code)
    {
//        $json = collect($this->getJson());
//        dump($code);
//
//        $hierarchy = collect([]);
//        $item = $this->search($code, $json, $hierarchy);
//        dump($item);
//        dump($hierarchy);

        $ateco = AtecoCode::query()->whereCode($code)->first();
        $children = AtecoCode::query()->whereParent($code)->get();
        // dump($ateco);
        $info = $ateco->info;

        $adempimenti = Attivita::query()->whereCode($code)->get()->groupBy(['attivita', 'livello']);


        $sql = "   WITH RECURSIVE pp(n) AS (VALUES('$code') UNION SELECT parent FROM ateco, pp WHERE ateco.code=pp.n )
    select code, nome from ateco where code in (select * from pp)  AND code != '$code'
    order by LENGTH(code) asc";

        $breadcrumb = DB::select(DB::raw($sql));


        //dump( $atecoComune);
//
//        echo $info['noteInclusioneHtml'];
        return view('code', ['info' => $info, 'ateco' => $ateco, 'children' => $children,
            'adempimenti' => $adempimenti,
            'bc' => $breadcrumb]);
    }

    public function home()
    {

        $mc = AtecoCode::query()->whereParent("")->orderBy("code")->get();

        //$json = $this->getJson();

        //dump($json);
        return view('home', ['mc' => $mc]);
    }


    public function suggester(Request $request)
    {
        $params = $request->all();
        if (!isset($params['search'])) {
            return [];
        }
        $search = $params['search'];
        return AtecoCode::query()->where(
            [['code', 'like', $search . '%']]
        )->orWhere([['nome', 'like', '%' . $search . '%']])
            ->orderBy(DB::raw('LENGTH("search") '))
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['nome' => $item->code . ' ' . $item->nome, 'url' => url('codice/' . $item->code)];
            });
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

    private function search($code, \Illuminate\Support\Collection $json, \Illuminate\Support\Collection $h)
    {

        foreach ($json as $item) {
            $id = trim($item->id);
            //dump($id ." - ". $code);
            if ($id == $code) {
                return $item;
            } elseif (isset($item->orderedChildren)) {

                $found = $this->search($code, collect($item->orderedChildren), $h);
                if ($found) {
                    $h->add($item);
                    return $found;
                }


            }
        }
        return null;
    }

    //
    //WITH RECURSIVE pp(n) AS (VALUES('74.90.9') UNION SELECT parent FROM ateco, pp WHERE ateco.code=pp.n )
    //select * from ateco where code in (select * from pp)
}
