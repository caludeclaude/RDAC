@extends('layouts.app')

@section('title', 'Dashboard - DENR-4A RDAC Portal')

@section('content')
    <div class="card p-4">
        <h4 class="fw-bold" style="color: var(--navy);">Welcome, {{ auth()->user()->full_name }}!</h4>
        <p class="text-muted">You are logged in as {{ auth()->user()->role->label() }}.</p>
        <ul class="mb-0">
            <li>Registered offices: <strong>{{ $officeCount }}</strong></li>
            <li>Complaint types: <strong>{{ $typeCount }}</strong></li>
        </ul>
    </div>
@endsection