<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CodeService {


    function getStatsByCode($code)
    {
        $lastKey = null;
        $plainCode = str_replace('.', '', $code);

        $data = collect([]);
        if (Storage::exists('stats/' . $plainCode . '.json')) {
            $filtered = collect(json_decode(Storage::get('stats/' . $plainCode . '.json')))
                ->filter(function ($v) {
                    return isset($v->Z);
                })->mapWithKeys(function ($v, $k) {
                    return [$k => $v->Z];
                });

            $lastKey = $filtered->keys()->last();
            if ($lastKey != null) {
                $items = collect($filtered[$lastKey]);
                $total = $items->sum();
                $remainder = 100;
                foreach ($items as $key => $value) {
                    $percentage = round(($value / $total) * 100, 1);

                    if ($items->last() == $value) {
                        $percentage = $remainder;
                    } else {
                        $remainder -= $percentage;
                    }
                    $items[$key] = round($percentage, 1);
                    $data[$key] = collect(['items' => $value, 'percentage' => round($percentage, 1)]);
                }
            }
        }
        return $data->isEmpty() ? null : collect(['year' => $lastKey, 'data' => $data]);
    }
}
