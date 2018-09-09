@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa  fa-envelope"></i>Email / </span>Edit Email Account</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" onclick="submitForm()" href="javascript:;" data-placement="bottom" data-original-title="Save"><i class="fa fa-save"></i></a>
            <a class="btn btn-dark" title="" data-toggle="tooltip" href="{{ route('admin.member') }}" data-placement="bottom" data-original-title="Back"><i class="fa fa-reply"></i></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>Edit Email Account</span>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal"
                          id="create_form"
                          method="post"
                          action="{{ route('admin.email.update') }}"
                          autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="email_id" value="{{ $email->id }}" />
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" value="{{ old('name', $email->name) }}">
                                @if( $errors->has('name'))
                                    <p class="help-block">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                        </div>



                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="email" value="{{ old('email', $email->email) }}">
                                @if( $errors->has('email'))
                                    <p class="help-block">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Shop Type</label>
                            <div class="col-sm-10">
                                <div class="radio">
                                    <label>
                                        <input type="radio" id="shop_id" name="shop_id" class="px radio" value="1" checked="checked"> <span class="lbl">Shopify</span>
                                    </label>
                                </div>
                                <div id="div_shopify">
                                    <div class="form-group {{ $errors->has('shopify_key') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">Shopify Key</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="shopify_key" value="{{ old('shopify_key', $email->shopify_key) }}">
                                            @if( $errors->has('shopify_key'))
                                                <p class="help-block">{{ $errors->first('shopify_key') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('shopify_pass') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">Shopify Pass</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="shopify_pass" value="{{ old('shopify_pass', $email->shopify_pass) }}">
                                            @if( $errors->has('shopify_pass'))
                                                <p class="help-block">{{ $errors->first('shopify_pass') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('shopify_hostname') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">Shopify Hostname</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="shopify_hostname" placeholder="ex:azstoreus.myshopify.com" value="{{ old('shopify_hostname', $email->shopify_hostname) }}">
                                            @if( $errors->has('shopify_hostname'))
                                                <p class="help-block">{{ $errors->first('shopify_hostname') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('shopify_shared_secret') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">Shopify Shared Secret</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="shopify_shared_secret" value="{{ old('shopify_shared_secret', $email->shopify_shared_secret) }}">
                                            @if( $errors->has('shopify_shared_secret'))
                                                <p class="help-block">{{ $errors->first('shopify_shared_secret') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" id="shop_id" name="shop_id" class="px radio" value="2"> <span class="lbl">Ebay</span>
                                    </label>
                                </div>
                                <div id="div_ebay" style="display: none;">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">
                                            <button id="get-grant-token-btn" class="btn btn-primary" data-loading-text="Please wait..." type="button">Get Link First</button>
                                        </label>
                                        <div class="col-sm-10">
                                            <div id="grand_result" style="padding-top: 5px;"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">
                                            <button id="get-user-token-btn" class="btn btn-primary" data-loading-text="Please wait..." type="button">Get User Token</button>
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="grand_code" type="text" class="form-control" name="grand_code">
                                            <div id="grand_code_error"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('ebay_access_token') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">ebay access token</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="ebay_access_token" name="ebay_access_token">{{ old('ebay_access_token', $email->ebay_access_token) }}</textarea>
                                            @if( $errors->has('ebay_access_token'))
                                                <p class="help-block">{{ $errors->first('ebay_access_token') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('ebay_refresh_token') ? 'has-error' : '' }}">
                                        <label class="col-sm-2 control-label">ebay refresh token</label>
                                        <div class="col-sm-10">
                                            <textarea id="ebay_refresh_token" class="form-control" name="ebay_refresh_token">{{ old('ebay_refresh_token', $email->ebay_refresh_token) }}</textarea>
                                            @if( $errors->has('ebay_refresh_token'))
                                                <p class="help-block">{{ $errors->first('ebay_refresh_token') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label">Notes</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="notes">{{ old('notes', $email->notes) }}</textarea>
                                @if( $errors->has('notes'))
                                    <p class="help-block">{{ $errors->first('notes') }}</p>
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
            $("input.radio").click(function(){
                value_ = $(this).val();
                if(value_ == '2'){
                    $("#div_ebay").show();
                    $("#div_shopify").hide()
                }else{
                    $("#div_ebay").hide();
                    $("#div_shopify").show()
                }
            });
        });

        function submitForm(){
            $("#create_form").submit();
        }

        $('#get-grant-token-btn').click(function () {
            var btn = $(this);
            btn.button('loading');
            var url = "{{ route('admin.email.grantcode') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data){
                    if(data['status'] == 'success'){
                        $("#grand_result").html('<div class="alert alert-info">'+decodeURI(data['url'])+'</div>');
                    } else {
                        $("#grand_result").html('<div class="alert alert-info">Error!</div>');
                    }
                    btn.button('reset');
                }

            });

        });

        $('#get-user-token-btn').click(function () {
            var btn = $(this);
            btn.button('loading');
            var url = "{{ route('admin.email.usertoken') }}";
            var grand_code = $("#grand_code").val();
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                    grand_code: grand_code
                },
                success: function(data){
                    if(data['status'] == 'success'){
                        $("#ebay_access_token").html(data['access_token']);
                        $("#ebay_refresh_token").html(data['refresh_token']);
                    } else {
                        $("#grand_code_error").html('<div class="alert alert-info">'+data['status']+': '+data['msg']+'</div>');
                    }
                    btn.button('reset');
                }

            });

        });

    </script>
@endsection

