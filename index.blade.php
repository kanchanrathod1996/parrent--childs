@extends('frontend.layouts.main')

@section('styles')
<style>
    .btn{
        text-decoration: underline;
        color: black;
        
    }

</style>
@stop
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb py-3 mb-4">
                <li class="breadcrumb-item active">
                    <a href="{{ route('events.index') }}">Events</a>
                </li>
            </ol>
        </nav>

        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    @include('frontend.includes.notifications')
                </div>
            </div>

            <div class="card">
                <div class="card-header flex-column flex-md-row">
                    <div class="head-label">
                        <h1>Event List</h1>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('events.create') }}" class="btn btn-primary">Add New Event</a>
                    </div>
                </div>

                <div class="card-content card-2">
                    <div class="card-body card-dashboard">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Member Name</th>
                                    <th>ACC</th>
                                    <th>Home Address</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                           
                                    <tr>
                                        <td>{{ $event->member_name }}</td>
                                        <td>{{ $event->acc }}</td>
                                        <td>{{ $event->home_address }}</td>
                                        <td>{{ $event->email }}</td>
                                        <td>{{ $event->phone }}</td>
                                        <td>{{ $event->location }}</td>
                                        <td>
                                            <a href="{{ route('events.edit', $event->id) }}" class="btn ">Edit</a>
                                            {{-- @dd($event); --}}
                                            <form action="" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                {{-- <button type="submit" class="btn btn-primary">Delete</button> --}}
                                            </form>
                                            <a href="{{route('events.view',$event->id)}}" class="btn">View </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
