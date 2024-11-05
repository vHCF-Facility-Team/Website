@extends('layouts.email')

@section('content')
<p>Dear {{ $pilot->full_name }},</p>

<p>This is an automated email to confirm that you have cancelled your assignment for Realops flight {{ $flight->flight_number }}. If you believe this is a mistake, you can rebid for the flight on the <a href="https://www.vhcf.net/realops">Realops Dashboard</a> if bidding is still open, or you can send an email to <a href="mailto:hcf-ec@vatusa.net">hcf-ec@vatusa.net</a>.</p>
@endsection
