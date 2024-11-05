@extends('layouts.email')
@section('content')
    <p>Dear {{ $visitor->name }},</p>

    <p>Congratulations, you have been accepted as a visitor in the Honolulu virtual ARTCC.</p><br>
    <p>You can find all the information for the various facilities within the ARTCC under the files page on the website, <a href="http://www.vhcf.net">www.vhcf.net</a>.</p><br>
    <p>Most visitors will be certified to control unrestricted fields through their current rating. It is highly recommended that you review the SOPs and LOAs located on the website prior to logging onto the network.</p><br>
    <p>Once again, congratulations on being accepted as a visitor in Honolulu and we hope to see you on the network soon! If you have any questions, feel free to email the DATM at <a href="mailto:hcf-datm@vatusa.net">hcf-datm@vatusa.net</a>.</p><br>

    <p>Best regards,</p>
    <p>HCF Staff</p>
@endsection
