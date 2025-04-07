@extends('layout.tenant')

@section('title', __t('contractors'))
<style>
    .pac-container { z-index: 10000 !important; }</style>
@section('contents')

    <app-contractor></app-contractor>

@endsection
