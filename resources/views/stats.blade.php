@extends('layout')

@push('head')

    <meta name="description"
          content="Scopri l'andamento del numero di imprese registrate con il codice Ateco {{$ateco->code}} ">
    <title>Statistiche codice Ateco {{$ateco->code}}</title>
    <link rel="canonical" href="{{url()->current()}}"/>
@endpush

@section('content')
    <div class="mt-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">

                <li class="breadcrumb-item active" aria-current="page"><a href="/" title="Homepage"> <i class="fa-solid fa-home"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="{{url("/codice/" . $ateco->code)}}" title="Codice ATECO {{$ateco->code}}">
                        Codice ATECO {{$ateco->code}}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><small>Statistiche codice Ateco {{$ateco->code}}</small>
                </li>
            </ol>
        </nav>
    </div>
    <h1 class="text-danger">Statistiche codice Ateco {{$ateco->code}}</h1>

    <p>
    </p>
    <h2 class="text-primary"><i class="fa-solid fa-chart-pie"></i> Imprese registrate
        negli anni</h2>
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="d-flex">
                <canvas id="myChart" width="500" height="300"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <table class="table">
                <thead>
                <tr>
                    <th>Anno</th>
                    <th>Elementi</th>
                    <th>Variazione</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($italia as $key =>$value)
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{$value['value']}}</td>
                            <td>@if( isset($value['growth'])) {{$value['growth']}}% @else -- @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    <p>
        @php
            $less = $italia->first()['value'] > $italia->last()['value'];
            $growth = $italia->sortBy('growth')->filter(function($item) {
                return array_key_exists('growth', $item);
            });
            $delta = abs(round(($italia->last()['value'] - $italia->first()['value']) /$italia->first()['value'] * 100,1));
        @endphp
        I dati relativi alle imprese registrate con codice Ateco {{$ateco->code }} in Italia nel corso degli anni
        mostrano una
        tendenza generale di
        @if( $less) diminuzione. @else crescita. @endif<br>

        Nel {{$italia->keys()->first()}} le imprese registrate erano {{$italia->first()['value']}},
        mentre nel {{$italia->keys()->last()}} sono @if($less ) scese a @else aumentate fino @endif
        {{$italia->last()['value']}}, il che rappresenta una @if( $less) diminuzione @else crescita @endif
        del {{$delta}}% rispetto al primo anno preso in considerazione. <br><br>
        @if( $less && $growth->count() )
            Il picco di diminuzione tra gli anni è stato tra il {{$growth->keys()->first() - 1}} e
            il {{$growth->keys()->first()}},
            con un calo del {{$growth->first()['growth']}}% nel numero di imprese registrate con codice
            Ateco {{$ateco->code}} in Italia.
        @endif
        @if( !$less && $growth->count() )
            Il picco di crescita tra gli anni è stato tra il {{$growth->keys()->last() - 1}} e
            il {{$growth->keys()->last()}},
            con un aumento del {{$growth->last()['growth']}}% nel numero di imprese registrate con codice
            Ateco {{$ateco->code}} in Italia.
        @endif

        {{--        Analizzando gli anni intermedi, si può notare una costante diminuzione, con picchi di calo maggiore negli anni 2013 e 2014.--}}
        {{--        Nonostante la tendenza generale sia negativa, ci sono stati anche anni in cui la diminuzione è stata più--}}
        {{--        contenuta, come nel 2019, con un calo dell'1,7% rispetto all'anno precedente. In generale, i dati mostrano una--}}
        {{--        tendenza di calo costante nel numero delle imprese registrate con codice Ateco 41.2 in Italia.--}}
    </p>

    <script>
        // Crea un elemento canvas per il grafico

        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($italia->keys()) !!},
                    datasets: [{
                        label: 'Imprese registrate in Italia',
                        data: {!! json_encode($italia->values()->map(function($i){return $i['value'];})) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
