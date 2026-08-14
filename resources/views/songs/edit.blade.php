@extends('layouts.app')

@section('title', 'Editează ' . $song->title . ' | Ce cântăm duminică')

@section('content')
    @include('songs._form', [
        'song' => $song,
        'formAction' => route('songs.update', $song),
        'formMethod' => 'PUT',
        'pageTitle' => 'Editează piesa',
        'pageDescription' => 'Modifică informațiile, secțiunile și poziția acordurilor.',
        'submitLabel' => 'Salvează modificările',
        'cancelUrl' => route('songs.show', $song),
    ])
@endsection