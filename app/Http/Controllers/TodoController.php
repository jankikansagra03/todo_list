<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Registrations;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

class TodoController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }
    public function about()
    {
        return view('about');
    }
    public function contact()
    {
        return view('contact');
    }
    public function login()
    {
        return view('login');
    }
    public function register()
    {
        return view('register');
    }

    public function form_register_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:registration',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,20}$/',
            'password_confirmation' => 'required',
            'gender' => 'required|in:male,female',
            'mobile_number' => 'required|regex:/^[0-9]{10}$/',
            'hobbies' => 'required|array|min:1',
            'profile_picture' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'fullname.required' => 'The full name field is required.',
            'fullname.string' => 'The full name must be a string.',
            'fullname.max' => 'The full name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must contain one small letter on capital letter, number and a special symbol.',
            'password_confirmation.required' => 'Confirm Passowrd cannot be empty',
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The selected gender is invalid.',
            'mobile_number.required' => 'The mobile number field is required.',
            'mobile_number.string' => 'The mobile number must be a string.',
            'mobile_number.max' => 'The mobile number may not be greater than 20 characters.',
            'hobbies.required' => 'Please select a hobby.',
            'profile_picture.required' => 'The profile picture field is required.',
            'mobile_number.regex' => 'Number must not be greater than 10 digits.',
            'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
            'profile_picture.max' => 'The profile picture may not be greater than 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect('register')->withErrors($validator)->withInput();
        }


        $reg = new Registrations();
        $reg = new Registrations();
        $reg->fullname = $request->fullname;
        $reg->email = $request->email;
        $reg->password = $request->password;
        $reg->gender = $request->gender;
        $reg->mobile = $request->mobile_number;
        $hobbies = $request->input('hobbies');
        $reg->hobbies = implode(',', $hobbies);
        $profile_pic = uniqid() . $request->profile_picture->getClientOriginalName();
        $reg->profile_picture = $profile_pic;

        if ($reg->save()) {
            $request->profile_picture->move("uploads/", $profile_pic);
            $data = array('name' => $request->fullname, 'email' => $request->email);
            Mail::Send(['text' => 'create_account_mail'], ["data1" => $data], function ($message) use ($data) {
                $message->to($data['email'], $data['name']);
                $message->from("jankikansagra12@gmail.com", "Janki Kansagra");
            });
            session()->flash('success', 'Registration successfull');
            return redirect('login');
        } else {
            session()->flash('error', 'Error in Registration');
            return redirect('register');
        }
    }
    public function forgot_password()
    {
        return view('forget_pwd');
    }
    public function register_data($data)
    {
        return view('register_data', compact('data'));
    }
    public function login_action(Request $req)
    {
        $validated = Validator::make(
            $req->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email address is a required field',
                'email.email' => 'Invalid email address',
                'password.required' => 'Password is a required field'
            ]
        );
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }
        $reg = new Registrations();

        $em = $req->email;
        $pwd = $req->password;

        $result = Registrations::where('email', $em)->where('password', $pwd)->first();
        if (!empty($result)) {
            if ($result->status == 'Active') {
                if ($result->role == 'User') {
                    session()->flash('success', "Login successful");
                    session()->put('user_uname', $em);
                    return redirect('user_dashboard');
                } else {
                    session()->flash('success', "Login successful");
                    session()->put('admin_uname', $em);
                    return redirect('admin_dashboard');
                }
            } else {
                session()->flash('error', 'Your Account is not activated. Kindly Activate yor account by verifying your email address using the verification link sent to your email account');
                return redirect('login');
            }
        } else {
            session()->flash('error', "Incorrect email address or password");
            return redirect('login');
        }
    }
    public function contact_action(Request $req)
    {
        $validated = Validator::make($req->all(), [
            'name' => 'required|regex:/^[A-Za-z ]{2,50}$/',
            'email' => 'required|email',
            'message' => 'required',
            'mobile' => 'required|regex:/^[0-9]{10}$/',

        ], [
            'name.required' => 'Name is a required field',
            'name.regex' => 'Name must contain only Letters and length mus be greater than 2 characters',
            'email.required' => 'Email is a required field',
            'email.email' => 'Invalid email address',
            'message.required' => 'Message is a required field',
            'mobile.required' => 'Mobile number is a required field',
            'mobile.regex' => 'Mobile Number must contain only 10 digits'
        ]);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }
        $contact = new Contact();

        $contact->name = $req->name;
        $contact->email = $req->email;
        $contact->msg = $req->message;
        $contact->mobile = $req->mobile;

        if ($contact->save()) {
            session()->flash('success', 'Thank you for your feedback.');
        } else {
            session()->flash('error', 'Error in saving feedback.Please try again later.');
        }
        return redirect('contact');
    }

    public function display_data()
    {
        $reg = new Registrations;
        $result = $reg->select()->get();
        return view('display_data', compact('result'));
    }
    
    public function verify_account($em)
    {
        $result = Registrations::whereEmail($em)->first();
        if (empty($result)) {
            session()->flash('error', 'Your account is not registered. Kindly register here.');
            return redirect('register');
        } else {
            if ($result->status == 'Active') {
                session()->flash('success', 'Your account is already activated kindly login');
            } else {
                $update = Registrations::where('email', $em)->update(array('status' => 'Active'));
                if ($update) {
                    session()->flash('success', 'Your account is activated successfully.');
                } else {
                    session()->flash('error', 'Account activation failed please try after sometime.');
                }
            }
            return redirect('login');
        }
    }
}
