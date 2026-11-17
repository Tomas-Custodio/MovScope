<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component
{
    public array $movList = [];

    public $mainroot = 'https://api.themoviedb.org/3/discover/movie';

    // filtros

    public string  $ordem = 'popularity.desc';
    public ?string $genero = null;
    public ?string $ano = null;
    public ?string $notaMinima = null;

    // paginação

    public int $pagina = 1;
    public int $totalPaginas = 1;
    public int $totalResultados = 0;

    //lista de de generos

    public array $generosLista = []; # encarregado de guardar os generos quando transformados em objectos
    public array $generosMapa = [];

    public function mount(){

        // a cache só aceita arrays (não objetos), por isso guarda-se array e converte-se depois

        /* quardando um ainfomacoa em cach epara evitar multiplas requosicoes, e filtrando o resultado
         apenas em categorias ( a reuisicao pega apenas categorias ! ) */

        $generos = Cache::remember('tmdb.generos', 86400, function () {

            return Http::withToken(config('services.tmdb.token'))
                ->acceptJson()->get('https://api.themoviedb.org/3/genre/movie/list', 
                ['language' => 'pt-PT'])->json('genres', []);
        });

        /* percorrendo em cada genero da lista e etranfrmando ela em
             onbjecto e guarado na em uma outra lista (e xistem listas normal e uma colecao de objectos */

        $this->generosLista = array_map(fn ($genero) => (object) $genero, $generos);

        /* percorrendo cada objecto da colecao */

        foreach ( $this->generosLista as $genero ) {

            /*$this->generosMapa: é o array onde vamos guardar os dados.
                [$genero->id]: utiliza o ID do género como chave.
                = $genero->name: guarda o nome como valor.*/

               /* reumo : onde o id e a chave o o o nome valor*/

            $this->generosMapa[$genero->id] = $genero->name;
        }

        $this->carregar();

    }

    public function carregar(): void {

        $filtros = [

            'language' => 'pt-PT',
            'sort_by' => $this->ordem,
            'with_genres' => $this->genero,
            'primary_release_year' => $this->ano,
            'vote_average.gte' => $this->notaMinima,
            'page' => $this->pagina,

        ];


        /* sem isto, "melhor avaliados" mostra filmes com 1 voto e nota 10 */

        if (str_starts_with($this->ordem, 'vote_average')) {
            $filtros['vote_count.gte'] = 200;
        }

        /* sem isto, "mais recentes" mostra filmes que ainda nem estrearam */ 

        if (str_starts_with($this->ordem, 'primary_release_date')) {
            $filtros['primary_release_date.lte'] = now()->toDateString();
        }

        /* pegando os itens  d=do array de 
            filtrs que nao sao null e com strings vazias*/

        $filtros = array_filter($filtros, fn ($v) => $v !== null && $v !== '');

        $response = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()->get( $this->mainroot, $filtros );

        $dados = $response->object();

        $this->movList = $dados->results ?? [];
        $this->totalResultados = $dados->total_results ?? 0;
        $this->totalPaginas = min($dados->total_pages ?? 1, 500); // a TMDB só deixa ir até à página 500
    }

    // corre sempre que um filtro (wire:model.live) muda

    public function updated(): void
    {
        $this->pagina = 1;
        $this->carregar();
    }

    public function limpar(): void
    {
        $this->reset(['ordem', 'genero', 'ano', 'notaMinima', 'pagina']);
        $this->carregar();
    }

    public function proxima(): void
    {
        if ($this->pagina < $this->totalPaginas) {
            $this->pagina++;
            $this->carregar();
        }
    }

    public function anterior(): void
    {
        if ($this->pagina > 1) {
            $this->pagina--;
            $this->carregar();
        }
    }

    public function viewDetails(int $movId): void
    {
        $this->redirect(route('mov.details', ['movId' => $movId]));
    }

    public function Watchtrailler(int $movId): void
    {
        $this->redirect(route('mov.trailler', ['movId' => $movId]));
    }
};
?>

