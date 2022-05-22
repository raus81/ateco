@foreach( $adempimento['documentazione'] as $data )

    @php
        $normativa =  $data['normativa'][0] ?? null;
        $modTrasmissione = $normativa['modalitaTrasmissione'] ?? null;
        //dump($modTrasmissione);
    @endphp

    <div class="card mb-2">
        <div class="card-header">
            <h5 class="text-danger">
                @if( $modTrasmissione == 'SUAP')
                    SUAP
                @elseif ($modTrasmissione == 'COMUNICA')
                    COMUNICAZIONE UNICA D'IMPRESA
                @elseif ($modTrasmissione == 'ALTRI ADEMPIMENTI')
                    ALTRI ADEMPIMENTI
                @elseif ($modTrasmissione == 'NESSUNA')
                @endif
                <br><small class="text-black-50">@if( $modTrasmissione == 'SUAP')
                        Pratica da inviare tramite il SUAP del comune in cui si svolge l'attivit&agrave;
                    @elseif ($modTrasmissione == 'COMUNICA')
                        Pratica da inviare al Registro Imprese territorialmente competente
                    @elseif ($modTrasmissione == 'ALTRI ADEMPIMENTI')
                        Pratica da inviare direttamente all'Ente competente
                    @elseif ($modTrasmissione == 'NESSUNA')
                    @endif
                </small>
            </h5>
        </div>
        <div class="card-body">
            <h6 class="card-title">Cosa serve: </h6>
            <p class="card-text">
                <i class="fa-solid fa-book-open text-danger"></i>
                {{strip_tags($normativa['descrizione'])}}
             </p>
            <h6 class="card-title">Ente competente: </h6>
            <p class="card-text">
                <i class="fa-solid fa-building-columns text-danger"></i>
                {{str_replace(' O ', ' o ', ucwords(strtolower(strip_tags($data['ente']))))}}
            </p>

        </div>
    </div>

    @php
       // dump( $normativa);
    @endphp
@endforeach
@php
    //dump( $adempimento);
@endphp
