<li class="nav-item dropdown">
    <a id="navbarGroupDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false" v-pre>
        @if(Auth::user()->group && !Auth::user()->group['global'] )
            {{Auth::user()->group['name']}}
        @else
            Meine Gruppen
        @endif <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarGroupDropdown">
        @if(!Auth::user()->demo )
            <a class="dropdown-item" href="{{ route('home.groups.create') }}">
                Gruppe erfassen
            </a>
        @endif
        @if(count(Auth::user()->groups) >1 )
            <hr>
            @foreach (Auth::user()->groups as $group)
                @if(!$group['global'])
                    <div class="row">

                        <div class="col-sm-9">
                            <a class="dropdown-item" href="{{route('home.groups.updateGroup',$group['id'])  }}"
                               onclick="event.preventDefault();
                                                       document.getElementById('groups-update-form-{{$group['id']}}').submit();">
                                {{$group['name']}}
                            </a>
                        </div>
                        <div class="col-sm-3">
                            @if(!Auth::user()->demo && $group->user && $group->user['id']===Auth::user()->id)
                                <a class="dropdown-item" href="{{route('home.groups.edit',$group)  }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <form id="groups-update-form-{{$group['id']}}"
                          action="{{route('home.groups.updateGroup',$group['id'])  }}" method="POST"
                          style="display: none;">
                        {{ method_field('PUT') }}
                        @csrf
                    </form>
                @endif
            @endforeach
        @endif
    </div>
</li>
