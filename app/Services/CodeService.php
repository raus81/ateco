<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CodeService {

    function getFullStatsByCode($code)
    {
        $lastKey = null;
        $plainCode = str_replace('.', '', $code);

        $data = collect([]);
        if (!Storage::exists('stats/' . $plainCode . '.json')) {
            return null;
        }
        $data = collect(json_decode(Storage::get('stats/' . $plainCode . '.json')));

        $zones = $data->filter(function ($v) {
            return isset($v->Z);
        })->mapWithKeys(function ($v, $k) {
            return [$k => $v->Z];
        });
        $zoneValue = [];
        foreach ($zones as $year => $zones) {
            foreach ($zones as $zone => $value) {
                $zoneValue[$zone][$year] = $value;
            }
        }
         $italia = $data->mapWithKeys(function ($v, $k) {
            return [$k => ['value' => $v->I->Italia]];
        })->toArray();

        $previous_year = null;

        foreach ($italia as $year => $year_data) {
            if ($previous_year !== null) {
                $diff = ($year_data['value'] - $previous_year['value']) / $previous_year['value'] * 100;
                $italia[$year]['growth'] = round($diff, 2);
            }
            $previous_year = $year_data;
        }
        return ['italia' => collect($italia), 'zone' => collect($zoneValue)];
    }

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
