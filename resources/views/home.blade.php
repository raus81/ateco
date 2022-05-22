@extends('layout')

@push('head')
    <meta name="description"
          content="Cerca tutte le informazioni e normative dei codici ATECO">
    <title>Ateco.numerosamente.it</title>
    <link rel="canonical" href="{{url("/")}}"/>
@endpush

@section('content')

    <div class="home justify-content-center d-flex flex-column text-center align-self-center h-100">
        <h1 class="text-white">Cerca un codice ATECO</h1>
        <div class="d-flex flex-row justify-content-center ">
            <div class="bg-danger p-4 rounded rounded-2 ">

                <img alt="Ateco.numerosamente.it " src="/imgs/logoAteco.svg"/>
            </div>
        </div>
        <h1 class="text-danger">Cerca un codice Ateco</h2>
        <div class="p-2">
            <input id="autoComplete" type="search" dir="ltr" spellcheck=false autocorrect="off" autocomplete="off"
                   autocapitalize="off" maxlength="2048" tabindex="1">
        </div>


        <div>
            <h4 class="text-danger">Categorie principali</h4>
            @foreach($mc as $cat)
                <a href="/codice/{{$cat->code}}" class=" btn btn-danger btn-sm mb-1">{{$cat->code}} - <small>{{$cat->nome}}</small></a>
            @endforeach
        </div>
    </div>
@endsection



@push('head')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.6/dist/css/autoComplete.min.css">
@endpush

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.6/dist/autoComplete.min.js"></script>
    <script>
        var url = "{{url('api/suggester')}}"
        var config = {
            selector: "#autoComplete",
            placeHolder: "Cerca ...",
            data: {
                src: async (query) => {
                    try {
                        // Fetch Data from external Source
                        const source = await fetch(`${url}?search=${query}`);
                        // Data is array of `Objects` | `Strings`
                        const data = await source.json();

                        return data;
                    } catch (error) {
                        return error;
                    }
                },
                // Data 'Object' key to be searched
                keys: ["nome"]
            },
            resultsList: {
                element: (list, data) => {
                    if (!data.results.length) {
                        // Create "No Results" message element
                        const message = document.createElement("div");
                        // Add class to the created element
                        message.setAttribute("class", "no_result");
                        // Add message text content
                        message.innerHTML = `<span>Nessun risultato per "${data.query}"</span>`;
                        // Append message element to the results list
                        list.prepend(message);
                    }
                },
                noResults: true,
            },
            resultItem: {
                highlight: {
                    render: true
                }
            }
        };
        const autoCompleteJS = new autoComplete(config);
        autoCompleteJS.input.addEventListener("selection", function (event) {

            document.location.href = event.detail.selection.value.url;
        });
    </script>
@endsection
