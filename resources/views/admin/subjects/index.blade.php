@extends('layouts.app')

@section('content')
    @foreach($subjects as $subject)
        {{ $subject }}
    @endforeach
@endsection
