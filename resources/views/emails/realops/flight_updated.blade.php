@extends('layouts.email')

@section('content')
<p>Dear {{ $pilot->full_name }},</p>

<p>This is an automated email to inform you Realops flight {{ $flight->flight_number }} has been updated. Please review the information below for the latest information and you can also view the latest information on the <a hret="https://www.vhcf.net/realops">Realops Dashboard</a>. Honolulu gate information and much more can be found in the <a hret="https://www.vhcf.net/pilots/guide/hnl">HNL Pilot Guide</a>.</p>

<p><b>Date: </b>{{ $flight->flight_date_formatted }}</p>
<p><b>Flight Number: </b>{{ $flight->flight_number }}</p>
<p><b>Callsign (if different from flight number): </b>{{ $flight->callsign }}</p>
<p><b>Departure Time: </b>{{ $flight->dep_time_formatted }}</p>
<p><b>Departure Airport: </b>{{ $flight->dep_airport }}</p>
<p><b>Arrival Airport: </b>{{ $flight->arr_airport }}</p>
<p><b>Estimated Enroute Time: </b>@if($flight->est_time_enroute) {{ $flight->est_time_enroute_formatted }} @else N/A @endif</p>
<p><b>Route: </b>@if($flight->route) {{ $flight->route }} @else N/A @endif</p>
@endsection
