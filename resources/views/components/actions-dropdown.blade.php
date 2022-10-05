<li class="nav-item dropdown">
    <a id="navbarActionDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        @if(Auth::user()->action && !Auth::user()->action['global'] )
            {{Auth::user()->action['name']}}
        @else
            Meine Aktionen
        @endif <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarActionDropdown">
        @if(!Auth::user()->demo )
            <a class="dropdown-item" href="{{ route('actions.create') }}">
                Aktion erfassen
            </a>
            <hr>
        @endif
        @foreach (Auth::user()->group->actions as $action)
            @if(!$action['global'])
                <div class="row" >

                    <div class="col-sm-9">
                        <a class="dropdown-item" href="{{route('admin.actions.updateAction',$action['id'])  }}"
                           onclick="event.preventDefault();
                                           document.getElementById('actions-update-form-{{$action['id']}}').submit();">
                            {{$action['name']}}
                        </a>
                    </div>
                    <div class="col-sm-3">
                        @if($action->user && $action->user['id']===Auth::user()->id)
                            <a class="dropdown-item" href="{{route('actions.edit',$action)  }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <form id="actions-update-form-{{$action['id']}}" action="{{route('admin.actions.updateAction',$action['id'])  }}" method="POST" style="display: none;">
                    {{ method_field('PUT') }}
                    @csrf
                </form>
            @endif
        @endforeach
    </div>
</li>
