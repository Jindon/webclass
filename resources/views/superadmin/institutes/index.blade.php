@extends('layouts.app')

@section('content')
    @foreach($institutes as $institute)
        {{ $institute }}
    @endforeach
@endsection
