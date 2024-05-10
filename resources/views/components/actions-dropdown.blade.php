<button type="button"
    class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
    id="actions-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-actions">
    <span class="flex-1 ml-3 text-left whitespace-nowrap">
        @if(Auth::user()->action && !Auth::user()->action['global'] )
        {{Auth::user()->action['name']}} {{Auth::user()->action['year']}}
        @else
        Meine Aktionen
        @endif <span class="caret"></span>
    </span>
    <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd">
        </path>
    </svg>
</button>

<div class="hidden z-50 my-4 w-56 text-base list-none navbar-background divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
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
        @foreach (Auth::user()->group->actions as $action)
        @if(!$action['global'])
        <li>
            <div class="row">

                <div class="col-sm-9">
                    <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                        href="{{route('admin.actions.updateAction',$action['id'])  }}"
                        onclick="event.preventDefault();
                                               document.getElementById('actions-update-form-{{$action['id']}}').submit();">
                        {{$action['name']}} {{$action['year']}}
                    </a>
                </div>
                <div class="col-sm-3">
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