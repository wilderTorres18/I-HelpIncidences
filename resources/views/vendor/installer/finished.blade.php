@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.final.title') }}
@endsection

@section('container')

    <p class="mt-2 max-w-3xl text-center mx-auto">
        ¡Puedes iniciar sesión o navegar a la página de inicio ahora!
    </p>

    <div class="buttons">
        <a href="{{ url('/') }}" target="_blank" class="button">Home Page</a>
        <a href="{{ url('/login') }}" target="_blank" class="button">Login</a>
    </div>


@endsection
