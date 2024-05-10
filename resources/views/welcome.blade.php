@extends('layouts.layout')

@section('content')
    <div class="wide" id="all">
        <!-- HERO SLIDER SECTION-->
        <section class="text-dark__white bg-cover bg-center primary-overlay overlay-dense"
                 style="background: url('img/photogrid.jpg') repeat">
            <div class="overlay-content py-5">
                <div class="container py-4">
                    <!-- Hero slider-->
                    <div class="swiper-container homepage-slider">
                        <div class="swiper-wrapper">
                            <!-- Hero Slide-->
                            <div class="swiper-slide h-auto mb-5">
                                <div class="row gy-5 h-100 align-items-center">
                                    <div class="col-lg-5 text-lg-end">
                                        <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight md:text-5xl lg:text-6xl">
                                            Online-Tool für die Verwaltung von Zopfaktionen</h1>
                                            <p class="mb-6 text-lg font-normal lg:text-xl8">Erfasse Bestellungen und Routen für deine Zopfaktion
                                                und
                                                behalte den Überblick über alles.
                                            </p>
                                    </div>
                                    <div class="col-lg-7"><img class="img-fluid" src="img/screenshots.png" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SERVICES SECTION-->
        <section class="py-5">
            <div class="container py-4">
                <div class="row gy-4">
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-desktop"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Alles Online</h4>
                        <p class="text-gray-600 dark:text-gray-200 text-sm">Verwalte deine Bestellungen und erstelle passende Routen direkt
                            im Dashboard. Verteile die Routen an die Lieferanten mit einem Knopfdruck.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-map-location-dot"></i></div>
                        <h4 class="text-2xl font-bold dark:text-white">Google-Maps-API</h4>
                        <p class="text-gray-600 dark:text-gray-200 text-sm">Dank der Google Maps API können die Bestellungen direkt auf
                            der Karte angeschaut werden und auch der schnellste Weg für die Routen wird
                            vorgeschlagen.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-file-import"></i></div>
                        <h4 class="text-2xl font-bold dark:text-white">Excel-Import</h4>
                        <p class="text-gray-600 dark:text-gray-200 text-sm">Importiere direkt alle deine Bestellungen aus einem Excel ins
                            System.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-chart-area"></i></div>
                        <h4 class="text-2xl font-bold dark:text-white">Graphische Darstellung</h4>
                        <p class="text-gray-600 dark:text-gray-200  text-sm">Erhalte eine graphische Übersicht über den ganzen Verlaufe der
                            Zopfaktion.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-print"></i></div>
                        <h4 class="text-2xl font-bold dark:text-white">Ausdruckbar</h4>
                        <p class="text-gray-600 dark:text-gray-200  text-sm">Falls gewünscht können die Routen als PDF heruntergeladen
                            werden.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center mb-4">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-mobile-alt"></i></div>
                        <h4 class="text-2xl font-bold dark:text-white">Mobile Tauglich</h4>
                        <p class="text-gray-600 dark:text-gray-200  text-sm">Die Lieferanten sehen ihre Routen als mobile taugliche Liste
                            und als Karte.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- BANNER SECTION-->
        <section class="py-5 bg-fixed bg-cover bg-center dark-overlay"
                 style="background: url(img/fixed-background-2.jpg)">
            <div class="overlay-content">
                <div class="container py-4 text-dark__white text-center">
                    <div class="icon icon-outlined icon-outlined-white icon-lg mx-auto mb-4">
                        <img src="img/cevi_pfad.svg" alt="Cevi Logo">
                    </div>
                    <h2 class="text-4xl font-bold text-white">Du willst deine Zopfaktion aufs nächste Level
                        bringen?</h2>
                    <p class="lead mb-4 text-white">Registrier dich jetzt und erstell gleich deine Aktion.</p><a
                        class="btn btn-outline-light btn-lg" href="/register">Jetzt registrieren</a>
                </div>
            </div>
        </section>
        <!-- SHOWCASE SECTION-->
        <section class="py-5 bg-pentagon" style="background: url(img/texture-bw.png)">
            <div class="container py-4">
                <!-- Counters-->
                <div class="row gy-4 text-center" id="counterUp">
                    <div class="col-lg-6 col-sm-6">
                        <div class="text-center text-gray-700">
                            <div class="icon-outlined border-gray-600 icon-lg mx-auto mb-3 icon-thin"><i
                                    class="fa-solid fa-truck"></i></div>
                            <h2 class="text-4xl counter font-bold" data-counter="{{$action_counter}}">
                                0</h2>
                            <h3 class="text-3xl font-bold">Zopf-Aktionen</h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <div class="text-center text-gray-700">
                            <div class="icon-outlined border-gray-600 icon-lg mx-auto mb-3 icon-thin"><i
                                    class="fa-solid fa-bread-slice"></i></div>
                            <h2 class="text-4xl counter font-bold" data-counter="{{$total_amount}}">
                                0</h2>
                            <h3 class="text-3xl font-bold">Anzahl Zöpfe</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- BLOCK SECTION-->
        <section class="py-5 bg-gray-200">
            <div class="container">
                <div class="row gy-4 align-items-center text-gray-700">
                    <div class="col-md-6 text-center">
                        <h2 class="text-4xl font-bold">Dashboard</h2>
                        <p class="lead mb-3">Erhalte den Überblick über deine Zopfaktion</p>
                        <p class="mb-3">Behalte den Überblick über deine Bestellungen, die Routen und alle wichtigen
                            Daten deiner Zopfaktion im Dashboard.</p>
                    </div>
                    <div class="col-md-6"><img class="img-fluid d-block mx-auto" src="img/dashboard.png"
                                               alt="..."></div>
                </div>
            </div>
        </section>
        <!-- GET IT-->
        <div class="bg-primary py-5">
            <div class="container text-center text-white">
                <div class="row">
                    <div class="col-lg-6 p-3">
                        <h3 class="text-3xl font-bold">Versuche es gleich selbst mit einem Test-Login
                            aus.</h3>
                    </div>
                    <div class="col-lg-2 p-3">
                        <a class="text-gray-100 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" href="/loginChef">Aktions-Leitende</a>
                    </div>
                    <div class="col-lg-2 p-3">
                        <a class="text-gray-100 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" href="/loginLeiter">Leitende</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
