@extends('layout.users_master')
@section('dynamic_section')
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class=col-lg-3></div>
            <div class=col-lg-6>
                <h2>Change Password</h2>
                <form action="{{ URL::to('/') }}/update_user_password" method="post" id="form1">
                    @csrf
                    <div class="form-group">
                        <label for="password1">Old Password:</label>
                        <input type="password" class="form-control" id="password1" placeholder="Enter Password" name="old_pwd"
                            value="{{ old('old_pwd') }}">
                        @error('old_pwd')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="New Password" value="{{ old('password') }}">
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password:</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            placeholder="Confirm New Password" value="{{ old('password_confirmation') }}">
                    </div>

                    <input type="submit" class="btn" value="Submit" name="lgn_btn" />

                </form>
            </div>
        </div>
    </div>
@endsection
