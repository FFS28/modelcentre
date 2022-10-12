<a href="{{ route('shop.home.index') }}">
    {{-- <img
        src="{{ asset('themes/velocity/assets/images/static/logo-text-white.png') }}"
        class="logo full-img" alt="" width="200" height="50" /> --}}
    <img class="logo full-img" alt="" width="200" height="200" src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/velocity/assets/images/logo-text.png') }}" alt="" />
</a>