@extends('layouts.app')

@section('content')
    <livestream 
        :user="{{ $user }}" 
        pusher-key="{{ config('broadcasting.connections.pusher.key') }}" 
        pusher-cluster="{{ config('broadcasting.connections.pusher.options.cluster') }}" 
        :group="{{ $group }}"
    />
@endsection