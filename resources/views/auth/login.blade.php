@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3">
        <div class="row" style="min-height: 85vh !important;">
            <div class="col-md-5 col-lg-6 col-xl-4 px-lg-5 d-flex align-items-center">
                <div class="w-100 py-5">
                    <div class="text-center"><x-logo/>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-Mail</label>
                            <input id="email" type="text"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('email') is-invalid @enderror"
                                name="email" placeholder="name@abt.ch" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <div class="row">
                                <div class="col">
                                    <label for="password" 
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Passwort</label>
                                </div>
                                @if (Route::has('password.request'))
                                    <div class="col-auto">
                                        <a tabindex="-1" class="form-text small text-muted"
                                        href="{{ route('password.request') }}">
                                            Passwort vergessen?
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <input id="password" placeholder="Passwort" type="password"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        Eingeloggt bleiben
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Submit-->
                        <button class="btn-block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mb-3">{{ __('Login') }}</button>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-7 col-lg-6 col-xl-8">
                <!-- Image-->
                <div style="background-image: url(/img/login.jpg); background-size: cover;" class="bg-cover h-100 mr-n3"></div>
            </div>
        </div>
    </div>
  @endsection
