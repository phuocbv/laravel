@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-envelope"></i>Emails / </span>Manager</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{ route('admin.email.create') }}" data-placement="bottom" data-original-title="Add"><i class="fa fa-plus"></i></a>
        </div>
    </div>

    <div>
        @include('errors.alert')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-envelope"></i>List Email Account</span>
                </div>
                <div class="panel-body">
                    <form action="" id="adminForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token()  }}" />
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-hover admin-table">
                            <thead>
                            <tr>
                                <th style="width:1ex"><input type="checkbox" name="sa" id="sa" onclick="checkbox('sa', 'ar_id[]', 'adminForm')"></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Notes</th>
                                <th class="text-center" style="width:15ex">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($emails as $row)
                                <tr>
                                    <td class="valign-middle"><input type="checkbox" name="ar_id[]" value="{{ $row->id }}"></td>
                                    <td class="valign-middle">
                                        @if($row->shop_id == 1)
                                            <span class="label label-primary label-tag">shoptify</span>
                                        @else
                                            <span class="label label-success label-tag">ebay</span>
                                        @endif
                                        {{ $row->name }}
                                    </td>
                                    <td class="valign-middle">{{ $row->email }}</td>
                                    <td class="valign-middle">{{ $row->notes }}</td>
                                    <td id="action_colum" class="text-center valign-middle" style="width:15ex">
                                        <?php
                                            $editurl = route('admin.email.edit', ['id' => $row->id]);
                                        ?>
                                        {!! icon_edit($editurl) !!}
                                        {!! icon_active("'emails'","'active'", $row->id, $row->active) !!}
                                        {!! icon_del($row->id) !!}
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

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

        function publish(table,field,id,status)
        {
            $("#publish"+id).html('<a href="javascript:;" class="btn btn-xs btn-default add-tooltip"><i class="fa fa-refresh fa-spin red"></i></a>');
            var url = "{{ route('admin.active') }}";
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
                    $("#publish"+id).html(data.published);
                    //$("#publish"+id+" a").attr("data-original-title","Mowr");
                }

            });

        }

        function del(id){
            bootbox.confirm({
                message: "Are you sure to delete this item?",
                callback: function(result) {
                    if(result == true){
                        var url = "{{ route('admin.email.delete') }}";
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                _token: "{{ csrf_token() }}",
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

    </script>
@endsection

