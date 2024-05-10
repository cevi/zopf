<button type="button"
    class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
    id="groups-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown-groups">
    <span class="flex-1 ml-3 text-left whitespace-nowrap">
        @if(Auth::user()->group && !Auth::user()->group['global'] )
        {{Auth::user()->group['name']}}
        @else
        Meine Gruppen
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

                <div class="col-sm-9">
                    <a class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white"
                        href="{{route('home.groups.updateGroup',$group['id'])  }}"
                        onclick="event.preventDefault();
                                                       document.getElementById('groups-update-form-{{$group['id']}}').submit();">
                        {{$group['name']}}
                    </a>
                </div>
                <div class="col-sm-3">
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