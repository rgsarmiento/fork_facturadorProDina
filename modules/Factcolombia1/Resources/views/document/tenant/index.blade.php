@extends('layouts.app')

@section('content')
    <tenant-document-document api="{{ env('SERVICE_FACT', '') }}" route="{{route('tenant.document')}}"></tenant-document-document>
@endsection
