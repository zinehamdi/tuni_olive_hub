@extends('layouts.guest')
@section('content')
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="role" value="normal">
        <div>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" required autofocus>
        </div>
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div>
            <label for="profile_picture">Profile Picture</label>
            <input id="profile_picture" type="file" name="profile_picture">
        </div>
        <button type="submit">Register as Normal User</button>
    </form>
@endsection
