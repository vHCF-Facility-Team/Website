@extends('layouts.master')

@section('title')
{{ $afld }} Ramp Status
@endsection

@push('custom_header')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<link rel="stylesheet" href="{{ mix('css/pilots_guide.css') }}" />
@endpush

@section('content')
@if( $afld == 'HNL')
  @include('inc.header', ['title' => '<center>Daniel K. Inouye International Airport (HNL) Ramp/Gate Status</center>', 'type' => 'external'])
@endif

<div class="container">
  <div style="width:100%; height:70vh">
    <div id="map"></div>
  </div>
  <div id="legend">
    <div class="legenditem"><span class="taxiarr"></span> Arrival</div>
    <div class="legenditem"><span class="taxidep"></span> Departure</div>
    <div class="legenditem"><span class="nofp"></span> Other</div>
  </div>
</div>
<script>
  @if($afld == 'HNL')
  const centroid = [21.31851, -157.92862];
  const maxLatLon = [21.3460, -157.9604];
  const minLatLon = [21.2958, -157.9034];
  @endif
</script>
<script src="{{mix('js/pilots_guide.js')}}"></script>
@endsection