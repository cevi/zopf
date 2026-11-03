@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <x-page-title :title="$title" :help="$help"/>

        <div class="grid gap-6 lg:grid-cols-2 mt-4">
            <section class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <h4 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Datenbank</h4>
                @if (count($pendingMigrations) > 0)
                    <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ count($pendingMigrations) }} offene {{ count($pendingMigrations) === 1 ? 'Migration' : 'Migrationen' }}:
                    </p>
                    <ul class="mb-4 text-sm font-mono list-disc list-inside text-gray-900 dark:text-white">
                        @foreach ($pendingMigrations as $migration)
                            <li>{{ $migration }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">Keine offenen Migrationen.</p>
                @endif
                <form method="POST" action="{{ route('admin.maintenance.migrate') }}">
                    @csrf
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Migrationen ausführen
                    </button>
                </form>
            </section>

            <section class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <h4 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Caches</h4>
                <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                    Nach einem Deployment die Caches neu aufbauen (Konfiguration und Views).
                    Zeigt die Seite nach einer Änderung an der Konfiguration noch alte Werte, die Caches leeren.
                </p>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.maintenance.cache') }}">
                        @csrf
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Caches neu aufbauen
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.maintenance.clear') }}">
                        @csrf
                        <button type="submit"
                            class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-700">
                            Caches leeren
                        </button>
                    </form>
                </div>
            </section>
        </div>

        @if (session()->has('maintenance_output'))
            <section class="mt-6">
                <h4 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Ausgabe</h4>
                <pre class="p-4 overflow-x-auto text-sm bg-gray-100 rounded-lg text-gray-900 dark:bg-gray-900 dark:text-white">{{ session('maintenance_output') }}</pre>
            </section>
        @endif
    </div>
@endsection
