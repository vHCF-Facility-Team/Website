@extends('layouts.master')

@section('title')
Roster
@endsection

@push('custom_header')
<link rel="stylesheet" href="{{ mix('css/roster.css') }}" />
@endpush

@section('content')
@include('inc.header', ['title' => 'Roster', 'type' => 'external'])

<div class="container">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-link active" href="#hcontrollers" role="tab" data-toggle="tab"><i class="fas fa-home"></i>&nbsp;Home Controllers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-link" href="#vcontrollers" role="tab" data-toggle="tab"><i class="fas fa-suitcase"></i>&nbsp;Visiting Controllers</a>
        </li>
    </ul>
    @php
    $tabs = ['hcontrollers', 'vcontrollers'];
    @endphp
    <div class="tab-content">
        @foreach($tabs as $tab)
        @if($loop->first)
        <div role="tabpanel" class="tab-pane active" id="{{ $tab }}">
            @else
            <div role="tabpanel" class="tab-pane" id="{{ $tab }}">
                @endif
                <table class="table table-bordered table-striped">
                    <thead class="sticky">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="text-center">Initials</th>
                            <th scope="col" class="text-center">Rating</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Unrestricted<br>Fields</th>
                            <th scope="col" class="text-center">HNL<br>Tier 2</th>
                            <th scope="col" class="text-center">HCF<br>Enroute</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($$tab as $c)
                        <tr>
                            <td>
                                @if($c->hasRole('atm'))
                                <span class="badge badge-danger">ATM</span>
                                @elseif($c->hasRole('datm'))
                                <span class="badge badge-danger">DATM</span>
                                @elseif($c->hasRole('ta'))
                                <span class="badge badge-danger">TA</span>
                                @elseif($c->hasRole('wm'))
                                <span class="badge badge-primary">WM</span>
                                @elseif($c->hasRole('awm'))
                                <span class="badge badge-primary">AWM</span>
                                @elseif($c->hasRole('ec'))
                                <span class="badge badge-primary">EC</span>
                                @elseif($c->hasRole('aec'))
                                <span class="badge badge-primary">AEC</span>
                                @elseif($c->hasRole('fe'))
                                <span class="badge badge-primary">FE</span>
                                @elseif($c->hasRole('afe'))
                                <span class="badge badge-primary">AFE</span>
                                @elseif($c->hasRole('ins'))
                                <span class="badge badge-info">INS</span>
                                @elseif($c->hasRole('mtr'))
                                <span class="badge badge-info">MTR</span>
                                @endif
                                {{ $c->backwards_public_name }}
                            </td>
                            <td class="text-center">{{$c->initials}}</td>
                            <td class="text-center">{{ $c->rating_short }}</td>
                            <td class="text-center">{{ $c->status_text }}</td>
                            <!-- Unrestricted -->
                            <td class="text-center">
                                @if($c->gnd > $c->getMagicNumber('UNCERTIFIED'))
                                <span class="badge badge-primary">DEL</span>
                                <span class="badge badge-success">GND</span>
                                @endif
                                @if($c->twr === $c->getMagicNumber('SOLO_CERTIFICATION'))
                                <span class="badge badge-warning text-light" data-toggle="tooltip" data-html="true" title="Cert Expires: {{ $c->solo }}<br>{{$c->twr_solo_fields}}">TWR-SOLO</span>
                                @elseif($c->twr > $c->getMagicNumber('UNCERTIFIED'))
                                <span class="badge badge-danger">TWR</span>
                                @endif
                                @if($c->app === $c->getMagicNumber('SOLO_CERTIFICATION'))
                                <span class="badge badge-warning text-light" data-toggle="tooltip" data-html="true" title="Cert Expires: {{ $c->solo }}<br>{{$c->twr_solo_fields}}">APP-SOLO</span>
                                @elseif($c->app > $c->getMagicNumber('UNCERTIFIED'))
                                <span class="badge badge-info">APP</span>
                                @endif
                            </td>
                            <!-- HCF Tier 2 -->
                            <?php
                                //  The LEGACY cases below are a temporary measure to ease transition into GCAP. These can be removed when
                                //  the facility roster has been fully updated to account for the new Tier 1 structure at CLT.
                            ?>
                            <td class="text-center">
                                @if(($c->hnl_del > $c->getMagicNumber('UNCERTIFIED'))||($c->del === $c->getMagicNumber('LEGACY_MAJOR_CERTIFIED')))
                                <span class="badge badge-primary">DEL</span>
                                @endif
                                @if(($c->hnl_gnd > $c->getMagicNumber('UNCERTIFIED'))||($c->gnd === $c->getMagicNumber('LEGACY_MAJOR_CERTIFIED')))
                                <span class="badge badge-success">GND</span>
                                @endif
                                @if(($c->hnl_twr > $c->getMagicNumber('UNCERTIFIED'))||($c->twr === $c->getMagicNumber('LEGACY_MAJOR_CERTIFIED')))
                                <span class="badge badge-danger">TWR</span>
                                @endif
                                @if(($c->hnl_app > $c->getMagicNumber('UNCERTIFIED'))||($c->app === $c->getMagicNumber('LEGACY_MAJOR_CERTIFIED')))
                                <span class="badge badge-info">APP</span>
                                @endif
                            </td>
                            <!-- Enroute -->
                            <td class="text-center">
                                @if($c->ctr === $c->getMagicNumber('SOLO_CERTIFICATION'))
                                <span class="badge badge-warning text-light" data-toggle="tooltip" data-html="true" title="Cert Expires: {{ $c->solo }}<br>{{$c->twr_solo_fields}}">HCF-SOLO</span>
                                @elseif($c->ctr > $c->getMagicNumber('UNCERTIFIED'))
                                <span class="badge badge-secondary">HCF</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
    @endsection
