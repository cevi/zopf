@extends('layouts.admin')

@section('content')

    <h1>Aktionen</h1> 
    <div class="col-sm-9">
        @if ($actions)
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Jahr</th>
                        <th scope="col">Name</th>
                        <th scope="col">Gruppe</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Updated Date</th>
                    </tr>
                </thead>
            @foreach ($actions as $action)
                <tbody>
                    <tr>
                        <td>{{$action->year}}</a></td>
                        <td><a href="{{route('actions.edit',$action->id)}}">{{$action->name}}</a></td>
                        <td>{{$action->group['name']}}</a></td>
                        <td>{{$action->action_status['name']}}</a></td>
                        <td>{{$action->created_at ? $action->created_at->diffForHumans() : 'no date'}}</td>
                        <td>{{$action->updated_at ? $action->updated_at->diffForHumans() : 'no date'}}</td>
                    </tr>
                </tbody>
            @endforeach
            </table>
        
        @endif

    </div>
@endsection