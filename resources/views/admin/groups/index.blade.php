@extends('layouts.admin')

@section('content')

    <h1>Gruppen</h1>
    <div class="col-sm-3">
        {!! Form::open(['method' => 'POST', 'action'=>'AdminGroupsController@store']) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name:') !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('user_id', 'Gruppenleiter:') !!}
                {!! Form::select('user_id', [''=>'Bitte wählen'] + $users, null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Gruppe erstellen', ['class' => 'btn btn-primary'])!!}
            </div>
         {!! Form::close()!!}
    </div>    
    <div class="col-sm-9">
        @if ($groups)
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Gruppenleiter</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Updated Date</th>
                    </tr>
                </thead>
            @foreach ($groups as $group)
                <tbody>
                    <tr>
                        <td><a href="{{route('groups.edit',$group->id)}}">{{$group->name}}</a></td>
                        <td>{{$group->user['username']}}</a></td>
                        <td>{{$group->created_at ? $group->created_at->diffForHumans() : 'no date'}}</td>
                        <td>{{$group->updated_at ? $group->updated_at->diffForHumans() : 'no date'}}</td>
                    </tr>
                </tbody>
            @endforeach
            </table>
        
        @endif

    </div>
@endsection