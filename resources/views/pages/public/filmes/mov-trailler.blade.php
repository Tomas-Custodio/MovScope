<?php

use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component
{
    public int $movId;
    public ?stdClass $mov = null;
    public array $videos = [];
    public ?string $current = null;

    public $mainroot = 'https://api.themoviedb.org/3/movie';

    public function mount(int $movId): void
    {
        $this->movId = $movId;

        // um só pedido: dados do filme + vídeos
        $response = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()
            ->get($this->mainroot.'/'.$this->movId, ['append_to_response' => 'videos']);

        $this->mov = $response->object();

        $this->videos = $this->mov->videos->results;

        $this->current = $this->videos[0]->key ?? null;
    }

    public function play(string $key): void
    {
        $this->current = $key;
    }
};
?>

<div class="relative min-h-screen w-full overflow-hidden bg-gray-950 text-white">

    {{-- FUNDO: backdrop do filme desfocado --}}
    @if ($mov?->backdrop_path)
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[60vh]">
            <img src="https://image.tmdb.org/t/p/w780{{ $mov->backdrop_path }}" alt=""
                 class="h-full w-full scale-110 object-cover opacity-30 blur-2xl">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/40 via-gray-950/80 to-gray-950"></div>
        </div>
    @endif

    <div class="relative p-4 sm:p-6">

        {{-- TOPO --}}
        <div class="mb-5 flex items-center gap-3">
            <a href="{{ route('home') }}" wire:navigate aria-label="Voltar"
               class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/10 backdrop-blur-md transition hover:bg-red-500 hover:ring-red-400">
                {{-- heroicon: arrow-left --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>

            <div class="min-w-0">
                <p class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-red-400">
                    {{-- heroicon: film --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 7.746 6 7.125v-1.5M4.875 8.25C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.125v-1.5m1.125 2.625c-.621 0-1.125.504-1.125 1.125v1.5m2.625-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-12 5.25v-5.25m0 5.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125m-12 0v-1.5c0-.621-.504-1.125-1.125-1.125M18 18.375v-5.25m0 5.25v-1.5c0-.621.504-1.125 1.125-1.125M18 13.125v1.5c0 .621.504 1.125 1.125 1.125M18 13.125c0-.621.504-1.125 1.125-1.125M6 13.125v1.5c0 .621-.504 1.125-1.125 1.125M6 13.125C6 12.504 5.496 12 4.875 12m-1.5 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M19.125 12h1.5m0 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h1.5m14.25 0h1.5" />
                    </svg>
                    Trailers &amp; vídeos
                </p>
                <h1 class="text-3xl  sm:text-2xl">
                    {{ $mov?->title }}
                </h1>
            </div>
        </div>

        @if (! $current)

            {{-- SEM VÍDEOS --}}
            <div class="flex flex-col items-center justify-center rounded-sm border border-dashed border-white/10 bg-gray-900/50 py-16 text-gray-500">
                <span class="mb-3 flex size-14 items-center justify-center rounded-full bg-white/5">
                    {{-- heroicon: video-camera-slash --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M12 18.75H4.5a2.25 2.25 0 0 1-2.25-2.25V9m12.841 9.091L16.5 19.5m-1.409-1.409c.407-.407.659-.97.659-1.591v-9a2.25 2.25 0 0 0-2.25-2.25h-9c-.621 0-1.184.252-1.591.659m12.182 12.182L2.909 5.909M1.5 4.5l1.409 1.409" />
                    </svg>
                </span>
                <p class="text-sm">Este filme ainda não tem trailers.</p>
            </div>

        @else
            @php $atual = collect($videos)->firstWhere('key', $current); @endphp

            <div class="flex flex-col gap-5 lg:flex-row">

                {{-- PLAYER --}}
                <div class="min-w-0 flex-1">
                    <div class="aspect-video w-full overflow-hidden rounded-sm bg-black shadow-2xl shadow-red-950/40 ring-1 ring-white/10">
                        <iframe
                            wire:key="player-{{ $current }}"
                            src="https://www.youtube-nocookie.com/embed/{{ $current }}?autoplay=1"
                            title="Trailer"
                            class="h-full w-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>

                    {{-- INFO DO VÍDEO ATUAL --}}
                    @if ($atual)
                        <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="text-base font-bold leading-snug sm:text-lg">{{ $atual->name }}</h2>

                                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                    <span class="rounded-sm bg-red-400 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-gray-950">
                                        {{ $atual->type }}
                                    </span>

                                    @if ($atual->official ?? false)
                                        <span class="flex items-center gap-1 text-sky-400">
                                            {{-- heroicon: check-badge (solid) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                            </svg>
                                            Oficial
                                        </span>
                                    @endif

                                    @if ($atual->published_at ?? null)
                                        <span class="flex items-center gap-1">
                                            {{-- heroicon: calendar --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            {{ substr($atual->published_at, 0, 10) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <a href="https://www.youtube.com/watch?v={{ $current }}" target="_blank" rel="noopener"
                               class="flex shrink-0 items-center gap-1.5 rounded-sm bg-white/5 px-2.5 py-1.5 text-xs font-semibold text-gray-300 ring-1 ring-white/10 transition hover:bg-red-500 hover:text-white hover:ring-red-400">
                                {{-- heroicon: arrow-top-right-on-square --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                Abrir no YouTube
                            </a>
                        </div>
                    @endif
                </div>

                {{-- LISTA DE VÍDEOS --}}
                <aside class="flex flex-col overflow-hidden rounded-sm bg-gray-900/70 ring-1 ring-white/5 backdrop-blur-md lg:max-h-[calc(100vh-9rem)] lg:w-80">
                    <div class="flex items-center justify-between border-b border-white/5 px-3 py-2.5">
                        <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-300">
                            {{-- heroicon: queue-list --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            Vídeos
                        </p>
                        <span class="rounded-sm bg-white/5 px-1.5 py-0.5 text-[10px] font-bold text-gray-400">
                            {{ count($videos) }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1 overflow-y-auto p-1.5 [scrollbar-color:var(--color-gray-700)_transparent] [scrollbar-width:thin]">
                        @foreach ($videos as $video)
                            @php $ativo = $current === $video->key; @endphp

                            <button
                                type="button"
                                wire:key="video-{{ $video->key }}"
                                wire:click="play('{{ $video->key }}')"
                                @class([
                                    'group relative flex gap-2.5 rounded-sm p-1.5 text-left transition',
                                    'bg-red-400/10' => $ativo,
                                    'hover:bg-white/5' => ! $ativo,
                                ])
                            >
                                @if ($ativo)
                                    <span class="absolute inset-y-1.5 left-0 w-0.5 rounded-full bg-red-400"></span>
                                @endif

                                <div class="relative aspect-video w-28 shrink-0 overflow-hidden rounded-sm bg-gray-800 shadow-md shadow-black/50">
                                    <img
                                        src="https://img.youtube.com/vi/{{ $video->key }}/mqdefault.jpg"
                                        alt="{{ $video->name }}"
                                        loading="lazy"
                                        @class([
                                            'h-full w-full object-cover transition duration-500 group-hover:scale-110',
                                            'brightness-50' => $ativo,
                                            'brightness-75 group-hover:brightness-100' => ! $ativo,
                                        ])
                                    />

                                    @if ($ativo)
                                        {{-- heroicon: signal (a tocar) --}}
                                        <span class="absolute inset-0 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6 animate-pulse text-red-400">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </span>
                                    @else
                                        {{-- heroicon: play (solid) --}}
                                        <span class="absolute inset-0 m-auto flex size-7 items-center justify-center rounded-full bg-black/60 opacity-0 backdrop-blur-sm transition group-hover:opacity-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-0.5 size-3.5">
                                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0 py-0.5">
                                    <p @class([
                                        'line-clamp-2 text-[13px] font-semibold leading-snug transition-colors',
                                        'text-red-400' => $ativo,
                                        'text-gray-200 group-hover:text-white' => ! $ativo,
                                    ])>
                                        {{ $video->name }}
                                    </p>
                                    <span class="mt-1 inline-block text-[10px] font-semibold uppercase tracking-wider text-gray-500">
                                        {{ $ativo ? 'A tocar' : $video->type }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>
