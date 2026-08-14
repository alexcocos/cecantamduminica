@extends('layouts.app')

@section('title', 'Adaugă piesă | Ce cântăm duminică')

@section('content')
    @include('songs._form', [
        'song' => null,
        'formAction' => route('songs.store'),
        'formMethod' => 'POST',
        'pageTitle' => 'Adaugă o piesă',
        'pageDescription' => 'Adaugă pe rând fiecare parte a piesei, apoi poziționează acordurile.',
        'submitLabel' => 'Salvează piesa',
        'cancelUrl' => route('songs.index'),
    ])
@endsection