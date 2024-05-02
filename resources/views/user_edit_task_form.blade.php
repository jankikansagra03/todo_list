@extends('layout.users_master')
@section('dynamic_section')
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Edit Task</h2>

                <form method="POST" action="{{ URL::to('/') }}/user_update_task/{{ $result->task_id }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="tname1">Task Name:</label>
                        <input type="text" class="form-control" id="tname1" name="tname"
                            value="{{ $result->task_name }}">
                        @error('tname')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tdesc1">Task Description:</label>
                        <textarea name="tdesc" id="tdesc1" cols="30" rows="10" class="form-control">
                        {{ $result->task_description }}
                        </textarea>
                        @error('tdesc')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="date1">Completetion Date:</label>
                        <input type="date" class="form-control" id="date1" name="com_date"
                            value="{{ $result->deadline }}">
                        @error('com_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="submit" class="btn" value="Edit Task" />
                </form>

            </div>
        </div>
    </div>
@endsection
