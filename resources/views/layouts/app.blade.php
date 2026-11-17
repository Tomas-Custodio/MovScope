<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="bg-gray-950 text-white">
        <header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/5 bg-gray-950/80 backdrop-blur-md">
            <div class="flex h-16 items-center justify-between px-5 sm:px-8">
                <div class="flex items-center gap-10">
                    {{-- LOGO --}}
                    <a href="{{ route('home') }}" wire:navigate class="text-3xl ">
                        Mov<span class="text-red-400">Scope</span>
                    </a>

                    {{-- NAV (desktop) --}}

                    <nav class="hidden items-center gap-6 text-sm text-gray-300 md:flex">
                        <a href="{{ route('home') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('home')])>Filmes</a>
                        <a href="{{ route('series') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('series')])>Séries</a>
                        <a href="{{ route('desenhos') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('desenhos')])>Desenhos</a>
                        <a href="{{ route('dramas') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('dramas')])>Dramas</a>
                        <a href="{{ route('romance') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('romance')])>Romance</a>
                        <a href="{{ route('ficcao') }}" wire:navigate @class([' transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('ficcao')])>Ficção científica</a>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    {{-- IDIOMA --}}
                    <span class="rounded-sm border border-white/30 px-1.5 py-0.5 text-xs font-bold uppercase text-gray-200">
                        pt
                    </span>

                    {{-- PESQUISA --}}
                    <form action="{{ route('pesquisa') }}" method="GET" class="relative">
                        {{-- heroicon: magnifying-glass --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Pesquisar..."
                               class="w-32 rounded-sm bg-white/5 py-1.5 pl-8 pr-2 text-sm text-white ring-1 ring-white/10 transition-all placeholder:text-gray-500 focus:w-44 focus:outline-none focus:ring-red-400 sm:w-48 sm:focus:w-64">
                    </form>

                    {{-- AVATAR --}}
                    <span class="flex size-8 items-center 
                    justify-center rounded-full bg-red-400 text-sm 
                    font-black uppercase text-gray-950 text-white">
                        U
                    </span>

                    {{-- MENU (mobile) — heroicon: bars-3 / x-mark --}}
                    <button type="button" @click="open = !open" class="text-gray-300 md:hidden" aria-label="Menu">
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- NAV (mobile) --}}
            <nav x-show="open" x-cloak x-transition class="flex flex-col border-t border-white/5 px-5 py-2 text-sm text-gray-300 md:hidden">
                <a href="{{ route('home') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('home')])>Filmes</a>
                <a href="{{ route('series') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('series')])>Séries</a>
                <a href="{{ route('desenhos') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('desenhos')])>Desenhos</a>
                <a href="{{ route('dramas') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('dramas')])>Dramas</a>
                <a href="{{ route('romance') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('romance')])>Romance</a>
                <a href="{{ route('ficcao') }}" wire:navigate @class(['py-2 transition-colors hover:text-red-400', 'text-red-400' => request()->routeIs('ficcao')])>Ficção científica</a>
            </nav>
        </header>

        {{-- FILTROS (iguais em todas as páginas; os géneros mudam conforme a página) --}}
        <div class="px-4 pt-4 sm:px-6">
        @php

            // deteta em que página estamos: séries usam "tv", o resto usa "movie"

            $tipo = request()->routeIs('series', 'serie.*') ? 'tv' : 'movie';

            $titulo = match (true) {
                
                request()->routeIs('series', 'serie.*') => 'séries',
                request()->routeIs('desenhos') => 'desenhos',
                request()->routeIs('dramas') => 'dramas',
                request()->routeIs('romance') => 'romance',
                request()->routeIs('ficcao') => 'ficção científica',
                default => 'filmes',
            };

            // os filtros mostram os resultados na própria página (se for uma lista) ou em Filmes/Séries

            $destino = request()->routeIs('home', 'series', 'desenhos', 'dramas', 'romance', 'ficcao')
                ? url()->current()
                : ($tipo === 'tv' ? route('series') : route('home'));

            // valores atuais dos filtros (vêm do URL)

            $ordem = request('ordem', 'popularity.desc');
            $genero = request('genero');
            $ano = request('ano');
            $notaMinima = request('notaMinima');

            // a cache só aceita arrays (não objetos), por isso guarda-se array e converte-se depois

            /* quardando um ainfomacoa em cach epara evitar multiplas requosicoes, e filtrando o resultado
             apenas em categorias ( a reuisicao pega apenas categorias ! ) */

            $generos = \Illuminate\Support\Facades\Cache::remember("tmdb.generos.{$tipo}", 86400, function () use ($tipo) {

                return \Illuminate\Support\Facades\Http::withToken(config('services.tmdb.token'))
                    ->acceptJson()->get("https://api.themoviedb.org/3/genre/{$tipo}/list",
                    ['language' => 'pt-PT'])->json('genres', []);
            });

            /* percorrendo em cada genero da lista e etranfrmando ela em
                 onbjecto e guarado na em uma outra lista (e xistem listas normal e uma colecao de objectos */

            $generosLista = array_map(fn ($genero) => (object) $genero, $generos);
            $ativos = count(array_filter([$genero, $ano, $notaMinima])) + ($ordem !== 'popularity.desc' ? 1 : 0);
            $campo = 'w-full appearance-none rounded-sm bg-gray-950/80 py-2.5 pl-9 pr-9 text-sm text-white [color-scheme:dark] ring-1 ring-white/10 transition placeholder:text-gray-600 hover:ring-white/20 focus:outline-none focus:ring-2 focus:ring-red-400';
            $etiqueta = 'mb-1.5 block text-[11px] font-semibold uppercase tracking-wider text-gray-500';
            $iconeEsq = 'pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-red-400';
            $iconeDir = 'pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-gray-500';
        @endphp

        <form method="GET" action="{{ $destino }}"
              x-data
              @change="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)))"
              @submit.prevent="Livewire.navigate($el.action + '?' + new URLSearchParams(new FormData($el)))"
              class="rounded-sm border border-white/5 bg-gradient-to-br from-gray-900 to-gray-900/40 p-4 shadow-xl shadow-black/40">

            {{-- cabeçalho da barra --}}
            <div class="mb-4 flex items-center justify-between gap-3">
                <p class="flex items-center gap-2 text-sm font-black">

                    {{-- heroicon: adjustments-horizontal --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>

                    Filtrar {{ $titulo }}

                    @if ($ativos)
                        <span class="flex size-5 items-center justify-center rounded-full bg-red-400 text-[10px] font-black text-gray-950">
                            {{ $ativos }}
                        </span>
                    @endif
                </p>

                @if ($ativos)
                    <a href="{{ $destino }}" wire:navigate
                        class="flex items-center gap-1 rounded-sm px-2 py-1 text-xs font-semibold text-gray-400 transition-colors hover:bg-red-500/10 hover:text-red-400">

                        {{-- heroicon: x-mark --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>

                        Limpar
                    </a>
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

                        <select name="ordem" class="{{ $campo }}">
                            <option value="popularity.desc" @selected($ordem == 'popularity.desc')>Mais populares</option>
                            <option value="vote_average.desc" @selected($ordem == 'vote_average.desc')>Melhor avaliados</option>
                            <option value="primary_release_date.desc" @selected($ordem == 'primary_release_date.desc')>Mais recentes</option>
                            @if ($tipo === 'movie')
                                <option value="revenue.desc" @selected($ordem == 'revenue.desc')>Maior receita</option>
                            @endif
                            <option value="title.asc" @selected($ordem == 'title.asc')>Título (A-Z)</option>
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

                        <select name="genero" class="{{ $campo }}">
                            <option value="">Todos os géneros</option>

                            @foreach ($generosLista as $g)
                                <option value="{{ $g->id }}" @selected($genero == $g->id)>{{ $g->name }}</option>
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

                        <input name="ano" value="{{ $ano }}" type="number" min="1900" max="2100"
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

                        <select name="notaMinima" class="{{ $campo }}">

                            <option value="">Qualquer</option>
                            <option value="5" @selected($notaMinima == '5')>5+</option>
                            <option value="6" @selected($notaMinima == '6')>6+</option>
                            <option value="7" @selected($notaMinima == '7')>7+</option>
                            <option value="8" @selected($notaMinima == '8')>8+</option>

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
        </form>
        </div>

        {{ $slot }}

        @livewireScripts
    </body>
</html>
