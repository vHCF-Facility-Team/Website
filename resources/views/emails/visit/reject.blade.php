@extends('layouts.email')

@section('content')
    <p>Dear {{ $visitor->name }},</p>

    <p>{!! nl2br(e($visitor->reject_reason)) !!}</p>

    <p>If you have any questions, please contact the DATM at <a href="mailto:hcf-datm@vatusa.net">hcf-datm@vatusa.net</a>.</p>
    <br>

    <p>Best regards,</p>
    <p>HCF Staff</p>
@endsection
