<button type="button" data-dropdown-placement="right-start"
    class="flex items-center justify-between w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
    id="actions-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-actions">
    <span class="block text-sm dark:text-gray-400">
        @if(Auth::user()->action && !Auth::user()->action['global'] )
        {{Auth::user()->action['name']}} {{Auth::user()->action['year']}}
        @else
        Meine Aktionen
        @endif <span class="caret"></span>
    </span>
    <svg aria-hidden="true" class="w-2.5 h-2.5 ms-3 rtl:rotate-180" fill="currentColor" viewBox="0 0 6 10" xmlns="http://www.w3.org/2000/svg">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
    </svg>
</button>

<div class="hidden z-50 my-4 w-60 text-base list-none navbar-background divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
    id="dropdown-actions">
    <ul aria-labelledby="dropdown-actions" class="py-1 text-gray-700 dark:text-gray-300">
        @if(!Auth::user()->demo )
        <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
            href="{{ route('actions.create') }}">
            Aktion erfassen
        </a>
        @endif

        @if(count(Auth::user()->group->actions) > 0)
        <hr>
        @foreach (Auth::user()->group->actions->sortByDesc('year') as $action)
            @if(!$action['global'] && (!($action['action_status_id']>config('status.action_aktiv') && $action['user_id'] <> Auth::user()->id) || ($action['id'] == Auth::user()->action['id'])))
            <li class="container">
                <div class="row">

                    <div class="col-10">
                        <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                            href="{{route('admin.actions.updateAction',$action['id'])  }}"
                            onclick="event.preventDefault();
                                                document.getElementById('actions-update-form-{{$action['id']}}').submit();">
                            {{$action['name']}} {{$action['year']}}
                        </a>
                    </div>
                    <div class="col-2">
                        @if(!Auth::user()->demo && $action->user && $action->user['id']===Auth::user()->id)
                        <a class="block py-2 text-center text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                            href="{{route('actions.edit',$action)  }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <form id="actions-update-form-{{$action['id']}}"
                    action="{{route('admin.actions.updateAction',$action['id'])  }}" method="POST" style="display: none;">
                    {{ method_field('PUT') }}
                    @csrf
                </form>
            </li>
            @endif
        @endforeach
        @endif
    </ul>
</div>