@extends('tenant.layouts.app')

@section('content')
    <tenant-configuration-general-data route="{{route('tenant.configuration')}}"></tenant-configuration-general-data>
    <tenant-configuration-software route="{{route('tenant.configuration')}}"></tenant-configuration-software>
@endsection
