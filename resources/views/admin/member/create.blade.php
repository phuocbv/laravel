@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Users / </span>Add Member</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" onclick="submitForm()" href="javascript:;" data-placement="bottom" data-original-title="Save"><i class="fa fa-save"></i></a>
            <a class="btn btn-dark" title="" data-toggle="tooltip" href="{{ route('admin.member') }}" data-placement="bottom" data-original-title="Back"><i class="fa fa-reply"></i></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>Create Member</span>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal"
                          id="create_form"
                          method="post"
                          action="{{ route('admin.member.create') }}"
                          autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                @if( $errors->has('name'))
                                    <p class="help-block">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="username" value="{{ old('username') }}">
                                @if( $errors->has('username'))
                                    <p class="help-block">{{ $errors->first('username') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                @if( $errors->has('email'))
                                    <p class="help-block">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label for="password" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password" placeholder="" value="">
                                @if( $errors->has('password'))
                                    <p class="help-block">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            <label for="password_confirmation" class="col-sm-2 control-label">Password Confirmation</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="" value="">
                                @if( $errors->has('password_confirmation'))
                                    <p class="help-block">{{ $errors->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Notes</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="notes">{{ old('notes') }}</textarea>
                                @if( $errors->has('email'))
                                    <p class="help-block">{{ $errors->first('notes') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Group</label>
                            <div class="col-sm-10">
                                <div class="radio">
                                    <label>
                                        <input type="radio" id="group_id" name="group_id" class="px radio" value="3" checked="checked"> <span class="lbl">Member </span>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" id="group_id" name="group_id" class="px radio" value="2"> <span class="lbl">Mod </span>
                                    </label>
                                </div>
                                <div id="div_mod" style="display: none;">
                                    @foreach($emails as $email)
                                        <div style="margin: 0;" class="checkbox">
                                            <label>
                                                <input type="checkbox" class="px" name="role[]" value="{{ $email->id }}">
                                                <span class="lbl">{{ $email->name }} ({{ $email->username }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
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
            $("input.radio").click(function(){
                value_ = $(this).val();
                if(value_ == '2'){
                    $("#div_mod").show();
                }else{
                    $("#div_mod").hide();
                }
            });
        });

        function submitForm(){
            $("#create_form").submit();
        }
    </script>
@endsection

