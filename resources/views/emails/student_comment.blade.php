@extends('layouts.email')

@section('content')
<p>Dear {{ $trainer_name }},</p>

<p>One of your students has entered a comment on their training ticket. You can view the comment here: <a href="www.{{url('/')}}/dashboard/training/tickets/view/{{ $ticket_id }}">Training Ticket</a>.</p>
<br>

<p>If you have any thoughts/comments, please email the TA at <a href="mailto:hcf-ta@vatusa.net">hcf-ta@vatusa.net</a>.</p>
<br>

<p>Sincerely,</p>
<p>HCF ARTCC Training Team</p>
@endsection
