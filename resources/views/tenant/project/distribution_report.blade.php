<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Project Report</title>
{{--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">--}}
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />--}}
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
        .page_break { page-break-before: always; }


    </style>
</head>
<body>
<header>
    <script>
    </script>
{{--    <img src="logo.png" alt="Company Logo" class="logo">--}}
</header>
<div >
{{--    {{$project[0]['pme_id']}}--}}
{{--    @foreach($project as $data)--}}
{{--        {{$data['pme_id']}}--}}
{{--        @endforeach--}}
{{--    <div style="page-break-after: always;"></div>--}}
    @foreach ($project as $data)
        @if (!is_null($data->coordinators_first_name))
{{--           coordinator {{$coodrindator = $data->coordinators}}--}}
            @php
                $coodrindator = $data['coordinators'][0]['full_name'];
            @endphp
        @else
            @php
                $coodrindator = null;

            @endphp
        @endif
        <img src="{{ asset('images/logo.png') }}" alt="Company Logo">
        <h4>Date: {{\Carbon\Carbon::today()->addDay()->toDateString()}}</h4>
        <h4>Project: {{ $data->name }} {{ $data->pme_id }}</h4>
        <table class="tg">
                    <thead>
                    <tr>
                        <th class="tg-dvid">EMP ID</th>
                        <th class="tg-dvid">Full Name</th>
                        <th class="tg-dvid">PME ID</th>
                        <th class="tg-dvid">Coordinator</th>
                        <th class="tg-dvid">Attendance Signature</th>
                        <th class="tg-dvid">Working Hours</th>
                        <th class="tg-dvid">Working Hours Signature</th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach( $data->users as $p)
                            <tr>
                                <td>
                                    {{$p->profile_employee_id}}
                                </td>
                                <td>
                                    {{$p->first_name}} {{$p->last_name}}
                                </td>
                                <td>
                                    {{ $data->pme_id }}
                                </td>
                                <td>
                                    {{ $coodrindator }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>


                    @endforeach
                    </tbody>
                </table>
    <a style="float: right">Coordinator Signature</a>
        <div class="page_break"></div>
    @endforeach


</div>
{{--<script src="{{ asset('js/app.js') }}" type="text/js"></script>--}}
</body>
</html>
