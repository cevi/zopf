@extends('layouts.admin')


@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                <a href="{{route('routes.create')}}" class="btn btn-primary btn-sm">
                    <span>Erfassen</span>
                </a>
            </header>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table responsive" width="100%" id="datatable">
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
@section('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
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
@endsection
