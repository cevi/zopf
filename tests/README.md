# Tests

Übersicht über die automatisierten Tests des Zopfaktions-Tools: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 2. November 2026: **55 Testfälle in 7 Dateien**, Laufzeit rund 1 Sekunde.
Aktuelle Zahlen: `vendor/bin/phpunit --list-tests`

## Inhalt

- [Tests ausführen](#tests-ausführen)
- [Aufbau](#aufbau)
- [Übersicht aller Tests](#übersicht-aller-tests)
- [Fachliche Regeln, die die Tests festhalten](#fachliche-regeln-die-die-tests-festhalten)
- [Neuen Test schreiben](#neuen-test-schreiben)

---

## Tests ausführen

```bash
vendor/bin/phpunit                                              # alle Tests
vendor/bin/phpunit tests/Feature/TestInfrastructureTest.php     # eine Datei
vendor/bin/phpunit --filter test_name                           # einzelne Tests
```

Unter Sail: `sail artisan test` bzw. `sail php vendor/bin/phpunit`.

- **Datenbank:** immer SQLite im Arbeitsspeicher, nie die Datenbank aus der `.env` (`tests/TestCase.php` bricht sonst ab).
- **Config-Cache:** Bei gecachter Config (`php artisan config:cache`) greift `phpunit.xml` nicht; vorher `php artisan config:clear`.

---

## Aufbau

### Konfiguration

| Datei | Zweck |
|---|---|
| `phpunit.xml` | SQLite in-memory, Mail/Cache/Session/Queue/Broadcast lokal, Telescope aus, leere Schlüssel für Google und Pusher, alles mit `force="true"` |
| `tests/TestCase.php` | `RefreshDatabase` mit `TestingSeeder`, Schutz vor echter DB, `withoutVite()`, `Http::preventStrayRequests()` |
| `database/seeders/TestingSeeder.php` | Stammdaten jedes Tests: `BasisdatenSeeder` (Status, Rollen, Administrator mit ID 1, Global-Gruppe und -Aktion), aber statt `storage/app/cities.sql` nur zwei Ortschaften |

### Testdaten: Szenario

`tests/Support/ActionScenario.php`: `$s = ActionScenario::create();` baut eine Gruppe mit einer laufenden Aktion. Für Mandanten-Trennung zwei Szenarien bauen (`$a`, `$b`).

| Eigenschaft | Inhalt |
|---|---|
| `$s->group`, `$s->action` | Gruppe und aktive Aktion mit Standort (`center`) |
| `$s->groupleader` | Gruppenleiter (Rolle 5), Besitzer der Gruppe |
| `$s->actionleader` | Aktionsleiter (Rolle 10), Besitzer der Aktion |
| `$s->leader` | Leiter (Rolle 20) mit Route unterwegs |
| `$s->leaderWithoutRoute` | Leiter ohne Route (Randfall) |
| `$s->route` | Route des Leiters, Status unterwegs |
| `$s->plannedRoute` | Route in Planung, niemandem zugewiesen |
| `$s->orderOnRoute` | Bestellung auf `$s->route` |
| `$s->openOrder` | offene Bestellung ohne Route |
| `$s->pickUpOrder` | Bestellung zum Abholen |
| `$s->users()` | Rollen-Map für Data Provider |
| `ActionScenario::admin()` | Administrator aus dem Seeder |

### Factories

| Factory | Standard | States / Hilfen |
|---|---|---|
| `User::factory()` | Leiter, aktiv, verifiziert, kein Demo, Slug wie `MakeUserSlug` | `admin()`, `groupleader()`, `actionleader()`, `leader()`, `demo()`, `unverified()`, `inAction($action)` (setzt `group_id`/`action_id` und legt `group_users`/`action_users` an) |
| `Group::factory()` | nicht global, kein Demo | |
| `Action::factory()` | aktiv, eigene Gruppe, Standort-Adresse in der Gruppe | `planned()`, `completed()` |
| `Route::factory()` | geplant, zu Fuss | `underway()`, `completed()` |
| `Order::factory()` | offen, Adresse in der Gruppe der Aktion | `onRoute($route)`, `pickUp()` |
| `Address::factory()` | Adresse in Zürich | |

---

## Übersicht aller Tests

| Bereich | Datei | Fälle | Prüft |
|---|---|---:|---|
| Grundlagen | `TestInfrastructureTest` | 9 | Test-DB, Seeder, Factories, Szenario, Demo-Seeder, Seitenaufruf |
| Smoke | `Smoke/AllRoutesSmokeTest` | 6 | alle GET-Routen pro Rolle ohne Serverfehler, bekannte Fehler in `KNOWN_ISSUES` |
| Formulare | `FormComponentsTest` | 13 | Formular-Komponenten (Ersatz für `Form::`): old() vor Wert vor Model |
| Dashboard | `AdminDashboardTest` | 3 | Dashboard mit und ohne Aktion, Logbuch-Links |
| Dashboard | `TimeChartDataTest` | 5 | Zeitdiagramm: Zöpfe pro halbe Stunde |
| Wartung | `MaintenancePageTest` | 3 | öffentliche Wartungsseite (503) für `php artisan down` |
| Sicherheit | `Security/MaintenanceTest` | 16 | Admin-Wartungsseite: Artisan nur per POST und nur für Admins, alte `run-*`-URLs weg |

### Grundlagen

**[`TestInfrastructureTest`](Feature/TestInfrastructureTest.php)**: das Fundament, auf dem alle anderen Tests stehen.

| Test | Prüft |
|---|---|
| `test_database_is_sqlite_in_memory` | Tests laufen nie gegen MySQL |
| `test_seeder_creates_master_data` | Rollen, Administrator, Global-Gruppe und -Aktion |
| `test_scenario_creates_users_with_expected_roles` | Rollen-Methoden und Mitgliedschaften pro Rolle |
| `test_scenario_creates_orders_within_action` | Bestellungen, Route, Standort, Adressen in der eigenen Gruppe |
| `test_two_scenarios_are_separated` | zwei Szenarien mit getrennten Daten |
| `test_each_test_starts_with_master_data_only` | keine Daten aus vorherigen Tests |
| `test_demo_seeder_runs` | `DemoActionSeeder` läuft und legt die Demo-Logins an |
| `test_welcome_page_renders` | Startseite |
| `test_leader_sees_home_page` | `/home` als Leiter über den ganzen Stack |

### Smoke

**[`Smoke/AllRoutesSmokeTest`](Feature/Smoke/AllRoutesSmokeTest.php)**: ruft jede GET-Route mit jeder Rolle auf (Gast, Leiter, Leiter ohne Route, Aktionsleiter, Gruppenleiter, Admin), jeweils mit dem Standardobjekt und einer Route in Planung ohne Leiter. Erwartet keinen Status >= 500. Neue Routen werden automatisch mitgetestet; fehlt ein Wert für einen Parameter, meldet der Test das.

- `EXCLUDED`: Routen mit Seiteneffekten per GET (Aktion abschliessen, alles ausliefern, ausgeliefert/deponiert), OAuth, Demo-Logins, Pusher.
- `KNOWN_ISSUES`: bekannte Serverfehler mit Rolle und Grund. Der Test bleibt grün, solange genau diese auftreten; ein behobener Fehler macht ihn rot, bis der Eintrag entfernt ist.
- Grenze: findet nur 500er, keine fehlenden Berechtigungen (die sind 200).

### Formulare

**[`FormComponentsTest`](Feature/FormComponentsTest.php)**: die Komponenten in `resources/views/components/form` (`x-form.input`, `textarea`, `select`, `checkbox`, `label`, `submit`), die seit Laravel 13 `Form::` aus `laravelcollective/html` ersetzen. Wertauflösung in `App\View\FormValue`.

| Test | Prüft |
|---|---|
| `test_input_takes_value_from_model` | Wert aus dem Model (`:model`) |
| `test_explicit_value_wins_over_model` | `value` vor Model |
| `test_old_input_wins_over_value_and_model` | nach Validierungsfehler bleibt die Eingabe stehen |
| `test_value_is_escaped` | kein HTML aus Werten |
| `test_password_never_shows_value` | Passwort und Datei ohne Wert |
| `test_time_from_carbon_is_formatted` | Zeitfelder als `H:i` |
| `test_attributes_are_passed_through` | `id`, `class`, `required` |
| `test_textarea_takes_value_from_model` | Textfeld mit Standardgrösse |
| `test_select_marks_model_value_as_selected` | Auswahl aus dem Model |
| `test_select_uses_old_input` | Auswahl nach Validierungsfehler |
| `test_checkbox_state` | Model und explizites `checked` |
| `test_unchecked_checkbox_stays_unchecked_after_failed_submit` | abgewählte Checkbox bleibt abgewählt |
| `test_edit_page_renders_form_with_model_values` | echte Bearbeiten-Seite: Ziel-URL, `_method`, `_token`, Wert |

### Dashboard

**[`AdminDashboardTest`](Feature/AdminDashboardTest.php)**

| Test | Prüft |
|---|---|
| `test_groupleader_sees_dashboard` | Dashboard mit Aktion |
| `test_logbook_links_point_to_edit_page` | Logbuch-Links mit Platzhalter statt leerem Routenparameter |
| `test_groupleader_without_action_sees_dashboard` | ohne Aktion: Hinweis statt Fehler, keine Diagramm-URLs `api/action//` |

**[`TimeChartDataTest`](Feature/TimeChartDataTest.php)**: `Helper::GetTimeChartData()` (in PHP statt MySQL-SQL, damit es auch unter SQLite läuft).

| Test | Prüft |
|---|---|
| `test_rounds_to_nearest_half_hour_and_fills_gaps` | ab :15/:45 aufgerundet, Lücken mit 0 |
| `test_entries_without_time_are_ignored` | Einträge ohne Uhrzeit |
| `test_only_own_action_is_counted` | nur Einträge der eigenen Aktion |
| `test_empty_action_returns_zero` | Aktion ohne Einträge |
| `test_without_action_returns_zero` | Benutzer ohne Aktion (auch `GetZopfChartData`) |

### Wartung

**[`MaintenancePageTest`](Feature/MaintenancePageTest.php)**: die öffentliche Seite `errors/503`, die während `php artisan down` erscheint.

| Test | Prüft |
|---|---|
| `test_page_renders_without_vite_assets` | Seite rendert eigenständig (Logo eingebettet, keine `/build/`-Dateien) |
| `test_maintenance_mode_shows_page` | im Wartungsmodus kommt 503 mit dieser Seite |
| `test_custom_message_and_retry_after_are_shown` | eigene Nachricht und `Retry-After` als Neuladen-Intervall |

### Sicherheit

**[`Security/MaintenanceTest`](Feature/Security/MaintenanceTest.php)**: Wartungsseite für Admins (`admin/maintenance`). Ersetzt die GET-URLs `admin/run-migrations`, `admin/run-migrations-seed` und `admin/run-deployment`, die ohne Login erreichbar waren. Artisan wird in jedem Test gefakt, bevor eine Route aufgerufen wird.

| Test | Prüft |
|---|---|
| `test_old_get_urls_are_gone` (3) | alte URLs geben 404 und rufen Artisan nicht auf |
| `test_admin_sees_maintenance_page` | Seite mit Knöpfen und offenen Migrationen |
| `test_admin_runs_migrations` | `migrate --force`, Ausgabe erscheint auf der Seite |
| `test_admin_rebuilds_caches` | `config:cache` und `view:cache` |
| `test_admin_clears_caches` | `optimize:clear` |
| `test_guest_cannot_run_actions` (3) | Gast wird umgeleitet, Artisan nie aufgerufen |
| `test_groupleader_cannot_run_actions` (3) | Gruppenleiter wird umgeleitet, Artisan nie aufgerufen |
| `test_actions_reject_get` (3) | Aktionen per GET geben 405 (kein Auslösen über Links oder `<img>`) |

---

## Fachliche Regeln, die die Tests festhalten

Abgestimmt mit der Projektleitung. Wer sie ändert, muss auch die Tests anpassen.

- Rollen sind hierarchisch: eine kleinere `role_id` hat mehr Rechte (Administrator 1, Gruppenleiter 5, Aktionsleiter 10, Leiter 20).
- Aktionsleiter sehen nur ihre eigene Aktion, Leiter nur ihre eigenen Routen.
- Die Demo-Logins (`aktionschef@demo`, `leiter1@demo`) sind gewollt, auch auf Prod.

---

## Neuen Test schreiben

1. Ort wählen: `tests/Feature/Security` für Berechtigungen, `tests/Feature/Smoke` für seitenweite Prüfungen, sonst `tests/Feature`.
2. Von `Tests\TestCase` erben, Testdaten mit `ActionScenario` oder den Factories.
3. Bei Fehlern zuerst den Test (rot), dann den Fix.
4. Methoden `test_<englisch_snake_case>`, Erklärungen auf Deutsch.
5. Externe Dienste nie wirklich aufrufen (`Http::fake()`, `Mail::fake()`, Socialite-Mock). Achtung: `Helper::getGeocoder()` nutzt Guzzle direkt, `Http::fake()` greift dort nicht.
6. Diese Datei ergänzen.

### Vorlage für einen neuen Eintrag

```markdown
**[`NeuerTest`](Feature/NeuerTest.php)**: wofür der Test zuständig ist.

| Test | Prüft |
|---|---|
| `test_...` | ... |
```
