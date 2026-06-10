@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>

    <p>You're logged in!</p>

    <p>Welcome, {{ auth()->user()->name }}</p>
</div>
@endsection