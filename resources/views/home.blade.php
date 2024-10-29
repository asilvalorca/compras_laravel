@extends('layouts.app')  <!-- Extiende de un layout llamado layout.blade.php -->

@section('title', 'Inicio')  <!-- Define la sección title como "Inicio" -->

@section('app-header')
    @include('layouts.partials.app-header', ['title' => 'Inicio', 'breadcrumb' => 'Dashboard'])
@endsection

@section('content')

@endsection
