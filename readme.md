<p align="center"><img src="https://zopf.cevi.tools/img/logo.svg" width="400"></p>
<p align="center"><img src="https://zopf.cevi.tools/img/photogrid.jpg"></p>

## Zopfaktions-Tool für Vereine

Das Zopfaktions-Tool bietet viele Funktionen, welche deine Zopfaktion unterstützt:

- Verwalte deine Bestellungen und erstelle passende Routen direkt
  im Dashboard. Verteile die Routen an die Lieferanten mit einem Knopfdruck.
- Dank der Google Maps API können die Bestellungen direkt auf
  der Karte angeschaut werden und auch der schnellste Weg für die Routen wird
  vorgeschlagen.
- Importiere direkt alle deine Bestellungen aus einem Excel ins
  System.
- Erhalte eine graphische Übersicht über den ganzen Verlaufe der
  Zopfaktion.
- Falls gewünscht können die Routen als PDF heruntergeladen
  werden.
- Die Lieferanten sehen ihre Routen als mobile taugliche Liste
  und als Karte.

## Lokale Installation

Das Tool ist ein PHP-Projekt basierend auf dem Framework [Laravel](https://laravel.com/). Um es lokal auszuführen
brauchst du einen [Docker Container](https://docs.docker.com/).

Um das Tool lokal bei dir benutzen zu können musst du den Quellcode herunterladen und
mittels [Laravel Sail](https://laravel.com/docs/9.x/sail) starten:

```bash
# clone the GitRepo
git clone https://github.com/jeromesigg/zopf
cd zopf

# install the dependencies
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
    
cp .env.example .env

# launch the application
./vendor/bin/sail up

# initialize the database
./vendor/bin/sail artisan migrate --seed
```

Anschliessend kannst du dein Tool unter [http://localhost](http://localhost) aufrufen.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
