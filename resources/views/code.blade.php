@extends('layout')

@push('head')

    <meta name="description"
          content="Scopri tutte le informazioni sul codice Ateco {{$ateco->code}} - {{$ateco->nome}}">
    <title>Codice ateco {{$ateco->code}} </title>
    <link rel="canonical" href="{{url("/codice/" .$ateco->code)}}"/>
@endpush
@section('content')
    <div class="mt-2">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach( $bc as $b)
                    <li class="breadcrumb-item">
                        <a class="" href="{{$b->code}}"><small>{{$b->code}} - {{$b->nome}}</small></a></li>
                @endforeach
                <li class="breadcrumb-item active" aria-current="page"><small>{{$ateco->code }} -
                        <small>{{$ateco->nome}}</small></small></li>
            </ol>
        </nav>
    </div>
    <h1 class="text-danger">{{$ateco->code }} - {{$ateco->nome}}</h1>
    <small> Codice ateco {{$ateco->code }} - {{$ateco->nome}} </small>

    @if(isset($info['noteInclusioneHtml']) && !empty($info['noteInclusioneHtml']))
        <h2 class="text-primary"><small><i class="small fa-solid fa-list-check"></i></small> Attività incluse</h2>
        <div class="ateco-inclusione">{!! $info['noteInclusioneHtml'] !!}</div>

    @endisset
    @if(isset($info['noteEsclusioneHtml']) && !empty($info['noteEsclusioneHtml']))

        <h2 class="text-primary text-danger"><small><i class="fa-solid fa-circle-exclamation"></i></small> Attività
            escluse</h2>


        <div class="ateco-esclusione">{!! $info['noteEsclusioneHtml'] !!}</div>

    @endisset
    @if($ateco->isCategoria() )

        @if( $ateco->hasNota())
            {!! $ateco->getNota() !!}
        @endif

    @endif
    @if( count($children ))
        <h2 class="text-primary "><small><i class="fa-solid fa-list"></i></small> Codici collegati</h2>
        <ul class="list-unstyled">
            @foreach($children as $child)
                <li><a href="{{url("/codice/" . $child->code)}}">{{$child->code}} - {{$child->nome}}</a></li>
            @endforeach
        </ul>
    @endif
    @if(count($adempimenti))
        <h2 class="text-primary"><small><i class="fa-solid fa-scale-balanced"></i></small> Adempimenti</h2>
        <div class="accordion accordion-flush " id="accordionFlushExample">

            @foreach( $adempimenti as $attività => $data )
                <div class="accordion-item">
                    <h3 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-{{$loop->index}}" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                            {{$attività}}
                        </button>
                    </h3>
                    <div id="flush-{{$loop->index}}" class="accordion-collapse collapse show"
                         aria-labelledby="flush-headingOne"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            @if(isset( $data['NAZIONALE']))
                                <div class="d-flex justify-content-start align-items-center"><img
                                        class="italy-icon   me-2" height="65" width="50"
                                        src="/imgs/italy.svg"/>
                                    <h4>
                                        Normativa
                                        nazionale</h4></div>
                                @foreach( $data['NAZIONALE'] AS $adempimento)
                                    @include('adempimento',['adempimento' => $adempimento->data])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    @if(isset($stats))
        @php
            $sorted = $stats['data']->sortByDesc('percentage');
             $topZone = $sorted->keys()->first();
        @endphp

        <h2 class="text-primary"><i class="fa-solid fa-chart-pie"></i> Distribuzione del codice {{$ateco->code }} nelle
            zone d'Italia</h2>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="d-flex">
                    <canvas id="myChart" width="500" height="200"></canvas>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Percentuale</th>
                        <th>Elementi</th>
                    </tr>
                    </thead>
                    @foreach($stats['data'] as $key =>$value)
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{$value['percentage']}}%</td>
                            <td>{{$value['items']}}</td>
                        </tr>


                    @endforeach
                </table>
            </div>
            <div class="col-12">
                <p>
                    Il codice ateco {{$ateco->code}}, relativo a <em>{{strtolower($ateco->nome)}}</em>, è maggiormente
                    presente nelle
                    regioni
                    @if($topZone != 'Isole')
                        delle
                    @else
                        del
                    @endif
                    {{$topZone}} con una percentuale di {{$sorted->first()['percentage']}}%.
                    Seguono poi:
                    @foreach($sorted->skip(1) as $key => $value)
                        @if($loop->last)
                            e infine
                        @endif
                        {{$key}} con {{$value['percentage']}}%@if(! $loop->last), @endif
                    @endforeach.
                </p>
            </div>
        </div>


        <script>
            // Crea un elemento canvas per il grafico

            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('myChart').getContext('2d');

                // Dati per il grafico
                const data = {
                    labels: {!!  json_encode($stats['data']->keys()->toArray() )!!},
                    datasets: [{
                        label: '# di attività',
                        data: {!!json_encode($stats['data']->map(function($item){return $item['items'];})->values())!!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                };

                // Opzioni per il grafico
                const options = {
                    responsive: true,
                    maintainAspectRatio: false
                };
                //Crea il grafico
                const myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: options
                });
            });

        </script>


    @endif

    <div class="w-100 mt-2">
        <img width="1024" height="449" alt="CODICE ATECO {{$ateco->code}} - {{$ateco->nome}}" class=" img-fluid"
             src="/immagini/svg/{{$ateco->code}}" style="border-radius: 10px; max-width: 1024px;width: 100%;"/>
    </div>



@endsection
