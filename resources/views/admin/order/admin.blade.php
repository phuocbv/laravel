@extends('admin.master')

@section('css')
    <!--
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

    <script>
        var member_list = [
            <?php
            foreach($members as $member){
                echo "{value: '".$member['id']."', text: '".$member['username']."'},";
            }
            ?>
        ];

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

    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Orders / </span>Manager</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" id="get-order-btn" data-placement="bottom" data-original-title="Get Orders"><i class="fa fa-cloud-download"></i> Get Orders</a>
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{ route('admin.order.create') }}" data-placement="bottom" data-original-title="Add"><i class="fa fa-plus"></i></a>
            <a class="btn btn-danger" onclick="submitDeleteForm()" href="javascript:;" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
        </div>
    </div>

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
                                    <select class="input-sm" name="mailacc" style="width: 100%">
                                        <option value="0">Mail Account</option>
                                        @foreach($emails as $email)
                                            <option value="{{$email->id}}">{{ $email->name }}({{$email->email}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <select class="input-sm" name="user_id" style="width: 100%">
                                        <option value="0">Member</option>
                                        @foreach($members as $member)
                                            <option value="{{$member->id}}">{{ $member->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
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
                                <div class="form-group col-sm-2">
                                    <select class="input-sm" name="payment" style="width: 100%">
                                        <option value="3">Pay Option</option>
                                        {!! echo_pay_option() !!}
                                    </select>
                                </div>
                                <div class="form-group col-sm-12" style="height: 10px;"></div>
                                <div class="form-group col-sm-6">
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



                                <div class="form-group col-sm-2">
                                    <input type="text" placeholder="ID" name="ids" class="form-control" style="width: 100%">
                                </div>
                                <div class="form-group col-sm-2">
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
                <form action="{{ route('admin.order.assignall') }}" id="adminForm" method="POST">
                <div class="panel-heading">
                    <span class="label label-warning">Total: ${{ number_format($total, 2) }}</span>
                    <span class="label label-primary">{{$orders->total()}} orders</span>

                    <span class="label label-danger">Waiting: {{$total_status[1]}}</span>
                    <span class="label label-warning">Ordered: {{$total_status[2]}}</span>
                    <span class="label label-info">Soon: {{$total_status[3]}}</span>
                    <span class="label label-success">Shipped: {{$total_status[4]}}</span>
                    <span class="label label-primary">Completed: {{$total_status[5]}}</span>
                    <span class="label label-default">Canceled: {{$total_status[6]}}</span>
                    <span class="label label-danger label-outline">Returned: {{$total_status[7]}}</span>
                    <div class="panel-heading-controls">


                        <select class="form-control input-sm" name="assign_member_id" style="width: 100px;">
                            @foreach($members as $member)
                                <option value="{{$member->id}}">{{ $member->username }}</option>
                            @endforeach
                        </select>
                        <a data-container="body" data-original-title="Assign to" href="javascript:;" onclick="submitAssignForm()" data-placement="top" data-toggle="tooltip" class="btn btn-sm btn-primary add-tooltip"><i class="fa fa-user-plus"></i>&nbsp;&nbsp;Assign</a>

                        <a data-container="body" data-original-title="Mark Paid" href="javascript:;" onclick="submitPaidForm()" data-placement="top" data-toggle="tooltip" class="btn btn-sm btn-primary add-tooltip"><i class="fa fa-usd"></i>&nbsp;&nbsp;Mark Paid</a>

                        <a data-container="body" data-original-title="Upload to GG Sheet" href="javascript:;" onclick="submitGSheetForm()" data-placement="top" data-toggle="tooltip" class="btn btn-sm btn-primary add-tooltip"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Upload to GG</a>


                        <div class="clearfix"></div>
                    </div>
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
                        <table cellpadding="0" cellspacing="0" border="0" class="table table_order">
                            <thead>
                            <tr>
                                <th style="width:1ex"><input type="checkbox" name="sa" id="sa" onclick="checkbox('sa', 'ar_id[]', 'adminForm')"></th>
                                <th>Buyer</th>
                                <th>Address</th>
                                <th>Items
                                    <div class="panel-heading-controls">
                                    <span style="width: 15ex" class="text-center">Tracking {!! icon_checklisttrack() !!}</span>
                                    <span style="width: 15ex" class="text-center">Check Sheet {!! icon_checklistsheet() !!}</span>
                                    </div>
                                </th>
                                <th class="text-center">Payment Status</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:15ex">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $row)
                                @php
                                    $color = "";
                                    if($row->order_status == 1){
                                         $color = '';
                                    }
                                    if($row->order_status == 2){
                                         $color = 'warning';
                                    }
                                    if($row->order_status == 3){
                                         $color = 'warning';
                                    }
                                    if($row->order_status == 4){
                                         $color = 'success';
                                    }
                                    if($row->order_status == 5){
                                         $color = 'info';
                                    }
                                    if($row->order_status == 6){
                                         $color = 'active';
                                    }
                                    if($row->order_status == 7){
                                         $color = 'danger';
                                    }


                                @endphp
                                <tr class="{{$color}}">
                                    <td class="text-center">{{ $row->id }} <input type="checkbox" name="ar_id[]" value="{{ $row->id }}"> </td>
                                    <td class="">

                                        @if($row->shop_id == 1)
                                            {{ $row->shop_name }}<br/>
                                            <span class="label label-primary label-tag">{{ $row->email->name }}</span>
                                        @else
                                            {{ $row->buyer_name }}<br/>
                                            {{ $row->buyer_username }}<br/>
                                            <span class="label label-success label-tag">{{ $row->email->name }}</span>
                                        @endif

                                    </td>
                                    <td class="">{!!  nl2br($row->buyer_address)  !!}</td>
                                    <td class="show_line_item">
                                        @foreach($row->lineitems as $lineitem)
                                        <div class="table-responsive">
                                            <table class="table b-a-0" id="lineForm_{{$row->id}}">
                                                <tr class="{{$color}}">
                                                    <td>{{ $lineitem->title }}</td>
                                                    <td style="width:20ex" class="text-center line_item_price" data-id="{{ $lineitem->id }}">
                                                        @if($lineitem->quantity > 1)
                                                            <span class="label label-info label-outline">${{$lineitem->price }} x {{ $lineitem->quantity }}</span>

                                                        @else
                                                            <span class="label label-outline">${{$lineitem->price }} x {{ $lineitem->quantity }}</span>
                                                        @endif
                                                            <br>
                                                            <span class="input_line_item" style="display:none;">
                                                            <input type="text" style="width: 60px" ><br>
                                                            <input type="text" style="width: 60px" >
                                                            </span>
                                                    </td>
                                                    <td class="text-center" style="width:25ex">
                                                        <div id="fulfill_status_{{$lineitem->id}}">
                                                        {!! icon_fulfillment_status($lineitem->fulfillment_status) !!}<br/>
                                                        @if(count($row->fulfillments) > 0)
                                                            @foreach($row->fulfillments as $fulfillment)
                                                                @if($fulfillment['line_item_id'] == $lineitem->item_shop_id)
                                                                    {{ $fulfillment['tracking_number'] }}<br/>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                            <div id="fulfillwq_status_{{$lineitem->id}}"></div>
                                                        @if(strtolower($lineitem->fulfillment_status) != 'fulfilled' && $lineitem->quantity > 1)
                                                            <a href="#" onclick="addTracking({{$lineitem->id}})" id="add_tracking_{{$lineitem->id }}" data-type="text" data-value="" data-title="Add tracking number">
                                                                <i class="panel-title-icon fa fa-plus"></i>
                                                            </a>
                                                            @if($row->shop_id == 1)
                                                                    <input type="hidden" id="fulfillable_quantity_{{$lineitem->id}}" value="{{$lineitem->fulfillable_quantity}}">
                                                            @else
                                                                    <input type="hidden" id="fulfillable_quantity_{{$lineitem->id}}" value="{{$lineitem->quantity}}">
                                                            @endif

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
                                                    <td class="text-center" style="width:15ex">
                                                        <div id="gsheet_{{$lineitem->id}}">
                                                            @if($row->order_status != 5)
                                                                {!! icon_gsheet($lineitem->id, $lineitem->sheet_range) !!}
                                                                <br/>
                                                                <div id="upload_sheet_status_{{$lineitem->id}}"></div>
                                                            @endif

                                                            @if($lineitem->sheet_range != null && strtolower($lineitem->fulfillment_status) != 'fulfilled')
                                                                    <input type="hidden" name="gline_id[]" value="{{ $lineitem->id }}">
                                                            @endif
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
                                                </tr>
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
                                        <span class="label label-outline">{{ $row->shop_payment_status }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" id="status_{{$row->id}}" data-type="select" data-value="" data-title="Change status">
                                            {!! echo_status($row->order_status) !!}
                                        </a>
                                    </td>
                                    <td id="action_colum" class="text-center" style="width:15ex">
                                        <?php
                                            $editurl = route('admin.order.edit', ['id' => $row->id]);
                                        ?>
                                        <div style="padding-bottom: 5px">
                                            <a href="#" id="assign_{{$row->id}}" data-type="select" data-value="" data-title="Assign member">{{$row->user->username or '......'}}</a>
                                        </div>
                                        {!! icon_edit($editurl) !!}
                                        {!! icon_del($row->id) !!}
                                        {!! icon_payment("'orders'","'payment_status'", $row->id, $row->payment_status) !!}
                                    </td>
                                </tr>

                                <script>
                                    $("#assign_<?= $row->id ?>").editable({
                                        prepend: "not selected",
                                        source: member_list,
                                        placement: 'left',
                                        pk: "{{ $row->id }}",
                                        url: "{{ route('admin.order.assignorder') }}"
                                    });

                                    $("#status_{{ $row->id }}").editable({
                                        source: [   {value: '1', text: 'Waiting'},
                                            {value: '2', text: 'Ordered'},
                                            {value: '3', text: 'Soon'},
                                            {value: '4', text: 'Shipped'},
                                            {value: '5', text: 'Complete'},
                                            {value: '6', text: 'Cancelled'},
                                            {value: '7', text: 'Returned'},
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

            $('#get-order-btn').on('click', function () {
                bootbox.dialog({
                    message: '<i class="fa fa-spinner fa-pulse"></i> Getting orders, please wait',
                    title: "Loading orders from internet...",
                    buttons: {
                        success: {
                            label: "Close",
                            className: "btn-success"
                        }
                    },
                    className: "bootbox-sm"
                });
                getOrders();
            });
            $('.table_order').on('click', '.line_item_price span.label-outline', function () {
                var current = $(this);
                var lineItemPrice = current.closest('.line_item_price');
                current.remove();
                lineItemPrice.find('.input_line_item').addStyle('display', 'block');
            });
        });

        function getOrders()
        {
            var url = "{{ route('admin.order.loadorders')  }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data){
                    if(data == 'success'){
                        //$('#getmail-btn').bootbox.dialog(false);
                        location.reload();
                    }
                    //$("#publish"+id).html(data.published);
                    //$("#publish"+id+" a").attr("data-original-title","Mowr");
                    //bootbox.dialog(false);
                    //windows.location.reload();
                }

            });
        }

        function del(id){
            bootbox.confirm({
                message: "Are you sure to delete this Order?",
                callback: function(result) {
                    if(result == true){
                        var url = "{{ route('admin.order.destroy') }}";
                        $.ajax({
                            type: 'DELETE',
                            url: url,
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE",
                                id: id
                            }

                        }).done(function(){
                            location.reload();
                        });
                    }
                },
                className: "bootbox-sm"
            });
        }



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

        function addTracking(lineItemid){

            var fulfillable_quantity = $('#fulfillable_quantity_'+lineItemid).val();

            bootbox.confirm('<form class="form-horizontal">\n' +
                '  </div>\n' +
                '  <div class="form-group form-group-sm">\n' +
                '    <label for="grid-input-sm-1" class="col-md-3 control-label">Tracking</label>\n' +
                '    <div class="col-md-9">\n' +
                '      <input class="form-control" id="input_tracking_number_'+lineItemid+'" placeholder="Tracking number" type="text">\n' +
                '    </div>\n' +
                '  </div>\n' +
                '  <div class="form-group form-group-sm">\n' +
                '    <label for="grid-input-sm-1" class="col-md-3 control-label">Quantity</label>\n' +
                '    <div class="col-md-9">\n' +
                '      <input class="form-control" id="input_quantity_'+lineItemid+'" placeholder="Quantity" type="number" min="1" max="'+fulfillable_quantity+'" value="1">\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</form>', function(result) {
                if(result){
                    var tracking =  $("#input_tracking_number_"+lineItemid).val();
                    var qty =  $("#input_quantity_"+lineItemid).val();

                    if(qty > fulfillable_quantity){
                        alert('Wrong Quantify Number')
                    }else {
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('admin.order.addtrackwq') }}",
                            data: {
                                lineid: lineItemid,
                                tracking: tracking,
                                qty: qty
                            },
                            success: function(data){
                                if(data['msg'] == 'success'){
                                    //$('#getmail-btn').bootbox.dialog(false);
                                    location.reload();
                                    //$("#fulfillwq_status_"+lineItemid).html(tracking);
                                }
                            }

                        });
                    }
                }
            });


        }


        function pay(table,field,id,status)
        {
            $("#pay_"+id).html('<a href="javascript:;" class="btn btn-xs btn-default add-tooltip"><i class="fa fa-refresh fa-spin red"></i></a>');
            var url = "{{ route('admin.paid') }}";
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    table: table,
                    field: field,
                    id: id,
                    status: status
                },
                success: function(data){
                    $("#pay_"+id).html(data.paid);
                    //$("#publish"+id+" a").attr("data-original-title","Mowr");
                }

            });

        }


        function checklistsheet(){
            var line_ids = $('input[name="gline_id[]"]').map(function(){
                return this.value;
            }).get();


            var url = "{{ route('admin.order.checksheetsession') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    line_ids: line_ids,
                },
                dataType: 'json',
                success: function(data){
                    if(data.status == 1){
                        var html = '<i class="fa fa-refresh fa-spin"></i> Checking...';
                        $("#upload_sheet_status_"+line_ids[0]).html(html);
                        cronsheet();
                    }
                }
            });
        }

        function cronsheet()
        {
            var url = "{{ route('admin.order.checksheetcron') }}";
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
                        var curent_sheet_id = data.curent_sheet_id;
                        $("#upload_sheet_status_"+curent_sheet_id).html("");
                        if(data['size'] == 1){
                            $("#tracking_"+curent_sheet_id).html(data["tracking"]);
                        } else {
                            $("#upload_sheet_status_"+curent_sheet_id).html(data["tracking"]);
                        }
                        var next_sheet_id = data.next_sheet_id;
                        var data = '<i class="fa fa-refresh fa-spin"></i> Checking...';
                        $("#upload_sheet_status_"+next_sheet_id).html(data);
                        cronsheet();
                    }
                }
            });
        }

        function upload_sheet(lineid){
            var btn = $("#upload_sheet_"+lineid);
            btn.addClass("disabled");
            var data = '<i class="fa fa-refresh fa-spin"></i> Uploading...';
            $("#upload_sheet_status_"+lineid).html(data);

            var url = "{{ route('admin.order.uploadsheet') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: lineid,
                },
                success: function(response){
                    $("#gsheet_"+lineid).html(response);
                }

            });
        }

        function gettrack_sheet(lineid){
            var data = '<i class="fa fa-refresh fa-spin"></i> Get info from sheet...';
            $("#upload_sheet_status_"+lineid).html(data);

            var url = "{{ route('admin.order.getsheettrack') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: lineid,
                },
                success: function(response){
                    $("#upload_sheet_status_"+lineid).html("");
                    if(response['size'] == 1){
                        $("#tracking_"+lineid).html(response["tracking"]);
                    } else {
                        $("#upload_sheet_status_"+lineid).html(response["tracking"]);
                    }
                }

            });
        }

        function submitGSheetForm(){
            var checked = $('input[type=checkbox]').is(':checked');
            if(!checked){
                bootbox.alert({
                    message: "Vui lòng chọn ít nhất 1 order để upload lên GG Sheet",
                    className: "bootbox-sm"
                });
            } else {
                bootbox.confirm({
                    message: "Bạn có chắc là muốn Upload lên GG Sheet không?",
                    callback: function(result) {
                        if(result == true){
                            $('#adminForm').attr('action', 'order/sheetall');
                            $('#adminForm').attr('method', 'POST');
                            $("#adminForm").submit();
                        }
                    },
                    className: "bootbox-sm"
                });
            }

        }

    </script>
@endsection

