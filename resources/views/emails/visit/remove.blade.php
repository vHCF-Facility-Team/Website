@extends('layouts.email')

@section('content')
<p>Dear {{ $visitor->full_name }},</p>
<br>
<p>This email is to inform you that you have been removed from the ZTL Visiting Roster. The causes for removal can be found in HCF ARTCC General Operating Procedures 1100.1F Chapter 2.4.</p>
<p>If you are still unsure of the reason for your removal, please contact the DATM at <a href="mailto:hcf-datm@vatusa.net">hcf-datm@vatusa.net</a>.</p>
<br>
<p>Sincerely,</p>
<p>vHCF ARTCC Staff</p>
@endsection
