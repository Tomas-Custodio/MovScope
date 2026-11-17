<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Filmes
|--------------------------------------------------------------------------
*/

Route::livewire('/', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'movie')
    ->defaults('titulo', 'filmes')
    ->name('home');

Route::livewire('/movie/{movId}', 'pages::public.filmes.mov-details')
    ->whereNumber('movId')
    ->name('mov.details');

Route::livewire('/movie/{movId}/elenco', 'pages::public.filmes.mov-participants')
    ->whereNumber('movId')
    ->name('mov.participants');

Route::livewire('/movie/{movId}/trailler', 'pages::public.filmes.mov-trailler')
    ->whereNumber('movId')
    ->name('mov.trailler');

/*
|--------------------------------------------------------------------------
| Séries
|--------------------------------------------------------------------------
*/

Route::livewire('/series', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'tv')
    ->defaults('titulo', 'séries')
    ->name('series');

Route::livewire('/serie/{serieId}', 'pages::public.series.serie-details')
    ->whereNumber('serieId')
    ->name('serie.details');

Route::livewire('/serie/{serieId}/trailler', 'pages::public.series.serie-trailler')
    ->whereNumber('serieId')
    ->name('serie.trailler');

/*
|--------------------------------------------------------------------------
| Géneros
|--------------------------------------------------------------------------
*/

// todas usam a mesma página (catalogo); só muda o género fixo

Route::livewire('/desenhos', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'movie')
    ->defaults('generoBase', 16)
    ->defaults('titulo', 'desenhos')
    ->name('desenhos');

Route::livewire('/dramas', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'movie')
    ->defaults('generoBase', 18)
    ->defaults('titulo', 'dramas')
    ->name('dramas');

Route::livewire('/romance', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'movie')
    ->defaults('generoBase', 10749)
    ->defaults('titulo', 'romance')
    ->name('romance');

Route::livewire('/ficcao-cientifica', 'pages::public.catalogo.catalogo')
    ->defaults('tipo', 'movie')
    ->defaults('generoBase', 878)
    ->defaults('titulo', 'ficção científica')
    ->name('ficcao');

/*
|--------------------------------------------------------------------------
| Pesquisa
|--------------------------------------------------------------------------
*/

Route::livewire('/pesquisa', 'pages::public.pesquisa.pesquisa')
    ->name('pesquisa');
