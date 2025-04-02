@extends('layouts.app')

@section('content')

    <section class="container mx-auto">
        <livewire:core.audit-log-manager />
        <livewire:core.session-manager />
    </section>

@endsection
