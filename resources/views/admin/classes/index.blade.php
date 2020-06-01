@extends('layouts.app')

@section('content')
    @foreach($sections as $section)
        {{ $section }}
    @endforeach

    @foreach($classes as $class)
        {{ $class }}
    @endforeach
@endsection
