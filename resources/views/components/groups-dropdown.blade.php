<button type="button" data-dropdown-placement="right-start"
    class="flex items-center justify-between w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
    id="groups-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-groups">
    <span class="block text-sm dark:text-gray-400">
        @if(Auth::user()->group && !Auth::user()->group['global'] )
        {{Auth::user()->group['name']}}
        @else
        Meine Gruppen
        @endif <span class="caret"></span>
    </span>
    <svg aria-hidden="true" class="w-2.5 h-2.5 ms-3 rtl:rotate-180" fill="currentColor" viewBox="0 0 6 10" xmlns="http://www.w3.org/2000/svg">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
    </svg>
</button>

<div class="hidden z-50 my-4 w-56 text-base list-none navbar-background divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 rounded-xl"
    id="dropdown-groups">
    <ul aria-labelledby="dropdown-groups" class="py-1 text-gray-700 dark:text-gray-300">
        @if(!Auth::user()->demo )
        <li>
            <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                href="{{ route('home.groups.create') }}">
                Gruppe erfassen
            </a>
        </li>
        @endif
        @if(count(Auth::user()->groups) >1 )
        <hr>
        @foreach (Auth::user()->groups as $group)
        @if(!$group['global'])
        <li>
            <div class="row">

                <div class="col-9">
                    <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                        href="{{route('home.groups.updateGroup',$group['id'])  }}"
                        onclick="event.preventDefault();
                                                       document.getElementById('groups-update-form-{{$group['id']}}').submit();">
                        {{$group['name']}}
                    </a>
                </div>
                <div class="col-3">
                    @if(!Auth::user()->demo && $group->user && $group->user['id']===Auth::user()->id)
                    <a class="block py-2  text-center text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                        href="{{route('home.groups.edit',$group)  }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @endif
                </div>
            </div>

            <form id="groups-update-form-{{$group['id']}}" action="{{route('home.groups.updateGroup',$group['id'])  }}"
                method="POST" style="display: none;">
                {{ method_field('PUT') }}
                @csrf
            </form>
        </li>
        @endif
        @endforeach
        @endif
    </ul>
</div>
</li>