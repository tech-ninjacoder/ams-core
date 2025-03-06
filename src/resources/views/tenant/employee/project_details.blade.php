@extends('layout.tenant')

@section('title', __t('project_details'))

@section('contents')
    <app-project-details
            :project-id="{{$project_id}}"
{{--            :manager-dept="{{ json_encode($manager_dept) }}"--}}
    ></app-project-details>
@endsection
