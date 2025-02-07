@extends('layouts.email')

@section('content')
<p>Dear {{ $pilot->full_name }},</p>

<p>Thank you for enrolling in the HCF Pilot Passport {{ $data->title }} Challenge! You can view program requirements and track your progress on
    our website at <a href="https://www.vhcf.net/pilot_passport" alt="Website">https://www.vhcf.net/pilot_passport</a>.</p>

<p>Please remember that in order to get credit for visiting a participating airfield, you should plan to land and remain on the ground for 
    about 5 minutes. You can immediately verify that we logged your visit by refreshing your passport book on our website. We will also send an
    email confirmation with each qualifying visit (emails may be delayed up to 5 minutes).</p>

<p>If you did not enroll in this program or otherwise believe this email to be in error, please contact us at: 
    <a href="mailto:hcf-wm@vatusa.net" alt="Email">hcf-wm@vatusa.net</a>.</p>

<p>Thank you!<br> - vHCF Staff</p>
@endsection
