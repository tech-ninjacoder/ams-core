<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Project Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .tg  {border-collapse:collapse;border-spacing:0; width: 100%;}
        .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
            overflow:hidden;padding:10px 5px;word-break:normal;}
        .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
            font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
        .tg .tg-dvid{background-color:#efefef;border-color:inherit;font-weight:bold;text-align:left;vertical-align:top}
        .tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
        .logo{
            float: left;
            margin-right: 20px;
            height: 50px;
            width: 50px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }

    </style>
</head>
<body>
<header>
    <script>
    </script>
{{--    <img src="logo.png" alt="Company Logo" class="logo">--}}
    <img src="{{ asset('images/logo.png') }}" alt="Company Logo">
</header>
<div >
    @foreach($project ?? '' as $data)
        <h2 class="text-center mb-3">Project Report</h2>
    <span class="align-left">Report Date: {{\Carbon\Carbon::today()->toDateString()}}</span>
{{--    <div class="d-flex justify-content-end mb-4">--}}
{{--        <a class="btn btn-primary" >Export to PDF</a>--}}
{{--    </div>--}}
        <table class="tg">
            <thead>
            <tr>
                <th class="tg-dvid">Project ID</th>
                <th class="tg-0pky">{{ $data->pme_id }}</th>
                <th class="tg-dvid">Project Name</th>
                <th class="tg-0pky">{{ $data->name }}</th>
                <th class="tg-dvid">Start Date</th>
                <th class="tg-0pky">{{ $data->p_start_date }}</th>
                <th class="tg-dvid">End Date</th>
                <th class="tg-0pky">{{ $data->p_end_date }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="tg-dvid">Location</td>
                <td class="tg-0pky">{{ $data->location }}</td>
                <td class="tg-dvid">Description</td>
                <td class="tg-0pky">{{ $data->description }}</td>
                <td class="tg-dvid">Manager</td>
                <td class="tg-0pky">{{ $data['managers'][0]->first_name ?? '-' }} {{ $data['managers'][0]->last_name ?? '-' }} </td>
                <td class="tg-dvid">Coordinator</td>
                <td class="tg-0pky">{{ $data['coordinators'][0]->first_name ?? '-' }} {{ $data['managers'][0]->last_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="tg-dvid">Estimated MH</td>
                <td class="tg-0pky">{{ $data->est_man_hour }}</td>
                <td class="tg-dvid">Actual MH</td>
                <td class="tg-0pky">{{$hours}}</td>
                <td class="tg-dvid">Status</td>
                @if ($data->status_id == 1)
                    <td class="tg-0pky">Active</td>
                @else
                    <td class="tg-0pky">Inactive</td>
                @endif
                    <td class="tg-dvid">Last Update</td>
                <td class="tg-0pky">{{ $data->updated_at }}</td>
            </tr>
            <tr>
                <td class="tg-dvid">Working Shift</td>
                <td class="tg-0pky">{{ $data['working_shifts'][0]->name  ?? 'N/A' }}</td>
                <td class="tg-dvid">Gate Passes</td>
                <td class="tg-0pky">{{ $data['gate_passes'][0]->name ?? 'N/A' }}</td>
                @php
                    $location = json_decode($data['center']);
                @endphp
                <td class="tg-dvid">Lng.</td>
                <td class="tg-0pky">{{ $location->lng  ?? 'N/A'}}</td>
                <td class="tg-dvid">Lat.</td>
                <td class="tg-0pky">{{ $location->lat  ?? 'N/A'}}</td>
            </tr>
            </tbody>
        </table>
{{--    {{$visitors}}--}}
{{--        @foreach ($visitors as $users)--}}
{{--            {{$users->user_id}}--}}
{{--       @endforeach--}}
        <h2 class="mt-3 mb-3">Currently Assigned Employees</h2>
        <table class="tg">
        <thead>
        <tr>
            <th class="tg-dvid">Employee Name</th>
            <th class="tg-dvid">Start Date</th>
            <th class="tg-dvid">notes</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $project as $p)
            @foreach($p->users as $u)
                <tr>
                    <td>
                        {{$u->first_name}} {{$u->last_name}}
                    </td>
                    <td>
                        {{$u->pivot->start_date}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    @endforeach

</div>
<script src="{{ asset('js/app.js') }}" type="text/js"></script>
</body>
</html>
