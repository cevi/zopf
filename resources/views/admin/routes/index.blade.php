@extends('layouts.admin')


@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <header>
            <a href="{{route('routes.create')}}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <span>Erfassen</span>
            </a>
        </header>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered responsive" width="100%" id="datatable">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Verantwortlich</th>
                            <th scope="col">Routen Art</th>
                            <th scope="col">Anz. Zöpfe</th>
                            <th scope="col">Anz. Bestellungen</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aktionen</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

</section>
@endsection
@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    buttons: [],
                    language: {
                        "url": "/lang/Datatables.json"
                    },
                    ajax: "{!! route('routes.CreateDataTables') !!}",
                    order: [[5, "asc"], [0, "asc"]],
                    columns: [
                        {data: 'name', name: 'name', "width": "10%"},
                        {data: 'user', name: 'user', "width": "10%"},
                        {data: 'routetype', name: 'routetype', "width": "5%"},
                        {data: 'zopf_count', name: 'zopf_count', "width": "5%"},
                        {data: 'order_count', name: 'order_count', "width": "5%"},
                        {
                            data: {
                                _: 'status.display',
                                sort: 'status.sort'
                            },
                            name: 'status',
                            "width": "5%"
                        },
                        {
                            data: 'Actions',
                            name: 'Actions',
                            orderable: false,
                            serachable: false,
                            sClass: 'text-center',
                            "width": "10%"
                        },

                    ]
                });
            });
        }, false);


</script>
@endpush