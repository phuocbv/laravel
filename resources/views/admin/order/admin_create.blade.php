@extends('admin.master')


@section('content')

    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Orders / </span>Create Order</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" onclick="submitForm()" href="javascript:;" data-placement="bottom" data-original-title="Save"><i class="fa fa-save"></i></a>
            <a class="btn btn-dark" title="" data-toggle="tooltip" href="{{ route('admin.order.index') }}" data-placement="bottom" data-original-title="Back"><i class="fa fa-reply"></i></a>
        </div>
    </div>

    @include('errors.alert_validate')

    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">

                <form id="create_form"
                      method="POST"
                      action="{{ route('admin.order.store') }}"
                      autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label class="sr-only" for="buyer_name">Buyer Name</label>
                            <input placeholder="Buyer Name" id="grid-buyer_name-11" name="buyer_name" class="form-control" type="text" value="{{ old('buyer_name') }}">
                        </div>
                        <div class="form-group col-sm-3">
                            <label class="sr-only" for="buyer_email">Buyer Email</label>
                            <input placeholder="Buyer Email" id="buyer_email" name="buyer_email" class="form-control" type="text" value="{{ old('buyer_email') }}">
                        </div>
                        <div class="form-group col-sm-3">
                            <select class="form-control" name="user_id">
                                @foreach($members as $member)
                                    <option value="{{$member->id}}">{{ $member->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <select class="form-control" name="mailacc_id">
                                @foreach($emails as $email)
                                    <option value="{{$email->id}}">{{ $email->name }}({{$email->username}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-8">
                            <input placeholder="Item Name" id="item_name" name="item_name" class="form-control" type="text" value="{{ old('item_name') }}">
                        </div>
                        <div class="form-group col-sm-2">
                            <input placeholder="Item Price" id="item_price" name="item_price" class="form-control" type="text" value="{{old('item_price')}}">
                        </div>
                        <div class="form-group col-sm-2">
                            <input placeholder="Item Quantity" id="item_qty" name="item_qty" class="form-control" type="text" value="{{old('item_qty')}}">
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <textarea placeholder="Shipping Address" id="shipping_address" name="shipping_address" class="form-control" rows="5">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>
                </form>


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

