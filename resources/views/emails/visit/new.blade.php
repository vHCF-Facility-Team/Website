@extends('layouts.email')

@section('content')
<p>Dear {{ $visitor->name }} (CID {{ $visitor->cid }}),</p>

<p>Your visitor application has been successfully submitted. </p>
<p>The HCF SOPs and LOAs can be found on the <a href="https://sops.vhcf.net/">HCF SOP Website</a>.</p>

<p>Best regards,</p>
<p>HCF Staff</p>
@endsection
