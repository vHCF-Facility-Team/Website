@extends('layouts.email')

@section('content')
<p>Dear {{ $pilot->full_name }},</p>

<p>Congratulations on completing the HCF Pilot Passport {{ $data->title }} Challenge!! You've earned a reward, visible at: 
    <a href="https://www.vhcf.net/pilot_passport/achievements" alt="Website">https://www.vhcf.net/pilot_passport</a>.</p>

<p>Your achievement will be displayed on the HCF Website for all to see. You may adjust your privacy settings or disable this feature
    by visiting our website at the link above and selecting the "Settings" tab.
</p>

<p>If you did not enroll in this program or otherwise believe this email to be in error, please contact us at: 
    <a href="mailto:hcf-wm@vatusa.net" alt="Email">hcf-wm@vatusa.net</a>.</p>

<p>Thank you!<br> - vHCF Staff</p>
@endsection
