@extends('layout.tenant')

@section('title', __t('projects'))
<style>
    .pac-container { z-index: 10000 !important; }</style>
@section('contents')

    <app-project></app-project>

@endsection
