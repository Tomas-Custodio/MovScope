<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public int $movId;
    public ?stdClass $mov = null;

    public array $cast = [];
    public array $crew = [];

    public int $totalElenco = 0;
    public int $totalEquipa = 0;

    public function mount(int $movId): void
    {
        $this->movId = $movId;

        $response = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()->get("https://api.themoviedb.org/3/movie/{$movId}/credits");

        $movieResponse = Http::withToken(config('services.tmdb.token'))
            ->acceptJson()->get("https://api.themoviedb.org/3/movie/{$movId}");

        if ($movieResponse->successful()) {
            $this->mov = $movieResponse->object();
        }

        $dados = $response->object();
        $elenco = $dados->cast ?? [];
        $equipa = $dados->crew ?? [];

        $this->totalElenco = count($elenco);
        $this->totalEquipa = count($equipa);

        // só os 24 primeiros do elenco (já vêm ordenados por importância)
        $this->cast = array_slice($elenco, 0, 24);

        // só a equipa principal
        $principais = ['Director', 'Writer', 'Screenplay', 'Producer', 'Original Music Composer', 'Director of Photography'];

        foreach ($equipa as $pessoa) {
            if (in_array($pessoa->job, $principais)) {
                $this->crew[] = $pessoa;
            }
        }
    }
};
?>

<div class="min-h-screen w-full bg-gray-950 text-white">

    {{-- CABEÇALHO --}}
    <div class="relative w-full overflow-hidden">
        @if ($mov && $mov->backdrop_path)
            <img
                src="https://image.tmdb.org/t/p/w1280{{ $mov->backdrop_path }}"
                alt="{{ $mov->title }}"
                class="absolute inset-0 h-full w-full object-cover brightness-50"
            />
        @endif

        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/60 via-gray-950/70 to-gray-950"></div>

        <div class="relative p-5 pb-10 sm:p-8 sm:pb-12">

            {{-- VOLTAR --}}
            <a href="{{ route('mov.details', ['movId' => $movId]) }}" wire:navigate
               class="inline-flex items-center gap-1 rounded-sm bg-black/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider backdrop-blur-md transition-colors hover:bg-red-500">
                {{-- heroicon: arrow-left --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Voltar
            </a>

            {{-- TÍTULO --}}
            <div class="mt-10">
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-red-400">
                    {{-- heroicon: user-group --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    Elenco e equipa técnica
                </p>

                <h1 class="mt-2 text-3xl font-black leading-tight sm:text-5xl">
                    {{ $mov->title ?? 'Filme' }}
                </h1>

                @if ($mov && $mov->tagline)
                    <p class="mt-2 text-sm italic text-gray-400">{{ $mov->tagline }}</p>
                @endif
            </div>

            {{-- INFORMAÇÕES IMPORTANTES --}}
            <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">

                @if ($mov && $mov->vote_average)
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5 backdrop-blur-md">
                        {{-- heroicon: star (solid) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-yellow-400">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-bold">{{ number_format($mov->vote_average, 1) }}</span>
                    </span>
                @endif

                @if ($mov && $mov->release_date)
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5 text-gray-300 backdrop-blur-md">
                        {{-- heroicon: calendar --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        {{ substr($mov->release_date, 0, 4) }}
                    </span>
                @endif

                @if ($mov && $mov->runtime)
                    <span class="flex items-center gap-1.5 rounded-sm bg-white/5 px-3 py-1.5 text-gray-300 backdrop-blur-md">
                        {{-- heroicon: clock --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ intdiv($mov->runtime, 60) }}h {{ $mov->runtime % 60 }}min
                    </span>
                @endif

                <span class="flex items-center gap-1.5 rounded-sm bg-red-400/10 px-3 py-1.5 font-semibold text-red-400 backdrop-blur-md">
                    {{-- heroicon: user --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    {{ $totalElenco }} no elenco
                </span>

                <span class="flex items-center gap-1.5 rounded-sm bg-red-400/10 px-3 py-1.5 font-semibold text-red-400 backdrop-blur-md">
                    {{-- heroicon: video-camera --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    {{ $totalEquipa }} na equipa
                </span>
            </div>
        </div>
    </div>

    <div class="px-5 pb-12 sm:px-8">

        {{-- EQUIPA PRINCIPAL --}}
        @if ($crew)
            <h2 class="mb-4 flex items-center gap-2 text-xl font-black">
                {{-- heroicon: video-camera --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                Equipa principal
            </h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($crew as $person)
                    <div
                        wire:key="crew-{{ $person->id }}-{{ $person->job }}"
                        class="flex items-center gap-3 rounded-sm border border-white/5 bg-gray-900 p-3 shadow-lg shadow-black/50 transition-colors hover:border-red-400/30"
                    >
                        @if ($person->profile_path)
                            <img
                                src="https://image.tmdb.org/t/p/w185{{ $person->profile_path }}"
                                alt="{{ $person->name }}"
                                loading="lazy"
                                class="size-14 shrink-0 rounded-sm object-cover"
                            />
                        @else
                            <div class="flex size-14 shrink-0 items-center justify-center rounded-sm bg-gray-800">
                                {{-- heroicon: user --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                        @endif

                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold">{{ $person->name }}</p>
                            <p class="mt-1 inline-block rounded-sm bg-red-400/10 px-2 py-0.5 text-xs font-semibold text-red-400">
                                {{ $person->job }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ELENCO --}}
        <h2 class="mb-4 mt-12 flex items-center gap-2 text-xl font-black">
            {{-- heroicon: user-group --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            Elenco principal
            <span class="text-sm font-normal text-gray-500">({{ count($cast) }} de {{ $totalElenco }})</span>
        </h2>

        <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach ($cast as $person)
                <div
                    wire:key="cast-{{ $person->id }}-{{ $person->character }}"
                    class="group overflow-hidden rounded-sm bg-gray-900 shadow-lg shadow-black/50 transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl hover:shadow-red-950/40"
                >
                    <div class="relative aspect-[2/3] overflow-hidden">
                        @if ($person->profile_path)
                            <img
                                src="https://image.tmdb.org/t/p/w300{{ $person->profile_path }}"
                                alt="{{ $person->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover brightness-75 transition duration-700 group-hover:scale-110 group-hover:brightness-100"
                            />
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gray-800">
                                {{-- heroicon: user --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-14 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-gray-950 transition-opacity duration-500 group-hover:opacity-80"></div>
                    </div>

                    <div class="p-3">
                        <p class="truncate text-sm font-bold transition-colors group-hover:text-red-400">
                            {{ $person->name }}
                        </p>
                        <p class="mt-1 line-clamp-2 text-xs text-gray-400">
                            {{ $person->character ?: 'Personagem não indicada' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>