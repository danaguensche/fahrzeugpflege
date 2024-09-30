@extends("layouts.default")
@section("title", "Anmelden")
@section("content")
<div id="app">
    <app />
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
</div>
@endsection