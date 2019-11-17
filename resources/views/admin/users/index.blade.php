@extends('layouts.admin')

@section('content')

@if (Session::has('deleted_user'))

<p class="bg-danger">{{session('deleted_user')}}</p>
    
@endif
<h1>Users</h1>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Rolle</th>
            @if ((Auth::user()->isAdmin()))
                <th scope="col">Gruppe</th>
            @endif
            <th scope="col">Status</th>
            <th scope="col">Created</th>
            <th scope="col">Updated</th>
        </tr>
    </thead>
    <tbody>
        @if ($users)
            @foreach ($users as $user)
            <tr>
                <td><a href="{{route('users.edit', $user->id)}}">{{$user->username}}</a></td>
                <td>{{$user->role['name']}}</td>
                @if ((Auth::user()->isAdmin()))
                    <td>{{$user->group['name']}}</td>
                @endif
                <td>{{$user->is_active == 1 ? "Aktiv" : "Nicht Aktiv"}}</td>
                <td>{{$user->created_at->diffForHumans()}}</td>
                <td>{{$user->updated_at->diffForHumans()}}</td>
            </tr>    
            @endforeach

        @endif

    </tbody>
</table>
@endsection