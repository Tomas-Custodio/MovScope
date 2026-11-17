<?php

use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component
{
    public ?stdClass $serie = null;

    public function mount(int $serieId): void
    {
        $response = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()->get("https://api.themoviedb.org/3/tv/{$serieId}", ['language' => 'pt-PT']);

        $this->serie = $response->object();
    }
};
?>

<div class="min-h-screen w-full bg-gray-950 text-white">

    {{-- BACKDROP --}}
    <div class="relative h-[55vh] w-full overflow-hidden">
        @if ($serie->backdrop_path)
            <img
                src="https://image.tmdb.org/t/p/original{{ $serie->backdrop_path }}"
                alt="{{ $serie->name }}"
                class="h-full w-full object-cover brightness-75"
            />
        @endif

        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 via-gray-950/30 to-gray-950"></div>

        <a href="{{ route('series') }}" wire:navigate
           class="absolute left-5 top-5 flex items-center gap-1 rounded-sm bg-black/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider backdrop-blur-md transition-colors hover:bg-red-500 sm:left-8 sm:top-8">
            {{-- heroicon: arrow-left --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Voltar
        </a>
    </div>

    {{-- CONTEÚDO --}}
    <div class="relative -mt-40 flex flex-col gap-8 px-5 pb-12 sm:flex-row sm:px-8">

        {{-- POSTER --}}
        @if ($serie->poster_path)
            <img
                src="https://image.tmdb.org/t/p/w500{{ $serie->poster_path }}"
                alt="{{ $serie->name }}"
                class="w-48 shrink-0 self-center rounded-sm shadow-2xl shadow-black/70 sm:w-64 sm:self-start"/>
        @endif

        <div class="flex min-w-0 flex-col gap-5 sm:pt-24">

            {{-- TÍTULO --}}
            <div>
                @if ($serie->networks)
                    <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-red-400">
                        {{ $serie->networks[0]->name }}
                    </p>
                @endif

                <h1 class="text-3xl leading-tight sm:text-5xl">{{ $serie->name }}</h1>

                @if ($serie->original_name !== $serie->name)
                    <p class="mt-1 text-sm text-gray-500">Título original: {{ $serie->original_name }}</p>
                @endif

                @if ($serie->tagline)
                    <p class="mt-2 text-sm italic text-gray-400">{{ $serie->tagline }}</p>
                @endif
            </div>

            {{-- INFO RÁPIDA --}}
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-300">
                <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                    {{-- heroicon: star (solid) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-yellow-400">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold text-white">{{ number_format($serie->vote_average, 1) }}</span>
                    <span class="text-xs text-gray-500">({{ number_format($serie->vote_count, 0, ',', '.') }} votos)</span>
                </span>

                @if ($serie->first_air_date)
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                        {{-- heroicon: calendar --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        {{ date('d/m/Y', strtotime($serie->first_air_date)) }}
                    </span>
                @endif

                @if ($serie->episode_run_time)
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5">
                        {{-- heroicon: clock --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ $serie->episode_run_time[0] }} min / episódio
                    </span>
                @endif

                <span class="flex items-center gap-1.5 rounded-sm bg-red-400/10 px-3 py-1.5 font-semibold text-red-400">
                    {{-- heroicon: check-circle --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $serie->status }}
                </span>
            </div>

            {{-- GÉNEROS --}}
            @if ($serie->genres)
                <div class="flex flex-wrap gap-2">
                    @foreach ($serie->genres as $genre)
                        <span wire:key="genre-{{ $genre->id }}"
                              class="rounded-sm border border-red-400/30 bg-red-400/10 px-2 py-1 text-xs font-semibold text-red-400">
                            {{ $genre->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- SINOPSE --}}
            <p class="max-w-3xl leading-relaxed text-gray-300">
                {{ $serie->overview ?: 'Sem descrição disponível.' }}
            </p>

            {{-- NÚMEROS --}}
            <div class="grid max-w-3xl grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-sm border border-white/5 bg-gray-900 p-4">
                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{-- heroicon: rectangle-stack --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                        </svg>
                        Temporadas
                    </p>
                    <p class="mt-2 text-lg font-bold">{{ $serie->number_of_seasons }}</p>
                </div>

                <div class="rounded-sm border border-white/5 bg-gray-900 p-4">
                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{-- heroicon: film --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 7.746 6 7.125v-1.5M4.875 8.25C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.125v-1.5m1.125 2.625c-.621 0-1.125.504-1.125 1.125v1.5m2.625-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-12 5.25v-5.25m0 5.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125m-12 0v-1.5c0-.621-.504-1.125-1.125-1.125M18 18.375v-5.25m0 5.25v-1.5c0-.621.504-1.125 1.125-1.125M18 13.125v1.5c0 .621.504 1.125 1.125 1.125M18 13.125c0-.621.504-1.125 1.125-1.125M6 13.125v1.5c0 .621-.504 1.125-1.125 1.125M6 13.125C6 12.504 5.496 12 4.875 12m-1.5 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M19.125 12h1.5m0 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h1.5m14.25 0h1.5" />
                        </svg>
                        Episódios
                    </p>
                    <p class="mt-2 text-lg font-bold">{{ $serie->number_of_episodes }}</p>
                </div>

                <div class="rounded-sm border border-white/5 bg-gray-900 p-4">
                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        {{-- heroicon: language --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                        </svg>
                        Idioma original
                    </p>
                    <p class="mt-2 text-lg font-bold uppercase">{{ $serie->original_language }}</p>
                </div>
            </div>

            {{-- CRIADORES --}}
            @if ($serie->created_by)
                <div class="flex max-w-3xl items-start gap-2 text-sm text-gray-300">
                    {{-- heroicon: user --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span>
                        Criado por
                        @foreach ($serie->created_by as $pessoa)
                            {{ $pessoa->name }}@if (! $loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif

            {{-- PAÍSES --}}
            @if ($serie->production_countries)
                <div class="flex max-w-3xl items-start gap-2 text-sm text-gray-300">
                    {{-- heroicon: globe-alt --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    <span>
                        @foreach ($serie->production_countries as $country)
                            {{ $country->name }}@if (! $loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif

            {{-- PRODUTORAS --}}
            @if ($serie->production_companies)
                <div class="flex max-w-3xl items-start gap-2 text-sm text-gray-300">
                    {{-- heroicon: building-office --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-red-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                    <span>
                        @foreach ($serie->production_companies as $company)
                            {{ $company->name }}@if (! $loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif

            {{-- BOTÕES --}}
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('serie.trailler', ['serieId' => $serie->id]) }}" wire:navigate
                   class="flex items-center gap-2 rounded-sm bg-red-400 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-950 transition-colors hover:bg-red-500">
                    {{-- heroicon: play --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    Ver trailer
                </a>

                @if ($serie->homepage)
                    <a href="{{ $serie->homepage }}" target="_blank" rel="noopener"
                       class="flex items-center gap-2 rounded-sm bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-wider transition-colors hover:bg-white/10">
                        {{-- heroicon: link --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                        Site oficial
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
