@extends('admin.master')

@section('css')
    <!--
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

    <script>

        $.fn.editableform.buttons =
            '<button type="submit" class="btn btn-primary btn-sm editable-submit">'+
            '<i class="fa fa-check"></i>'+
            '</button>'+
            '<button type="button" class="btn btn-default btn-sm editable-cancel">'+
            '<i class="fa fa-close"></i>'+
            '</button>';
    </script>
@endsection

@section('content')


    <div>
        @include('errors.alert')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-heading">
                        <a href="#collapseOne-success" data-parent="#accordion-success-example" data-toggle="collapse" class="accordion-toggle collapsed">
                            Filter
                        </a>
                    </div> <!-- / .panel-heading -->
                    <div class="panel-collapse collapse" id="collapseOne-success" style="height: 0px;">
                        <div class="panel-body">
                            <form class="form-inline" action="{{ route('admin.order.index') }}">
                                <div class="form-group col-sm-2">
                                    <input type="text" placeholder="Search by Buyer" name="buyer_name" class="form-control" style="width: 100%">
                                </div>
                                <div class="form-group col-sm-2">
                                    <input type="text" placeholder="Search by Address" name="address" class="form-control col-sm-2" style="width: 100%">
                                </div>
                                <div class="form-group col-sm-2">
                                    <select class="input-sm" name="status" style="width: 100%">
                                        <option value="0">Choose status</option>
                                        {!! echo_status_option() !!}
                                    </select>
                                </div>
                                <div class="form-group col-sm-1">
                                    <select class="input-sm" name="payment" style="width: 100%">
                                        <option value="3">Pay Option</option>
                                        {!! echo_pay_option() !!}
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <div class="input-daterange input-group" id="datepicker-range">
                                        <input class="form-control" name="start_date" type="text">
                                        <span class="input-group-addon">to</span>
                                        <input class="form-control" name="end_date" type="text">
                                    </div>

                                    <script>
                                        $(function() {
                                            $('#datepicker-range').datepicker({
                                                format: 'dd/m/yyyy',
                                            });
                                        });
                                    </script>


                                </div>



                                <div class="form-group col-sm-1">
                                    <input type="text" placeholder="ID" name="ids" class="form-control" style="width: 100%">
                                </div>
                                <div class="form-group col-sm-1">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>

                            </form>

                        </div> <!-- / .panel-body -->
                    </div> <!-- / .collapse -->
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <form action="" id="adminForm" method="POST">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>Mananger Orders</span>
                    <span class="label label-warning">Total: ${{ number_format($total, 2) }}</span>
                    <span class="label label-primary">{{$orders->total()}} orders</span>

                    <span class="label label-danger">Waiting: {{$total_status[1]}}</span>
                    <span class="label label-warning">Ordered: {{$total_status[2]}}</span>
                    <span class="label label-info">Soon: {{$total_status[3]}}</span>
                    <span class="label label-success">Shipped: {{$total_status[4]}}</span>
                    <span class="label label-primary">Completed: {{$total_status[5]}}</span>
                    <span class="label label-default">Canceled: {{$total_status[6]}}</span>
                    <span class="label label-danger label-outline">Returned: {{$total_status[7]}}</span>
                </div>
                <div class="panel-body">
                        <input type="hidden" name="_token" value="{{ csrf_token()  }}" />
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <script>
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                        </script>
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-hover admin-table">
                            <thead>
                            <tr>
                                <th style="width:1ex"><input type="checkbox" name="sa" id="sa" onclick="checkbox('sa', 'ar_id[]', 'adminForm')"></th>
                                <th>Buyer</th>
                                <th>Address</th>
                                <th>Items
                                        <span class="text-right">Tracking {!! icon_checklisttrack() !!}</span>
                                </th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:15ex">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $row)
                                <tr>
                                    <td class="text-center">{{ $row->id }} <input type="checkbox" name="ar_id[]" value="{{ $row->id }}"> </td>
                                    <td class="">

                                        @if($row->shop_id == 1)
                                            {{ $row->shop_name }}<br/>
                                        @else
                                            {{ $row->buyer_name }}<br/>
                                            {{ $row->buyer_username }}<br/>
                                        @endif

                                    </td>
                                    <td class="">{!!  nl2br($row->buyer_address)  !!}</td>
                                    <td class="">
                                        @foreach($row->lineitems as $lineitem)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover m-b-0" id="lineForm_{{$row->id}}">
                                                <tbody>

                                                    <td>{{ $lineitem->title }}</td>
                                                    <td style="width:20ex" data-id="{{ $lineitem->id }}">
                                                        @if($lineitem->quantity > 1)
                                                            <span class="label label-info label-outline">${{$lineitem->price }} x {{ $lineitem->quantity }}</span>
                                                        @else
                                                            <span class="label label-outline">${{$lineitem->price }} x {{ $lineitem->quantity }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center" style="width:15ex">
                                                        <div id="fulfill_status_{{$lineitem->id}}">
                                                        {!! icon_fulfillment_status($lineitem->fulfillment_status) !!}<br/>
                                                        @if(count($row->fulfillments) > 0)
                                                            @foreach($row->fulfillments as $fulfillment)
                                                                @if($fulfillment['line_item_id'] == $lineitem->item_shop_id)
                                                                    {{ $fulfillment['tracking_number'] }}<br/>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-center" style="width:15ex">
                                                        <a href="#" id="tracking_{{$lineitem->id}}" data-type="text" data-value="" data-title="Change tracking number">
                                                            @if($lineitem->tracking != '')
                                                                {{ $lineitem->tracking }}
                                                            @else
                                                                ------
                                                            @endif
                                                        </a>
                                                        @if($lineitem->tracking != '' && $lineitem->tracking_status['des'] != 'RETURNED')
                                                            <div style="margin-top: 5px">
                                                                {!! icon_checktrack($lineitem->id) !!}
                                                            </div>
                                                            <input type="hidden" name="line_id[]" value="{{ $lineitem->id }}">
                                                            <input type="hidden" name="tracking[]" value="{{ $lineitem->tracking }}">
                                                        @endif
                                                        <div id="track_status_{{$lineitem->id}}">
                                                            {!!  echo_tracking_status($lineitem->tracking_status['des'])  !!}
                                                        </div>
                                                    </td>
                                                    <script>
                                                        $("#tracking_{{ $lineitem->id }}").editable({
                                                            type: 'text',
                                                            name: 'tracking',
                                                            placement: 'left',
                                                            pk: "{{ $lineitem->id }}",
                                                            url: "{{ route('admin.order.changetrack') }}",
                                                            success: function(response, newValue) {
                                                                //if(response.status == 'error') return response.msg; //msg will be shown in editable form
                                                                $("#track_status_{{$lineitem->id}}").html(response['des']);

                                                                var fulfillHtml = '<span class="label label-primary label-outline">fulfilled</span><br/>' + response['tracking'];
                                                                $("#fulfill_status_{{$lineitem->id}}").html(fulfillHtml);
                                                            }
                                                        });
                                                    </script>
                                                </tbody>
                                            </table>
                                        </div>

                                        @endforeach

                                        <span class="label">{{ $row->order_at->format('Y/m/d h:i') }}</span>
                                        <p>
                                            <a href="#" id="note_{{$row->id}}" data-type="text" data-value="" data-title="Change notes">
                                                @if($row->notes != '')
                                                    {{ $row->notes }}
                                                @else
                                                    ------
                                                @endif
                                            </a>
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" id="status_{{$row->id}}" data-type="select" data-value="" data-title="Change status">
                                            {!! echo_status($row->order_status) !!}
                                        </a>
                                    </td>
                                    <td id="action_colum" class="text-center" style="width:15ex">
                                        {!! icon_payment_member("'orders'","'payment_status'", $row->id, $row->payment_status) !!}
                                    </td>
                                </tr>

                                <script>

                                    $("#status_{{ $row->id }}").editable({
                                        source: [   {value: '1', text: 'Waiting'},
                                            {value: '2', text: 'Ordered'},
                                            {value: '3', text: 'Soon'},
                                            {value: '4', text: 'Shipped'},
                                            {value: '5', text: 'Complete'},
                                            {value: '6', text: 'Cancelled'},
                                        ],
                                        placement: 'left',
                                        pk: "{{ $row->id }}",
                                        url: "{{ route('admin.order.changestatus')  }}"
                                    });

                                    $("#note_{{ $row->id }}").editable({
                                        type: 'text',
                                        name: 'notes',
                                        placement: 'left',
                                        pk: "{{ $row->id }}",
                                        url: "{{ route('admin.order.changenotes') }}",
                                        success: function(response) {
                                        }
                                    });

                                    //$("#lineForm_{{$row->id}} :input").serializeArray();

                                </script>
                            @endforeach
                            </tbody>
                        </table>

                    <div class="text-center">
                        {!! $orders->appends(Request::all())->render() !!}
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

        function checktrack(id)
        {
            var btn = $("#btn_track_"+id);
            btn.addClass("disabled");
            var data = '<i class="fa fa-refresh fa-spin"></i> Re-checking...';
            $("#track_status_"+id).html(data);

            var url = "{{ route('admin.order.checkonetrack') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: id,
                },
                success: function(response){
                    $("#track_status_"+id).html(response['des']);
                    btn.removeClass("disabled");
                }

            });
        }



        function checklistrack(){
            var line_ids = $('input[name="line_id[]"]').map(function(){
                return this.value;
            }).get();

            var line_trackings = $('input[name="tracking[]"]').map(function(){
                return this.value;
            }).get();
            var url = "{{ route('admin.order.checktracksession') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    line_ids: line_ids,
                    line_trackings: line_trackings
                },
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        var html = '<i class="fa fa-refresh fa-spin"></i> Re-checking...';
                        $("#track_status_"+line_ids[0]).html(html);
                        crontrack();
                    }
                }
            });
        }

        function crontrack()
        {
            var url = "{{ route('admin.order.checktrackcron') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                dataType: 'json',
                success: function(data){
                    if(data.jobsleft == 0){
                        $.growl.notice({ message: "Checking done!" });
                        return false;
                    } else {
                        var curent_track_id = data.curent_track_id;
                        $("#track_status_"+curent_track_id).html(data.des);
                        var next_track_id = data.next_track_id;
                        var html = '<i class="fa fa-refresh fa-spin"></i> Re-checking...';
                        $("#track_status_"+next_track_id).html(html);
                        crontrack();
                    }
                }
            });
        }


    </script>
@endsection

