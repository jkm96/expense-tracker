@extends('layouts.app')

@section('content')

    <section class="container mx-auto p-2">
        <livewire:core.audit-log-manager />
        <livewire:core.session-manager />
    </section>

@endsection
