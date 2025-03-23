@extends('layouts.app')

@section('content')

    <section class="container mx-auto p-2  text-center">
            <livewire:auth.reset-password :token="$token" :email="$email"/>
    </section>

@endsection
