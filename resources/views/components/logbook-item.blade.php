<li class="mb-10 ml-6">
    <span
        class="flex absolute -left-3 justify-center items-center w-6 h-6 bg-blue-200 rounded-full ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
        <svg aria-hidden="true"
             class="w-3 h-3 text-blue-600 dark:text-blue-400"
             fill="currentColor"
             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path
                fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd">
            </path>
        </svg>
    </span>
    <div
        class="justify-between items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600">
        <time
            class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{$time}}</time>
        <div class="text-sm font-normal text-gray-500 dark:text-gray-300">
            <strong>
                <a href="{{route('logbooks.edit', $id)}}">
                    {{$user}}:
                </a>
            </strong>
            {{$content}}
        </div>
    </div>
</li>
