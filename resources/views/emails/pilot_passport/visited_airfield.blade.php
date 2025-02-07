@extends('layouts.email')

@section('content')
<p>Dear {{ $pilot->full_name }},</p>

<p>Congratulations on your visit to {{ $data->name }}! You've earned a stamp in your pilot passport book, visible at: 
    <a href="https://www.vhcf.net/pilot_passport/passport_book" alt="Website">https://www.vhcf.net/pilot_passport</a>.</p>

@if(!is_null($data->description))
    <p>{{ $data->description }}</p>

@endif
<p>If you did not enroll in this program or otherwise believe this email to be in error, please contact us at: 
    <a href="mailto:hcf-wm@vatusa.net" alt="Email">hcf-wm@vatusa.net</a>.</p>

<p>Thank you!<br> - vHCF Staff</p>
@endsection
