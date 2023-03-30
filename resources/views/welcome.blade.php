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
                                        <h1 class="text-uppercase">Online-Tool für die Verwaltung von Zopfaktionen</h1>
                                        <ul class="list-unstyled text-uppercase fw-bold mb-0">
                                            <li class="mb-2">Erfasse Bestellungen und Routen für deine Zopfaktion und
                                                behalte den Überblick über alles.
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-7"><img class="img-fluid" src="img/screenshots.png" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="swiper-pagination swiper-pagination-light"></div> --}}
                    </div>
                </div>
            </div>
        </section>
        <!-- SERVICES SECTION-->
        <section class="py-5">
            <div class="container py-4">
                <div class="row gy-4">
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-desktop"></i></div>
                        <h4 class="text-uppercase mb-3">Alles Online</h4>
                        <p class="text-gray-600 text-sm">Verwalte deine Bestellungen und erstelle passende Routen direkt
                            im Dashboard. Verteile die Routen an die Lieferanten mit einem Knopfdruck.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-map-location-dot"></i></div>
                        <h4 class="text-uppercase mb-3">Google-Maps-API</h4>
                        <p class="text-gray-600 text-sm">Dank der Google Maps API können die Bestellungen direkt auf
                            der Karte angeschaut werden und auch der schnellste Weg für die Routen wird
                            vorgeschlagen.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-file-import"></i></div>
                        <h4 class="text-uppercase mb-3">Excel-Import</h4>
                        <p class="text-gray-600 text-sm">Importiere direkt alle deine Bestellungen aus einem Excel ins
                            System.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid fa-chart-area"></i></div>
                        <h4 class="text-uppercase mb-3">Graphische Darstellung</h4>
                        <p class="text-gray-600 text-sm">Erhalte eine graphische Übersicht über den ganzen Verlaufe der
                            Zopfaktion.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-print"></i></div>
                        <h4 class="text-uppercase mb-3">Ausdruckbar</h4>
                        <p class="text-gray-600 text-sm">Falls gewünscht können die Routen als PDF heruntergeladen
                            werden.</p>
                    </div>
                    <!-- Service-->
                    <div class="col-lg-4 col-md-6 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fa-solid  fa-mobile-alt"></i></div>
                        <h4 class="text-uppercase mb-3">Mobile Tauglich</h4>
                        <p class="text-gray-600 text-sm">Die Lieferanten sehen ihre Routen als mobile taugliche Liste
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
                    <h2 class="text-uppercase mb-3">Du willst deine Zopfaktion aufs nächste Level bringen?</h2>
                    <p class="lead mb-4">Registrier dich jetzt und erstell gleich deine Aktion.</p><a
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
                            <h1 class="counter mb-3" data-counter="{{$action_counter}}">0</h1>
                            <h2 class="text-uppercase fw-bold mb-0">Zopf-Aktionen</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <div class="text-center text-gray-700">
                            <div class="icon-outlined border-gray-600 icon-lg mx-auto mb-3 icon-thin"><i
                                    class="fa-solid fa-bread-slice"></i></div>
                            <h1 class="counter mb-3" data-counter="{{$total_amount}}">0</h1>
                            <h2 class="text-uppercase fw-bold mb-0">Anzahl Zöpfe</h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- BLOCK SECTION-->
        <section class="py-5 bg-gray-200">
            <div class="container">
                <div class="row gy-4 align-items-center">
                    <div class="col-md-6 text-center">
                        <h2 class="text-uppercase">Dashboard</h2>
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
        <div class="bg-primary py-5 text-dark__white">
            <div class="container text-center">
                <div class="row">
                    <div class="col-lg-6 p-3">
                        <h3 class="text-uppercase mb-0">Versuche es gleich selbst mit einem Test-Login aus.</h3>
                    </div>
                    <div class="col-lg-2 p-3">
                        <a class="btn btn-outline-light" href="/loginChef">Aktions-Leitende</a>
                    </div>
                    <div class="col-lg-2 p-3">
                        <a class="btn btn-outline-light" href="/loginLeiter">Leitende</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
