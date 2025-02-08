@extends('layouts.app')

@section('content')

    <section class="container mx-auto p-2  text-center">
            <h2 class="text-3xl font-semibold text-gray-800 mb-6">Sign In Account</h2>
            <livewire:auth.login-user />
    </section>

@endsection
