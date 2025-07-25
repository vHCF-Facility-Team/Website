@extends('layouts.dashboard')

@section('title')
Update Controller
@endsection

@section('content')
@include('inc.header', ['title' => '<i class="fas fa-user"></i>&nbsp;Update ' . $user->full_name . ' (' . $user->id. ')'])

<div class="container">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#profile" role="tab" data-toggle="tab" style="color:black"><i class="fas fa-id-card"></i>&nbsp;Member Profile</a>
        </li>
        @if(Auth::user()->isAbleTo('roster') || Auth::user()->isAbleTo('train') || Auth::user()->hasRole('ec'))
        <li class="nav-item">
            <a class="nav-link" href="#certifications" role="tab" data-toggle="tab" style="color:black"><i class="fas fa-graduation-cap"></i>&nbsp;Controller Certifications</a>
        </li>
        @endif
        @if(Auth::user()->isAbleTo('roster') || Auth::user()->isAbleTo('events'))
        <li class="nav-item">
            <a class="nav-link" href="#events" role="tab" data-toggle="tab" style="color:black"><i class="fa-solid fa-chart-line"></i>&nbsp;Event Participation</a>
        </li>
        @endif
    </ul>
    {{ html()->form()->route('updateController', [$user->id])->open() }}
    @csrf
    @php
        $roster_disable = 'disabled';
        if(Auth::user()->isAbleTo('roster')) {
            $roster_disable = null;
        }
        $train_config_disable = 'disabled';
        if(Auth::user()->isAbleTo('roster') || Auth::user()->hasRole('ata')) {
           $train_config_disable = null;
        }
        $events_disable = 'disabled';
        if(Auth::user()->isAbleTo('roster') || Auth::user()->hasRole('ec')) {
            $events_disable = null;
        }
    @endphp
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="profile">
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="cid">CID</label>
                        {{ html()->text('cid', $user->id)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="rating">Rating</label>
                        {{ html()->text('rating', $user->rating_long)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="fname">First Name</label>
                        {{ html()->text('fname', $user->fname)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="lname">Last Name</label>
                        {{ html()->text('lname', $user->lname)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="email">Email</label>
                        {{ html()->text('email', $user->email)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="initials">Initials</label>
                        {{ html()->text('initials', $user->initials)->class(['form-control'])->attributes([$roster_disable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        @if($user->visitor == 1)
                            <label for="visitor_from">Visitor From</label>
                            {{ html()->text('visitor_from', $user->visitor_from)->class(['form-control'])->attributes([$roster_disable]) }}
                            {{ html()->hidden('status', $user->status) }}
                        @else
                            <label for="status">Status</label>
                            {{ html()->select('status', $user->user_status, $user->status)->class(['form-control'])->attributes([$roster_disable]) }}
                        @endif
                    </div>
                    <div class="col-sm-6">
                        *Note: Read-only roster data is sync'd with the VATSIM CERT database nightly
                    </div>
                </div>
            </div>
            <hr>
            <h6><i class="fas fa-building"></i>&nbsp;Facility Staff Settings</h6>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="staff">Facility Staff</label>
                        {{ html()->select('staff', $user->facility_staff, $user->staff_position)->class(['form-control'])->attributes([$roster_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="events_staff">Events Staff</label>
                        {{ html()->select('events_staff', $user->events_staff, $user->events_position)->class(['form-control'])->attributes([$events_disable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="training">Training Staff</label>
                        {{ html()->select('training', $user->training_staff, $user->train_position)->class(['form-control'])->attributes([$roster_disable]) }}
                    </div>
                    @if($user->hasRole('mtr') || $user->hasRole('ins'))
                    <div class="col-sm-6">
                        <label for="max">Training Level</label>
                        {{ html()->select('max', $user->training_level, $user->max)->class(['form-control'])->attributes([$train_config_disable]) }}
                    </div>
                    @endif
                </div>
            </div>
            <hr>
            <h6><i class="fas fa-user-cog"></i>&nbsp;Account Settings</h6>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-10">
                        @php
                            $allow_training = ($user->canTrain == 1) ? true : false;
                            $is_visitor = ($user->visitor == 1) ? true : false;
                            $allow_events = ($user->canEvents == 1) ? true : false;
                            $api_exempt = ($user->api_exempt == 1) ? true : false;
                        @endphp
                        <label for="canTrain">Allow Training?</label>
                        {{ html()->checkbox('canTrain', $allow_training, 1)->attributes([$roster_disable]) }}
                    </div>
                    <div class="col-sm-10">
                        <label for="visitor">Visitor?</label>
                        {{ html()->checkbox('visitor', $is_visitor, 1)->attributes(['disabled']) }}
                        @if($user->visitor == 1)
                            <a href="/dashboard/admin/roster/visit/remove/{{ $user->id }}">(Remove from Roster)</a>
                        @endif
                    </div>
                    <div class="col-sm-10">
                        <label for="canEvents">Allow Signing up for Events?</label>
                        {{ html()->checkbox('canEvents', $allow_events, 1)->attributes([$roster_disable]) }}
                    </div>
                    @if($user->visitor != 1)
                    <div class="col-sm-10">
                        <label for="api_exempt">Exempt from VATUSA API Roster Update?</label>
                        {{ html()->checkbox('api_exempt', $api_exempt, 1)->attributes([$roster_disable]) }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <button class="btn btn-success text-nowrap" type="submit"><i class="fas fa-save"></i>&nbsp;Save</button>
                </div>

                <div class="col-sm-1">
                    <a href="{{ url()->previous() }}" class="btn btn-danger text-nowrap"><i class="fas fa-undo"></i>&nbsp;Cancel</a>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="certifications">
            <br>
            <h6><span class="badge badge-warning text-light">New!</span>&nbsp;This control now complies with GCAP requirements.<br>Please review the latest version of HCF 3120.1 and 3120.2 prior to updating a controller's certifications.</h6>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        @php
                            $solo_disable = $unres_gnd_disable = $unres_twr_disable = $unres_app_disable = $center_disable = 'disabled';
                            $hnl_disable = $hnl_app_disable = 'disabled';
                            if(Auth::user()->isAbleTo('roster')) {
                                $solo_disable = $unres_gnd_disable = $unres_twr_disable = $unres_app_disable = $center_disable = null;
                                $hnl_disable = $hnl_app_disable = null;
                            }
                            elseif (Auth::user()->isAbleTo('train') && is_numeric(Auth::user()->max)) {
                                $unres_gnd_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_UNRES_GND')) ? null : 'disabled';
                                $solo_disable = (Auth::user()->max > Auth::user()->getMagicNumber('TRAIN_UNRES_GND')) ? null : 'disabled';
                                $unres_twr_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_UNRES_TWR')) ? null : 'disabled';
								$hnl_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_HNL_TWR')) ? null : 'disabled';
                                $unres_app_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_UNRES_APP')) ? null : 'disabled';
                                $hnl_app_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_HNL_APP')) ? null : 'disabled';
                                $center_disable = (Auth::user()->max >= Auth::user()->getMagicNumber('TRAIN_CTR')) ? null : 'disabled';
                            }
                        @endphp
                        {{ html()->hidden('del', $user->del) }}
                        <label for="gnd">Unrestricted Ground/Clearance Delivery</label>
                        {{ html()->select('gnd', $user->uncertified_certified, $user->gnd)->class(['form-control'])->attributes([$unres_gnd_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="twr">Unrestricted Tower</label>
                        {{ html()->select('twr', $user->Uncertified_solo_certified, $user->twr)->class(['form-control'])->attributes([$unres_twr_disable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="app">Unrestricted Approach</label>
                        {{ html()->select('app', $user->uncertified_solo_certified, $user->app)->class(['form-control'])->attributes([$unres_app_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="ctr">Center Certification</label>
                        {{ html()->select('ctr', $user->uncertified_solo_certified, $user->ctr)->class(['form-control'])->attributes([$center_disable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="twr_solo_fields">Unrestricted Solo Certifications (list facility IDs)</label>
                        {{ html()->text('twr_solo_fields', $user->twr_solo_fields)->class(['form-control'])->attributes(['maxlength' => 255, $solo_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="twr_solo_expires" class="form-label">Solo Expiration Date</label>
                        {{ html()->text('solo_expires', $user->solo_exp)->class(['form-control'])->attributes(['disabled']) }}
                    </div>
                </div>
                @if(Auth::user()->isAbleTo('roster'))
                <div class="row mt-2">
                    <div class="col-12">
                        <span data-toggle="modal" data-target="#remove_solo_certs">
                            <button type="button" class="btn btn-danger text-nowrap"><i class="fa-solid fa-user-xmark mr-2"></i>Remove Solo Certifications</button>
                        </span>
                    </div>
                </div>
                <div class="modal fade" id="remove_solo_certs" tabindex="-1" role="dialog" aria-labelledby="removeSoloCerts" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Remove all solo certifications</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                                <div class="modal-body">
                                    <p>This action will immediately revoke <b><u>ALL</u></b> solo certifications for 
                                        {{ $user->full_name }} from both the facility roster and VATUSA.</p>
                                </div>
                                <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <a href="/dashboard/admin/roster/solo/{{ $user->id }}" role="button" class="btn btn-danger text-nowrap">Revoke Solo Certs Now!</a>
                                </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <hr>
            <h6><i class="fas fa-level-up-alt"></i>&nbsp;Tier 2 Facility Certifications</h6>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="hnl_del">Honolulu Clearance Delivery</label>
                        {{ html()->select('hnl_del', $user->uncertified_certified, $user->hnl_del)->class(['form-control'])->attributes([$hnl_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="hnl_gnd">Honolulu Ground</label>
                        {{ html()->select('hnl_gnd', $user->uncertified_certified, $user->hnl_gnd)->class(['form-control'])->attributes([$hnl_disable]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="hnl_twr">Honolulu Tower</label>
                        {{ html()->select('hnl_twr', $user->uncertified_certified, $user->hnl_twr)->class(['form-control'])->attributes([$hnl_disable]) }}
                    </div>
                    <div class="col-sm-6">
                        <label for="hnl_app">Honolulu Approach</label>
                        {{ html()->select('hnl_app', $user->uncertified_certified, $user->hnl_app)->class(['form-control'])->attributes([$hnl_app_disable]) }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <button class="btn btn-success text-nowrap" type="submit"><i class="fas fa-save"></i>&nbsp;Save</button>
                </div>
                <div class="col-sm-1">
                    <a href="{{ url()->previous() }}" class="btn btn-danger text-nowrap"><i class="fas fa-undo"></i>&nbsp;Cancel</a>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="events">
            <br>
            <h5>Controler Event Participation Tracking</h5>
            <div class="row mb-2">
                <div class="col-6">
                    <div class="card p-3">
                        <h5 class="card-title">Stats Last 12-Months</h5>
                        <div class="card-body">
                            Event Participation: {{ $event_stats->events_total_12mo }}<br>
                            Event Hours Logged: {{ $event_stats->hours_total_12mo }}<br>
                            Event No-Shows: {{ $event_stats->no_shows_12mo }}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card p-3">
                        <h5 class="card-title">Stats Lifetime</h5>
                        <div class="card-body">
                            Event Participation: {{ $event_stats->events_total }}<br>
                            Event Hours Logged: {{ $event_stats->hours_total }}<br>
                            Event No-Shows: {{ $event_stats->no_shows }}
                        </div>
                    </div>
                </div>            
            </div>
            <table class="table table-striped">
                <thead>
                    <tr class="text-center">
                        <th>Date</th>
                        <th>Event Name</th>
                        <th>Position<br>Assigned</th>
                        <th>Connection<br>Log</th>
                        <th>Time Logged<br>(hours)</th>
                        <th>No Show?</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr class="text-center">
                            <td>{{ $event->event_date }}</td>
                            <td><a href="/dashboard/controllers/events/view/{{ $event->id }}" alt="Link to event" target="_blank"></a>{{ $event->event_name }}</td>
                            <td>{{ $event->position_assigned }}</td>
                            <td>
                                @foreach ($event->connection as $connection)
                                    {{ $connection->callsign }} ({{ $connection->start }}-{{ $connection->end }}) <br>
                                @endforeach
                            </td>
                            <td>{{ $event->time_logged }}</td>
                            <td>
                                @if($event->no_show == 1)
                                    <span class="text-danger" data-toggle="tooltip" title="Marked No-Show"><i class="fas fa-user-tag"></i></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if (count($events) == 0)
                        <tr class="text-center">
                            <td colspan="6">No event history found for this controller.</td>
                        </tr>
                    @endif        
                </tbody>
            </table>
        </div>
    </div>
    {{ html()->form()->close() }}
</div>
<script src="{{mix('js/roster.js')}}"></script>
@endsection
