<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtecoController extends Controller {
    public function showCode($code)
    {
        $json = collect($this->getJson());
        dump($code);

        $hierarchy = collect([]);
        $item = $this->search($code, $json, $hierarchy);
        dump($item);
        dump($hierarchy);
    }

    public function home()
    {

        $json = $this->getJson();

        dump($json);
        return "ciao";
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

    private function search($code, \Illuminate\Support\Collection $json, \Illuminate\Support\Collection$h)
    {

        foreach ($json as $item) {
            $id = trim($item->id);
            //dump($id ." - ". $code);
            if ($id == $code) {
                return $item;
            } elseif (isset($item->orderedChildren)) {

                $found = $this->search($code, collect($item->orderedChildren),$h);
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
