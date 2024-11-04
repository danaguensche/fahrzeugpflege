@extends("layouts.default")
@section("title", "Anmelden")
@section("content")
<div id="app">
    <app />

    <login :action="{{ route('login.post') }}"></login>
    <SignUp :action="{{ route('signup.post') }}"></SignUp>

    @if(session()->has("success"))
    <div class="alert alert-success">
        {{session()->get("success")}}
    </div>
    @endif

    @if(session()->has("error"))
    <div class="alert alert-error">
        {{session()->get("error")}}
    </div>
    @endif

</div>
@endsection