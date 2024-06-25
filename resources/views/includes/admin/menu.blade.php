<div class="d-flex flex-column flex-shrink-0 text-black py-3">
    @if(Auth()->user()->hasRole('admin'))
        @include('includes.admin.menu.admin')
    @endif
    @if(Auth()->user()->hasRole('client'))
        @include('includes.admin.menu.client')
    @endif
    <hr>
</div>
