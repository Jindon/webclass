@extends('layouts.app')

@section('content')
    @foreach($plans as $plan)
        {{ $plan }}
    @endforeach
@endsection
