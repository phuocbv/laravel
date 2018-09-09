@extends('admin.master')

@section('content')
    <div class="page-header">
        <h1><span class="text-muted font-weight-light"><i class="page-header-icon fa fa-users"></i>Users / </span>Manager</h1>

        <div class="pull-right" id="top-righ-icon">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{ route('admin.member.create') }}" data-placement="bottom" data-original-title="Add"><i class="fa fa-plus"></i></a>
            <a class="btn btn-danger" onclick="submitAdminForm()" href="javascript:;" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
        </div>
    </div>

    <div>
        @include('errors.alert')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <span class="panel-title"><i class="panel-title-icon fa fa-plus"></i>List Member</span>
                </div>
                <div class="panel-body">
                    <form action="" id="adminForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token()  }}" />
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-hover admin-table">
                            <thead>
                            <tr>
                                <th style="width:1ex"><input type="checkbox" name="sa" id="sa" onclick="checkbox('sa', 'ar_id[]', 'adminForm')"></th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Group</th>
                                <th class="text-center" style="width:15ex">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $row)
                                <tr>
                                    <td class="valign-middle"><input type="checkbox" name="ar_id[]" value="{{ $row->id }}"></td>
                                    <td class="valign-middle">{{ $row->name }}</td>
                                    <td class="valign-middle">{{ $row->username }}</td>
                                    <td class="valign-middle ">{{ $row->email }}</td>
                                    <td class="valign-middle ">
                                        @if($row->group_id == 1)
                                            Admin
                                        @elseif($row->group_id == 2)
                                            Mod
                                        @elseif($row->group_id == 3)
                                            Member
                                        @endif
                                    </td>
                                    <td id="action_colum" class="text-center valign-middle" style="width:15ex">
                                        <?php
                                        $editurl = route('admin.member.edit', ['id' => $row->id]);
                                        ?>
                                        {!! icon_edit($editurl) !!}
                                        {!! icon_active("'users'","'active'", $row->id, $row->active) !!}
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
    </script>
@endsection

