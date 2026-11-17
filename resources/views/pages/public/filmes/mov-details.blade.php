<?php

use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component
{
    public ?stdClass $mov = null;

    public function mount(int $movId): void
    {
        $response = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()->get("https://api.themoviedb.org/3/movie/{$movId}");

        $this->mov = $response->object();
    }

    public function participantes(): void
    {
        $this->redirect(route('mov.participants', ['movId' => $this->mov->id]));
    }
};
?>

<div class="min-h-screen w-full bg-gray-950 text-white">

        {{-- BACKDROP --}}

        <div class="relative h-[55vh] w-full overflow-hidden">
            @if ($mov->backdrop_path)
                <img
                    src="https://image.tmdb.org/t/p/original{{ $mov->backdrop_path }}"
                    alt="{{ $mov->title }}"
                    class="h-full w-full object-cover brightness-75"
                />
            @endif

            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 via-gray-950/30 to-gray-950"></div>

            <a href="{{ route('home') }}" wire:navigate
               class="absolute left-5 top-5 flex items-center gap-1 rounded-sm b
               g-black/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider backdrop-blur-md 
               transition-colors hover:bg-red-500 sm:left-8 sm:top-8">

                {{-- heroicon: arrow-left --}}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                stroke-width="2" stroke="currentColor" class="size-4">

                    <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />

                </svg>

                Voltar

            </a>

        </div>

        {{-- CONTEÚDO --}}

        <div class="relative -mt-40 flex flex-col gap-8 px-5 pb-12 sm:flex-row sm:px-8">

            {{-- POSTER --}}

            @if ( $mov->poster_path )

                <img
                    src="https://image.tmdb.org/t/p/w500{{ $mov->poster_path }}"
                    alt="{{ $mov->title }}"
                    class="w-48 shrink-0 self-center rounded-sm shadow-2xl shadow-black/70 
                    sm:w-64 sm:self-start"/>

            @endif

            <div class="flex min-w-0 flex-col gap-5 sm:pt-24">

                {{-- TÍTULO --}}

                <div>

                    @if ($mov->belongs_to_collection)

                        <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-red-400">
                            {{ $mov->belongs_to_collection->name }}
                        </p>

                    @endif

                    <h1 class="text-3xl font-black leading-tight sm:text-5xl">{{ $mov->title }}</h1>

                    @if ($mov->original_title !== $mov->title)

                        <p class="mt-1 text-sm text-gray-500">Título original: {{ $mov->original_title }}</p>
                    @endif

                    @if ($mov->tagline)
                        <p class="mt-2 text-sm italic text-gray-400">{{ $mov->tagline }}</p>
                    @endif
                </div>

                {{-- INFO RÁPIDA --}}
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-300">
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                        {{-- heroicon: star (solid) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-yellow-400">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold text-white">{{ number_format($mov->vote_average, 1) }}</span>
                        <span class="text-xs text-gray-500">({{ number_format($mov->vote_count, 0, ',', '.') }} votos)</span>
                    </span>

                    @if ($mov->release_date)
                        <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                            {{-- heroicon: calendar --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            {{ date('d/m/Y', strtotime($mov->release_date)) }}
                        </span>
                    @endif

                    @if ($mov->runtime)
                        <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                            {{-- heroicon: clock --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ intdiv($mov->runtime, 60) }}h {{ $mov->runtime % 60 }}min
                        </span>
                    @endif

                    <span class="flex items-center gap-1.5 rounded-sm bg-red-400/10 px-3 py-1.5 font-semibold text-red-400">
                        {{-- heroicon: check-circle --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ $mov->status }}
                    </span>
                </div>

                {{-- GÉNEROS --}}

                @if ($mov->genres)

                    <div class="flex flex-wrap gap-2">

                        @foreach ($mov->genres as $genre)

                            <span wire:key="genre-{{ $genre->id }}" 
                                    class="rounded-sm border border-red-400/30 bg-red-400/10 
                                        px-2 py-1 text-xs font-semibold text-red-400">

                                {{ $genre->name }}

                            </span>

                        @endforeach

                    </div>

                @endif

                {{-- SINOPSE --}}

                <p class="max-w-3xl leading-relaxed text-gray-300">

                    {{ $mov->overview ?: 'Sem descrição disponível.' }}

                </p>

                {{-- NÚMEROS --}}

                <div class="grid max-w-3xl grid-cols-1 gap-3 sm:grid-cols-3">

                    <div class="rounded-sm border border-white/5 bg-gray-900 p-4">

                        <p class="flex items-center gap-2 text-xs font-semibold uppercase 
                            tracking-wider text-gray-500">

                            {{-- heroicon: banknotes --}}

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" 
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                                        class="size-4 text-red-400">

                                <path stroke-linecap="round" 
                                        stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 
                                        2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 
                                        0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25
                                        6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 
                                        0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 
                                        0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 
                                        0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0
                                        0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0
                                        0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 
                                        0h.008v.008H6V10.5Z"/>

                            </svg>

                            Orçamento
                        </p>
                        <p class="mt-2 text-lg font-bold">

                            {{ $mov->budget > 0 ? '$ '.number_format($mov->budget, 0, ',', '.')
                                : 'Não divulgado' }}
                        </p>
                    </div>

                    <div class="rounded-sm border border-white/5 bg-gray-900 p-4">
                        <p class="flex items-center gap-2 text-xs font-semibold 
                            uppercase tracking-wider text-gray-500">

                            {{-- heroicon: banknotes --}}

                            <svg xmlns="http://www.w3.org/2000/svg" 
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                    stroke="currentColor" class="size-4 text-red-400">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                     d="M2.25 18.75a60.07 60.07 0 0 1 15.797 
                                        2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 
                                    4.5v.75A.75.75 0 0 1 3 6h-.75m0 
                                    0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 
                                    
                                    .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 
                                    1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 
                                    0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 
                                    1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 
                                    6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />

                                </svg>

                            Receita

                        </p>

                        <p class="mt-2 text-lg font-bold">

                            {{ $mov->revenue > 0 ? '$ '
                                .number_format($mov->revenue, 0, ',', '.') : 'Não divulgada'
                            }}

                        </p>

                    </div>

                    <div class="rounded-sm border border-white/5 bg-gray-900 p-4">
                        <p class="flex items-center gap-2 text-xs font-semibold 
                            uppercase tracking-wider text-gray-500">

                            {{-- heroicon: language --}}

                            <svg xmlns="http://www.w3.org/2000/svg" 
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                    stroke="currentColor" class="size-4 text-red-400">

                                <path stroke-linecap="round" 

                                    stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 
                                        5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 
                                        0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 
                                        15.08 3 17.502m9.334-12.138c.896.061 1.785.147 
                                        2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802"
                                />

                            </svg>

                            Idioma original
                        </p>
                        <p class="mt-2 text-lg font-bold uppercase">{{ $mov->original_language }}</p>
                    </div>
                </div>

                {{-- PAÍSES --}}

                @if ( $mov->production_countries )

                    <div class="flex max-w-3xl items-start gap-2 text-sm text-gray-300">

                        {{-- heroicon: globe-alt --}}

                        <svg xmlns="http://www.w3.org/2000/svg" 
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                            stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-red-400">

                            <path stroke-linecap="round" stroke-linejoin="round" 

                                d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 
                                0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3
                                12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 
                                0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 
                                0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 
                                12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 
                                0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 
                                2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 
                                0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 
                                1 3 12c0-1.605.42-3.113 1.157-4.418"/>

                        </svg>

                        <span>

                            @foreach ($mov->production_countries as $country)

                                {{ $country->name }}
                                
                                @if (! $loop->last), 
                                
                                @endif

                            @endforeach

                        </span>
                    </div>
                @endif

                {{-- PRODUTORAS --}}

                @if ($mov->production_companies)

                    <div class="flex max-w-3xl items-start gap-2 text-sm text-gray-300">
                        {{-- heroicon: building-office --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-red-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                        </svg>
                        <span>
                            @foreach ($mov->production_companies as $company)
                                {{ $company->name }}@if (! $loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                @endif

                {{-- BOTÕES --}}
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <button wire:click='participantes' class="flex items-center gap-2 rounded-sm bg-red-400 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-950 transition-colors hover:bg-red-500">
                        {{-- heroicon: user-group --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                        Ver elenco
                    </button>

                    <a href="{{ route('mov.trailler', ['movId' => $mov->id]) }}" wire:navigate
                       class="flex items-center gap-2 rounded-sm bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wider backdrop-blur-md transition-colors hover:bg-red-500">
                        {{-- heroicon: play --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                        </svg>
                        Ver trailer
                    </a>

                    @if ($mov->homepage)
                    
                        <a href="{{ $mov->homepage }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 rounded-sm bg-white/5
                            px-4 py-2 text-xs font-semibold uppercase tracking-wider
                             transition-colors hover:bg-white/10">

                            {{-- heroicon: link --}}

                            <svg xmlns="http://www.w3.org/2000/svg" 

                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" 
                                    stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 
                                    1.242 7.244l-4.5 4.5a4.5 4.5 0 0 
                                    1-6.364-6.364l1.757-1.757m13.35-.622
                                    1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 
                                    4.5a4.5 4.5 0 0 0 1.242 7.244"/>

                            </svg>

                            Site oficial

                        </a>
                    @endif
                </div>
            </div>
        </div>
</div>
