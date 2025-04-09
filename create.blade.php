@extends('frontend.layouts.main')
@section('content')
<?php
$children = isset($data) ? $data->children : collect();
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb py-3 mb-4">
                <li class="breadcrumb-item active">
                    <a href="javascript:void(0);">MEMBERS</a>
                </li>
            </ol>
        </nav>
        <div class="col-12">
            <div class="col-12">
                @include('backend.includes.notifications')
            </div>
            <div class="card">
                <div class="card-header flex-column flex-md-row">
                    <div class="head-label">
                        <h1>{{ isset($data) ? 'Edit' : 'Next' }} Christmas Party Event</h1>
                    </div>
                </div>
                <div class="card-content card-2">
                    <div class="card-body card-dashboard">
                        @if(isset($data))
                            {!! Form::model($data, ['route' => ['events.update', $data->id], 'method' => 'PUT']) !!}
                        @else
                            {!! Form::open(['route' => 'events.store', 'method' => 'POST']) !!}
                        @endif

                        {!! csrf_field() !!}
                        <div class="form-group">
                            {{ Form::label('member_name', 'MEMBER NAME') }}
                            {{ Form::text('member_name', old('member_name', isset($data) ? $data->member_name : ''), ['class' => 'form-control', 'required']) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('acc', 'ACC') }}
                            {{ Form::text('acc', old('acc', isset($data) ? $data->acc : ''), ['class' => 'form-control', 'required']) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('home_address', 'HOME ADDRESS') }}
                            {{ Form::text('home_address', old('home_address', isset($data) ? $data->home_address : ''), ['class' => 'form-control',  'placeholder' => 'Enter Only capital letters ','required']) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('email', 'EMAIL ADDRESS') }}
                            {{ Form::email('email', old('email', isset($data) ? $data->email : ''), ['class' => 'form-control', 'required']) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('phone', 'PHONE') }}
                            {{ Form::text('phone', old('phone', isset($data) ? $data->phone : ''), ['class' => 'form-control', 'required']) }}
                        </div>

                        <div class="form-group">
                            {{ Form::label('location', 'EVENT LOCATION') }}
                            {{ Form::text('location', old('location', isset($data) ? $data->location : ''), ['class' => 'form-control', 'required']) }}
                        </div>

                        <h4>REGISTER CHILD</h4><br><br>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <h4 class="card-title mb-2">Add Child</h4>
                                    <div class="card-content">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="children_table">
                                                <tbody>
                                                    @if(count($children))
                                                            @foreach ($children as $i => $child)
                                                                <tr>
                                                                    <td>{!! Form::text("child_name[$i]", $child->child_name, ['class' => 'form-control', 'placeholder' => 'Enter child name', 'required' => $i === 0]) !!}</td>
                                                                    <td>{!! Form::text("gender[$i]", $child->gender, ['class' => 'form-control', 'placeholder' => 'Enter child gender', 'required' => $i === 0]) !!}</td>
                                                                    <td>{!! Form::text("age[$i]", $child->age, ['class' => 'form-control', 'placeholder' => 'Enter child age', 'required' => $i === 0]) !!}</td>
                                                                    <td><a href="javascript:;" class="btn btn-danger remove-btn" title="Delete" style="{{ $i === 0 ? 'visibility: hidden;' : 'visibility: visible;' }}"><i class="fa fa-times"></i></a></td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td>{!! Form::text('child_name[]', null, ['class' => 'form-control', 'placeholder' => 'Enter child name', 'required']) !!}</td>
                                                                <td>{!! Form::text('gender[]', null, ['class' => 'form-control', 'placeholder' => 'Enter child gender', 'required']) !!}</td>
                                                                <td>{!! Form::text('age[]', null, ['class' => 'form-control', 'placeholder' => 'Enter child age', 'required']) !!}</td>
                                                                <td><a href="javascript:;" class="btn btn-danger remove-btn" title="Delete" style="visibility: hidden;"><i class="fa fa-times"></i></a></td>
                                                            </tr>
                                                     @endif  
                                                </tbody>
                                            </table>
                                            <a href="javascript:void(0);" class="btn btn-success add-child"><i class="fa fa-plus-square"></i> Add Child</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::submit(isset($data) ? 'Update' : 'Next', ['class' => 'btn btn-primary']) !!}
                        {{-- <a href="{{ route('events.index') }}" class="btn btn-primary">Next to Review</a> --}}
                        {{-- <a href="{{route('events.view',$event->id)}}" class="btn">View </a> --}}

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')<script>
    $(document).on('click', '.add-child', function () {
        const tBody = $('#children_table tbody');
        if (tBody.find("tr").length < 4) {
            const tRow = tBody.find('tr:first').clone();
            tRow.find(":input").val(""); // Clear input values
            tRow.find(".remove-btn").css('visibility', 'visible'); // Show delete button for new row
            tBody.append(tRow);       //here updateChildInputNames  function call 
            updateChildInputNames(tBody);
        } else {
            alert("You can only add up to 4 children.");  
        }
    });

    $(document).on('click', '.remove-btn', function () {
        const tBody = $('#children_table tbody');
        $(this).closest('tr').remove();
        updateChildInputNames(tBody);  //here updateChildInputNames  function call
        toggleDeleteButtons(tBody);    //hete toggleDeleteButtons function call 
    });

    
    function updateChildInputNames(tBody) {
        tBody.find("tr").each((index, row) => { 
            $(row).find(":input").each((_, input) => { 
                const name = $(input).attr("name").replace(/\[\d+\]/, `[${index}]`);
                $(input).attr("name", name);
                if ($(input).is("[id]")) {
                    $(input).attr("id", name);
                }
                if ($(input).prev("label").is("[for]")) {
                    $(input).prev("label").attr("for", name);
                }
                // Set 'required' for the first row only
                if (index === 0) {
                    $(input).attr("required", true);
                } else {
                    $(input).removeAttr("required");
                }
            });
        });
    }

    function toggleDeleteButtons(tBody) {
        tBody.find("tr").each((index, row) => {
            const removeBtn = $(row).find(".remove-btn");
            if (index === 0) {
                removeBtn.css('visibility', 'hidden');
            } else {
                removeBtn.css('visibility', 'visible');
            }
        });
    }
  
</script>

@endsection


