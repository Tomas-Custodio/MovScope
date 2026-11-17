<?php

use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component
{
    public array $lista = [];

    public function mount(): void
    {
        $this->lista = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()
            ->get('https://api.themoviedb.org/3/discover/movie', ['language' => 'pt-PT', 'with_genres' => 16])
            ->object()->results ?? [];
    }
};
?>

<div class="min-h-screen w-full bg-gray-950 p-4 text-white sm:p-6">
    <div class="mb-5">
        <h1 class="flex items-center gap-2 text-2xl sm:text-3xl">
            {{-- heroicon: face-smile --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-red-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
            </svg>
            Desenhos
        </h1>
        <p class="mt-1 text-sm text-gray-500">Animação para todas as idades.</p>
    </div>

    <div wire:loading.class="opacity-50" class="grid grid-cols-1 gap-5 transition-opacity sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($lista as $item)
            <div wire:key="item-{{ $item->id }}"
                 class="group relative h-96 overflow-hidden rounded-sm bg-gray-900 shadow-lg shadow-black/50 transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-red-950/40">

                @if ($item->backdrop_path)
                    <img src="https://image.tmdb.org/t/p/w500{{ $item->backdrop_path }}" alt="{{ $item->title }}" loading="lazy"
                         class="h-full w-full object-cover brightness-75 transition duration-700 group-hover:scale-110 group-hover:brightness-100">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gray-800">
                        {{-- heroicon: photo --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-14 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                @endif

                {{-- sombra de cima para baixo; fica mais leve quando o card está em foco --}}
                <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 via-gray-950/30 to-gray-950 transition-opacity duration-500 group-hover:opacity-80"></div>

                {{-- NOTA --}}
                <span class="absolute right-3 top-3 flex items-center gap-1 rounded-sm bg-black/60 px-2 py-1 text-xs backdrop-blur-md">
                    {{-- heroicon: star (solid) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-yellow-400">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                    </svg>
                    {{ number_format($item->vote_average, 1) }}
                </span>

                <div class="absolute inset-x-0 bottom-0 p-4">
                    <p class="text-2xl leading-tight transition-colors group-hover:text-red-400">
                        {{ Str::limit($item->title, 35) }}
                    </p>

                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
                        @if ($item->release_date ?? null)
                            <span class="flex items-center gap-1">
                                {{-- heroicon: calendar --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                {{ substr($item->release_date, 0, 4) }}
                            </span>
                        @endif
                        <span class="uppercase">{{ $item->original_language }}</span>
                        <span>{{ number_format($item->vote_count, 0, ',', '.') }} votos</span>
                    </div>

                    <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-gray-300">
                        {{ $item->overview }}
                    </p>

                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('mov.details', ['movId' => $item->id]) }}" wire:navigate
                           class="flex items-center gap-1 rounded-sm bg-red-400 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-950 transition-colors hover:bg-red-500">
                            {{-- heroicon: information-circle --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            Detalhes
                        </a>

                        <a href="{{ route('mov.trailler', ['movId' => $item->id]) }}" wire:navigate
                           class="flex items-center gap-1 rounded-sm bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur-md transition-colors hover:bg-red-500">
                            {{-- heroicon: play --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                            </svg>
                            Trailer
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