<div class="min-h-screen w-full bg-gray-950 p-5 text-white sm:p-8">

    {{-- TOPO --}}

    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">

        <div>

            <h1 class="flex items-center gap-2 text-2xl  sm:text-3xl">

                {{-- heroicon: video-camera --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="size-7 text-red-400">

                    <path stroke-linecap="round" 
                    stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 
                    0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 
                    18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 
                    0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 
                    0 0 0 2.25 2.25Z"/>

                </svg>

                Descobrir filmes
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ number_format($totalResultados, 0, ',', '.') }} filmes encontrados
            </p>

        </div>

    </div>

    {{-- FILTROS --}}

   
{{-- FILTROS --}}
@php
    $ativos = count(array_filter([$genero, $ano, $notaMinima])) + ($ordem !== 'popularity.desc' ? 1 : 0);
    $campo = 'w-full appearance-none rounded-sm bg-gray-950/80 py-2.5 pl-9 pr-9 text-sm text-white [color-scheme:dark] ring-1 ring-white/10 transition placeholder:text-gray-600 hover:ring-white/20 focus:outline-none focus:ring-2 focus:ring-red-400';
    $etiqueta = 'mb-1.5 block text-[11px] font-semibold uppercase tracking-wider text-gray-500';
    $iconeEsq = 'pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-red-400';
    $iconeDir = 'pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-gray-500';
@endphp

