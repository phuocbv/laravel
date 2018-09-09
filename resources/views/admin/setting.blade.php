@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Cmanager / </span>Setting</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" onclick="submitForm()" href="javascript:;" data-placement="bottom" data-original-title="Save"><i class="fa fa-save"></i></a>
            <a class="btn btn-dark" title="" data-toggle="tooltip" href="{{ route('admin.member') }}" data-placement="bottom" data-original-title="Back"><i class="fa fa-reply"></i></a>
        </div>
    </div>

    <div>
        @include('errors.alert')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>Setting</span>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal"
                          id="create_form"
                          method="post"
                          action="{{ route('admin.postsetting') }}"
                          autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group {{ $errors->has('sheetid') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Spreadsheet Id</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="sheetid" value="{{ old('sheetid', $configs['sheetid']) }}">
                                @if( $errors->has('sheetid'))
                                    <p class="help-block">{{ $errors->first('sheetid') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('sheetname') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Sheet Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="sheetname" value="{{ old('sheetname', $configs['sheetname']) }}">
                                @if( $errors->has('sheetid'))
                                    <p class="help-block">{{ $errors->first('sheetname') }}</p>
                                @endif
                            </div>
                        </div>


                    </form>

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

        function submitForm(){
            $("#create_form").submit();
        }
    </script>
@endsection

