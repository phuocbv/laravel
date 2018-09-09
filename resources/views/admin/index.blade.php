@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Orders / </span>Manager</h1>

    </div>

    <div>
        @include('errors.alert')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>Under Construction</span>
                </div>
                <div class="panel-body">
                    Comming Soon
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script>
        $( document ).ready(function() {
            $('#top-righ-icon a').tooltip();
            $('#action_colum a').tooltip();
        });
    </script>
@endsection

