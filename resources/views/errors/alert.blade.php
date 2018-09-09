<div>
    @if (Session::has('message'))
        <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>{!!  Session::get('message')  !!}</p>
        </div>
    @endif
</div>