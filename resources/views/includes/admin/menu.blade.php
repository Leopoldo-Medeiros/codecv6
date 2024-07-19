<div class="d-inline-flex py-3 px-3">
    @if(Auth()->user()->hasRole('admin'))
        @include('includes.admin.menu.admin')
    @endif
    @if(Auth()->user()->hasRole('client'))
        @include('includes.admin.menu.client')
    @endif
    <hr>
</div>