<div class="mb-8 rounded-sm border border-white/5 bg-gradient-to-br from-gray-900 to-gray-900/40 p-5 shadow-xl shadow-black/40">

    {{-- cabeçalho da barra --}}
    <div class="mb-4 flex items-center justify-between gap-3">
        <p class="flex items-center gap-2 text-sm font-black">

            {{-- heroicon: adjustments-horizontal --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
            </svg>

            Filtros

            @if ($ativos)
                <span class="flex size-5 items-center justify-center rounded-full bg-red-400 text-[10px] font-black text-gray-950">
                    {{ $ativos }}
                </span>
            @endif
        </p>

        @if ($ativos)
            <button wire:click="limpar"
                class="flex items-center gap-1 rounded-sm px-2 py-1 text-xs font-semibold text-gray-400 transition-colors hover:bg-red-500/10 hover:text-red-400">

                {{-- heroicon: x-mark --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

                Limpar
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- ORDENAR --}}
        <div>
            <label class="{{ $etiqueta }}">Ordenar por</label>

            <div class="relative">

                {{-- heroicon: arrows-up-down --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="{{ $iconeEsq }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                </svg>

                <select wire:model.live="ordem" class="{{ $campo }}">
                    <option value="popularity.desc">Mais populares</option>
                    <option value="vote_average.desc">Melhor avaliados</option>
                    <option value="primary_release_date.desc">Mais recentes</option>
                    <option value="revenue.desc">Maior receita</option>
                    <option value="title.asc">Título (A-Z)</option>
                </select>

                {{-- heroicon: chevron-down --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="{{ $iconeDir }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>

        {{-- GÉNERO --}}
        <div>
            <label class="{{ $etiqueta }}">Género</label>

            <div class="relative">

                {{-- heroicon: tag --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="{{ $iconeEsq }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>

                <select wire:model.live="genero" class="{{ $campo }}">
                    <option value="">Todos os géneros</option>

                    @foreach ($generosLista as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>

                {{-- heroicon: chevron-down --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 

                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                    class="{{ $iconeDir }}">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>

        {{-- ANO --}}
        <div>
            <label class="{{ $etiqueta }}">Ano de estreia</label>

            <div class="relative">

                {{-- heroicon: calendar --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="{{ $iconeEsq }}">

                <path stroke-linecap="round" 

                    stroke-linejoin="round" d="M6.75 
                        3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 
                            1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 
                            0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0
                             0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 
                             5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>

                <input wire:model.live.debounce.600ms="ano" type="number" min="1900" max="2100"
                    placeholder="Ex: 2024" class="{{ $campo }}">
            </div>
        </div>

        {{-- NOTA --}}

        <div>
            <label class="{{ $etiqueta }}">Nota mínima</label>

            <div class="relative">

                {{-- heroicon: star --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="{{ $iconeEsq }}">
                   
                    <path stroke-linecap="round" stroke-linejoin="round"

                     d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 
                     0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 
                     3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 
                     0 0 1-.84.61l-4.725-2.885a.562.562 0 0 
                     0-.586 0L6.982 20.54a.562.562 0 0 
                     1-.84-.61l1.285-5.386a.562.562 0 
                     0 0-.182-.557l-4.204-3.602a.562.562 
                     0 0 1 .321-.988l5.518-.442a.563.563 
                     0 0 0 .475-.345L11.48 3.5Z" />
                </svg>

                <select wire:model.live="notaMinima" class="{{ $campo }}">

                    <option value="">Qualquer</option>
                    <option value="5">5+</option>
                    <option value="6">6+</option>
                    <option value="7">7+</option>
                    <option value="8">8+</option>

                </select>

                {{-- heroicon: chevron-down --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 

                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="{{ $iconeDir }}">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />

                </svg>
            </div>
        </div>

    </div>
</div>


    {{-- SEM RESULTADOS --}}

    @if ( count($movList) === 0 )

        <div class="rounded-sm border border-white/5 bg-gray-900 p-10 text-center text-sm text-gray-400">
            Nenhum filme encontrado com estes filtros.
        </div>

    @endif

    {{-- LISTA --}}

    <div wire:loading.class="opacity-50" 
        class="grid grid-cols-1 gap-6 transition-opacity sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        @foreach ($movList as $mov)

            <div wire:key="mov-{{ $mov->id }}"
                class="group relative h-96 overflow-hidden 
                    rounded-sm bg-gray-900 shadow-lg shadow-black/50 transition-all 
                        duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-red-950/40">

                @if ($mov->backdrop_path)

                    <img
                        src="https://image.tmdb.org/t/p/w500{{ $mov->backdrop_path }}"
                        alt="{{ $mov->title }}"
                        loading="lazy"
                        class="h-full w-full object-cover brightness-75 
                            transition duration-700 group-hover:scale-110 group-hover:brightness-100"/>
                @else

                    <div class="flex h-full w-full items-center justify-center bg-gray-800">
                        {{-- heroicon: video-camera --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-14 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>

                @endif

                {{-- sombra de cima para baixo; fica mais leve quando o card está em foco --}}

                <div class="absolute inset-0 bg-gradient-to-b 
                    from-gray-950/70 via-gray-950/30 to-gray-950 transition-opacity 
                        duration-500 group-hover:opacity-80">
                </div>

                {{-- NOTA --}}

                <span class="absolute right-3 top-3 flex items-center gap-1 
                    rounded-sm bg-black/60 px-2 py-1 text-xs backdrop-blur-md">
                    
                    {{-- heroicon: star (solid) --}}

                    <svg xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" fill="currentColor" class="size-4 text-yellow-400">

                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 
                            1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 
                            1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 
                            1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.
                            996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527
                            c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" 
                            clip-rule="evenodd"/>
                    </svg>

                    <span class="font-bold">{{ number_format($mov->vote_average, 1) }}</span>

                </span>

                <div class="absolute inset-x-0 bottom-0 p-4 text-white">

                    {{-- GÉNEROS --}}

                    <div class="mb-2 flex flex-wrap gap-1">

                        @foreach (array_slice($mov->genre_ids, 0, 2) as $gid)

                            <span class="rounded-sm border border-red-400/30 bg-red-400/10 px-2 py-0.5 text-[10px] font-semibold text-red-400">
                                {{ $generosMapa[$gid] ?? '' }}
                            </span>

                        @endforeach

                    </div>

                    <p class="text-2xl leading-tight transition-colors group-hover:text-red-400">
                        {{ Str::limit($mov->title, 35) }}
                    </p>

                    {{-- INFO RÁPIDA --}}

                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-400">

                        @if ($mov->release_date)

                            <span class="flex items-center gap-1">

                                {{-- heroicon: calendar --}}

                                <svg xmlns="http://www.w3.org/2000/svg" 
                                fill="none" viewBox="0 0 24 24" 
                                    stroke-width="1.5" stroke="currentColor" 
                                    class="size-3.5">

                                    <path stroke-linecap="round" 

                                        stroke-linejoin="round" d="M6.75 
                                            3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 
                                            2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 
                                            1 21 7.5v11.25m-18 0A2.25 2.25 0
                                            0 0 5.25 21h13.5A2.25 2.25 0 
                                            0 0 21 18.75m-18 0v-7.5A2.25 
                                            2.25 0 0 1 5.25 9h13.5A2.25 2.25 
                                            0 0 1 21 11.25v7.5" />

                                </svg>

                                {{ substr($mov->release_date, 0, 4) }}

                            </span>
                        @endif

                        <span class="uppercase">
                            {{ $mov->original_language }}
                        </span>

                        <span>
                                {{ number_format($mov->vote_count, 0, ',', '.') }} votos
                        </span>

                    </div>

                    <p class="mt-2 line-clamp-2 text-xs 
                        leading-relaxed text-gray-300">

                        {{ $mov->overview }}

                    </p>

                    <div class="mt-3 flex items-center gap-2">

                        <button wire:click="viewDetails({{ $mov->id }})" 

                            class="flex items-center gap-1 rounded-sm 
                                bg-red-400 px-3 py-1 text-xs font-semibold 
                                uppercase tracking-wider text-gray-950 
                                transition-colors hover:bg-red-500">

                            {{-- heroicon: information-circle --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                            viewBox="0 0 24 24" stroke-width="1.5" 
                                stroke="currentColor" class="size-4">

                                <path stroke-linecap="round" 

                                    stroke-linejoin="round" d="m11.25 
                                        11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 
                                            2.836a.75.75 0 0 0 1.063.853l.041-.021M21 
                                            12a9 9 0 1 1-18 0 9 9 0 0 1 18 
                                            0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            Detalhes
                        </button>

                        <button wire:click="Watchtrailler({{ $mov->id }})" class="flex items-center gap-1 rounded-sm bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur-md transition-colors hover:bg-red-500">
                            {{-- heroicon: play --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                            </svg>
                            Trailer
                        </button>
                    </div>
                </div>
            </div>

        @endforeach

    </div>

    {{-- PAGINAÇÃO --}}

    @if ($totalPaginas > 1)
        <div class="mt-10 flex items-center justify-center gap-4">

            <button wire:click="anterior" @disabled($pagina <= 1) aria-label="Anterior"
                class="flex size-10 items-center justify-center 
                    rounded-full bg-white/5 transition-colors hover:bg-red-500 
                        disabled:cursor-not-allowed disabled:opacity-30
                        disabled:hover:bg-white/5">

                {{-- heroicon: chevron-left --}}

                <svg xmlns="http://www.w3.org/2000/svg" 
                fill="none" viewBox="0 0 24 24" stroke-width="2" 
                    stroke="currentColor" class="size-4">

                    <path stroke-linecap="round" 
                        stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <span class="text-sm text-gray-400">
                Página <span class="font-bold text-white">{{ $pagina }}</span> de {{ $totalPaginas }}
            </span>

            <button wire:click="proxima" @disabled($pagina >= $totalPaginas)

                aria-label="Seguinte"
                    class="flex size-10 items-center justify-center 
                    rounded-full bg-white/5 transition-colors hover:bg-red-500 
                    disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-white/5">

                {{-- heroicon: chevron-right --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" 

                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                    d="m8.25 4.5 7.5 7.5-7.5 7.5" />

                </svg>
            </button>
        </div>
    @endif
</div>