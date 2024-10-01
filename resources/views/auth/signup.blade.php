@extends("layouts.default")
@section("title", "Registrieren")
@section("content")
<div id="app">
    <app/>

    @if ($errors->has("firstname"))
    <span class="warning">
        {{$errors->first("firstname")}}
    </span>
    @endif

    @if ($errors->has("lastname"))
    <span class="warning">
        {{$errors->first("lastname")}}
    </span>
    @endif

    @if ($errors->has("email"))
    <span class="warning">
        {{$errors->first("email")}}
    </span>
    @endif

    @if ($errors->has("password"))
    <span class="warning">
        {{$errors->first("password")}}
    </span>
    @endif

    @if ($errors->has("password-repeat"))
    <span class="warning">
        {{$errors->first("password-repeat")}}
    </span>
    @endif

    <SignUp :action="{{ route('signup.post') }}"></SignUp>
</div>
@endsection