<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registrations;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;

class UserController extends Controller
{
    public function user_dashboard()
    {
        return view('user_dashboard');
    }
    public function logout()
    {
        session()->remove('user_uname');
        return redirect('login');
    }

    public function update_form(Request $request)
    {
        $email = session()->get('user_uname');
        $result = Registrations::where('email', $email)->first();
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'mobile_number' => 'required|regex:/^[0-9]{10}$/',
            'hobbies' => 'required|array|min:1',
            'profile_picture' => 'mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'fullname.required' => 'The full name field is required.',
            'fullname.string' => 'The full name must be a string.',
            'fullname.max' => 'The full name may not be greater than 255 characters.',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid.',
            'mobile_number.required' => 'The mobile number field is required.',
            'mobile_number.string' => 'The mobile number must be a string.',
            'mobile_number.max' => 'The mobile number may not be greater than 20 characters.',
            'hobbies.required' => 'Please select a hobby.',
            'mobile_number.regex' => 'Number must not be greater than 10 digits.',
            'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
            'profile_picture.max' => 'The profile picture may not be greater than 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect('edit_profile')->withErrors($validator)->withInput();
        }
        //  $email = session()->get('user_uname');
        $hobbies = implode(",", $request->input('hobbies'));
        if ($request->hasFile('profile_picture')) {
            $pic_name = uniqid() . $request->profile_picture->getClientOriginalName();
            $data1 = Registrations::where('email', $email)->update(array('fullname' => $request->fullname, 'gender' => $request->gender, 'mobile' => $request->mobile_number, 'hobbies' => $hobbies, 'profile_picture' => $pic_name));
        } else {
            $data1 = Registrations::where('email', $email)->update(array('fullname' => $request->fullname, 'gender' => $request->gender, 'mobile' => $request->mobile_number, 'hobbies' => $hobbies));
        }

        if ($data1) {
            if ($request->hasfile('profile_picture')) {
                $request->profile_picture->move('uploads/', $pic_name);
                unlink("uploads/" . $result->profile_picture);
            }
            session()->flash('success', 'Profile Updtated Successfully');
        } else {
            session()->flash('error', 'Error in updating profile');
        }

        return redirect('user_edit_profile');
    }

    public function user_change_password()
    {
        return view('user_change_password');
    }

    public function user_update_password(Request $request)
    {
        $email = session()->get('user_uname');
        $validator = Validator::make($request->all(), [
            'old_pwd' => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,20}$/',
            'password_confirmation' => 'required',

        ], [
            'old_pwd.required' => 'Old Password Cannot be empty',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must contain one small letter on capital letter, number and a special symbol.',
            'password_confirmation.required' => 'Confirm Password cannot be empty',

        ]);

        if ($validator->fails()) {
            return redirect('change_password')->withErrors($validator)->withInput();
        }

        $result = Registrations::where('email', $email)->first();
        //echo $result->password;
        if ($result->password == $request->old_pwd) {
            $update_pwd = Registrations::where('email', $email)->update(array('password' => $request->password));
            if ($update_pwd) {
                session()->flash('success', 'Password updated successfully');
            } else {
                session()->flash('error', 'Error in updating Password');
            }
        } else {
            session()->flash('error', 'Incorrect old Password');
        }
        return redirect('user_change_password');
    }
    public function user_edit_profile()
    {
        $email = session()->get('user_uname');
        $result = Registrations::where('email', $email)->first();
        return view('user_edit_profile_form', compact('result'));
    }
    public function user_add_task_form()
    {
        return view('user_add_task_form');
    }
    public function user_add_task(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'tname' => 'required',
            'tdesc' => 'required',
            'com_date' => 'required',
        ], [
            'tname.required' => 'Task name is a required field',
            'tdesc.required' => 'Task description is a required field',
            'com_date.required' => 'Task date is a required field'
        ]);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }
        $email = session()->get('user_uname');
        $result = Registrations::where('email', $email)->first();

        $task = new Task();
        $task->task_name = $request->tname;
        $task->task_description = $request->tdesc;
        $task->deadline = $request->com_date;
        $task->registration_id = $result->id;

        if ($task->save()) {
            session()->flash('success', 'Task Added Successfully');
            return redirect('user_view_list');
        } else {
            session()->flash('error', 'Error in adding task');
            return redirect('user_add_task');
        }
    }

    public function user_task_list()
    {
        $em = session()->get('user_uname');
        $result = Registrations::where('email', $em)->first();

        $task_result = Task::where('registration_id', $result->id)->get();

        return view('display_user_task', compact('task_result'));
    }
    public function user_edit_task($task_id)
    {
        $result = Task::where('task_id', $task_id)->first();
        return view('user_edit_task_form', compact('result'));
    }
    public function user_complete_task($task_id)
    {
        $updt = Task::where('task_id', $task_id)->update(array('status' => 'Completed'));
        if ($updt) {
            session()->flash('success', 'Task marked as completed.');
        } else {
            session()->flash('error', 'Error in marking task as completed.');
        }
        return redirect('user_view_list');
    }
    public function user_delete_task($task_id)
    {
        $result = Task::where('task_id', $task_id)->delete();
        if ($result) {
            session()->flash('success', 'Task Deleted Successfully.');
        } else {
            session()->flash('error', 'Error in deleting task.');
        }
        return redirect('user_view_list');
    }

    public function user_update_task(Request $request, $task_id)
    {
        //$id = session()->get('task_id');
        $updt = Task::where('task_id', $task_id)->update(array('task_name' => $request->tname, 'task_description' => $request->tdesc, 'deadline' => $request->com_date));
        if ($updt) {
            session()->flash('success', 'Task updated successfully.');
        } else {
            session()->flash('error', 'Error in updating task.');
        }
        return redirect('user_view_list');
    }
    public function user_view_completed_list()
    {
        $task_result = Task::where('status', 'Completed')->get();
        return view('display_user_task', compact('task_result'));
    }
    public function user_view_pending_list()
    {
        $task_result = Task::where('status', 'Pending')->get();
        return view('display_user_task', compact('task_result'));
    }
}
