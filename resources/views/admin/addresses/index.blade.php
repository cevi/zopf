@extends('layouts.admin')

@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item active">Adressen</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Adressen</h1>
            </header>
            <div class="row">
    
                <div class="col-sm-9">
                    @if ($addresses)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Vorname</th>
                                    <th scope="col">Strasse</th>
                                    <th scope="col">PLZ</th>
                                    <th scope="col">Ort</th>
                                    <th scope="col">Gruppe</th>
                                    <th scope="col">Created Date</th>
                                    <th scope="col">Updated Date</th>
                                </tr>
                            </thead>
                        @foreach ($addresses as $address)
                            <tbody>
                                <tr>
                                    <td><a href="{{route('addresses.edit',$address->id)}}">{{$address->name}}</a></td>
                                    <td>{{$address->firstname}}</a></td>
                                    <td>{{$address->street}}</a></td>
                                    <td>{{$address->city['plz']}}</a></td>
                                    <td>{{$address->city['name']}}</a></td>
                                    <td>{{$address->group['name']}}</a></td>
                                    <td>{{$address->created_at ? $address->created_at->diffForHumans() : 'no date'}}</td>
                                    <td>{{$address->updated_at ? $address->updated_at->diffForHumans() : 'no date'}}</td>
                                </tr>
                            </tbody>
                        @endforeach
                        </table>
                    
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection