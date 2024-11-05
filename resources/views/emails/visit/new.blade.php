@extends('layouts.email')

@section('content')
<p>Dear {{ $visitor->name }},</p>

<p>Your visitor application has been successfully submitted. In the next 24 hours, you will be assigned the HCF Visitor Exam through the 
    VATUSA Academy. Please visit the <a href="https://academy.vatusa.net/">VATUSA Academy</a> and under "Academy Courses" select "All 
    Courses". You should then see the HCF Visiting Exams course once it has been assigned.</p>
<p>The HCF Visitor Exam will ensure you have the skills required to control at HCF. Certain questions will require you to reference various 
    SOPs and LOAs that can be found on the <a href="https://www.vhcf.net/controllers/files">HCF Website</a>. Some questions will ensure 
    you understand the rules for being a member at HCF. The answers to these questions can be found in our General Operating Procedures.</p>
<p>You will have seven (7) days to complete the exams and you must pass each with a score of 80% or better. If you have any questions or 
    concerns, please email the DATM at <a href="mailto:hcf-datm@vatusa.net">hcf-datm@vatusa.net</a>.</p>
<p>Best regards,</p>
<p>HCF Staff</p>
@endsection
