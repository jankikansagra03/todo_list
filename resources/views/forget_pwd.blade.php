@extends('layout.master')
@section('dynamic_section')
    <!-- Forgot Password Page -->
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Forgot Password</h2>
                <p>Enter your registered email address below and we'll send you instructions on how to reset your password.
                </p>
                <form action="/forgot-password" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <input type="submit" class="btn" value="Reset Password" />
                </form>
            </div>
        </div>
    </div>
@endsection
