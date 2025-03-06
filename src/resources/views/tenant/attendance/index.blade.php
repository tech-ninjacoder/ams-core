@extends('layout.tenant')

@section('title', __t('daily_log'))

@section('contents')
    <app-daily-log></app-daily-log>
@endsection
<style>
    p.text-muted.font-size-90.mb-0 {
        display: none !important;
    }

</style>
