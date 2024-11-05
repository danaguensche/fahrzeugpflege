@extends("layouts.default")
@section("title", "Anmelden")
@section("content")
<div id="app">
    <app></app>

    <login :action="'{{ route('login.post') }}'"></login>
    <sign-up :action="'{{ route('signup.post') }}'"></sign-up>

    @if(session()->has("success"))
    <div class="alert success">
        {{ session()->get("success") }}
    </div>
    @endif

    @if(session()->has("error"))
    <div class="alert error">
        {{ session()->get("error") }}
    </div>
    @endif
</div>
@endsection
