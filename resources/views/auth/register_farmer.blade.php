@extends('layouts.guest')
@section('content')
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="role" value="farmer">
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
            <label for="olive_type">Olive Type</label>
            <input id="olive_type" type="text" name="olive_type" required>
        </div>
        <div>
            <label for="farm_location">Farm Location</label>
            <input id="farm_location" type="text" name="farm_location" required>
        </div>
        <div>
            <label for="tree_number">Number of Trees</label>
            <input id="tree_number" type="number" name="tree_number" required>
        </div>
        <div>
            <label for="profile_picture">Profile Picture</label>
            <input id="profile_picture" type="file" name="profile_picture">
        </div>
        <button type="submit">Register as Farmer</button>
    </form>
@endsection
