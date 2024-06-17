<div class="d-flex flex-column flex-shrink-0 text-white bg-dark py-3">
    <div class="px-4">
        <span class="fs-4">Menu</span>
    </div>
    @if(Auth()->user()->hasRole('admin'))
        @include('includes.admin.menu.admin')
    @endif
    @if(Auth()->user()->hasRole('client'))
        @include('includes.admin.menu.client')
    @endif
    <hr>
</div>
