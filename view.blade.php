@extends('frontend.layouts.main')

@section('styles')
<style>
    .btn-custom {
        text-decoration: underline;
        color: white;
        background-color: #007bff; 
        border: none;
    }
    .btn-custom:hover {
        background-color: #0056b3; 
    }
    .child-info {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
    }
    .card-header {
        background-color: #f7f7f7; 
        border-bottom: 1px solid #e0e0e0;
    }
    .child-info .col {
        padding: 10px;
    }
</style>
@stop

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            @include('backend.includes.notifications')
        </div>
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb py-3 mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ route('events.index') }}">Events</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $event->member_name }}</li>
            </ol>
        </nav>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4">{{ $event->member_name }} - Event Details</h1>
                </div>

                <div class="card-body">
                    <h4>Event Information</h4>
                    <ul class="list-unstyled">
                        <li><strong>ACC:</strong> {{ $event->acc }}</li>
                        <li><strong>Home Address:</strong> {{ $event->home_address }}</li>
                        <li><strong>Email:</strong> {{ $event->email }}</li>
                        <li><strong>Phone:</strong> {{ $event->phone }}</li>
                        <li><strong>Location:</strong> {{ $event->location }}</li>
                    </ul>

                    <h2 class="mt-4">Children:</h2>
                    @if ($event->children->isEmpty())
                        <p>No children registered for this event.</p>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-4"><strong>Child's Name</strong></div>
                                    <div class="col-md-4"><strong>Gender</strong></div>
                                    <div class="col-md-4"><strong>Age</strong></div>
                                </div>
                            </div>
                            @foreach ($event->children as $child)
                                <div class="col-12">
                                    <div class="row child-info">
                                        <div class="col-md-4">{{ $child->child_name }}</div>
                                        <div class="col-md-4">{{ $child->gender }}</div>
                                        <div class="col-md-4">{{ $child->age }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-custom">Back to Edit</a>
                        {{-- Uncomment if delete functionality is needed --}}
                        {{-- <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-custom" onclick="return confirm('Are you sure you want to delete this event?');">Delete</button>
                        </form> --}}
                        {{-- <a href="{{ route('events.index') }}" class="btn btn-custom">Back to Event List</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
