@extends('layout.users_master');

@section('dynamic_section')
    @if (empty($task_result))
        {
        <h1>No tasks to display.</h1>
        }
    @endif
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Task List</h1>
                <table class="table table-responsive table-striped">
                    @php
                        $i = 1;
                    @endphp
                    <tr>
                        <th>Sr.No</th>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Completion Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @foreach ($task_result as $task)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $task->task_name }}</td>
                            <td>{{ $task->task_description }}</td>
                            <td>{{ $task->deadline }}</td>
                            <td>{{ $task->status }}</td>
                            <td>

                                @if ($task->status != 'Completed')
                                    <a href="user_edit_task/{{ $task->task_id }}">
                                        <button class="btn btn-primary">
                                            <i class="fa-solid fa-file-pen"></i> Edit
                                        </button>
                                    </a>
                                    <a href="user_complete_task/{{ $task->task_id }}">
                                        <button class="btn btn-success"><i class="fa-solid fa-calendar-check"></i> Mark
                                            as Done
                                        </button>
                                    </a>
                                @else
                                    <button class="btn btn-success">
                                        <i class="fa-solid fa-check-double"></i> Completed
                                    </button>
                                @endif
                                <a href="user_delete_task/{{ $task->task_id }}">
                                    <button class="btn btn-danger">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </a>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
