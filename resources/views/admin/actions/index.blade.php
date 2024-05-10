@extends('layouts.admin')

@section('content')

<x-page-title :title="$title" :help="$help" />
<section>
    <div class="container-fluid">
        <!-- Page Header-->
        <a href="{{route('actions.create')}}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Erfassen</a>
        <div class="row">

            <div class="col-sm-9">
                <table class="table table-striped table-bordered" style="width:100%" id="datatable">
                    <thead>
                        <tr>
                            <th>Jahr</th>
                            <th>Name</th>
                            <th>Gruppe</th>
                            <th>Status</th>
                            <th>Aktionen</th>
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
                    ajax: "{!! route('actions.CreateDataTables') !!}",
                    columns: [
                        {data: 'year', name: 'year'},
                        {data: 'name', name: 'name'},
                        {data: 'group', name: 'group'},
                        {data: 'status', name: 'status'},
                        {data: 'Actions', name: 'Actions', orderable: false, serachable: false, sClass: 'text-center'},
                    ],
                    order: [[0, 'desc']]
                });
            });
        }, false);
</script>

@endpush