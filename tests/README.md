# Tests

Übersicht über die automatisierten Tests des Zopfaktions-Tools: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 2. November 2026: **133 Testfälle in 18 Dateien** (PHP), 4 JS-Tests und 16 Browser-Tests (Dusk), Laufzeit rund 1 Sekunde.
Aktuelle Zahlen: `vendor/bin/phpunit --list-tests`

## Inhalt

- [Tests ausführen](#tests-ausführen)
- [Aufbau](#aufbau)
- [Browser-Tests (Dusk)](#browser-tests-dusk)
- [CI und Code-Stil](#ci-und-code-stil)
- [Übersicht aller Tests](#übersicht-aller-tests)
- [Fachliche Regeln, die die Tests festhalten](#fachliche-regeln-die-die-tests-festhalten)
- [Neuen Test schreiben](#neuen-test-schreiben)

---

## Tests ausführen

```bash
vendor/bin/phpunit                                              # alle Tests
vendor/bin/phpunit tests/Feature/TestInfrastructureTest.php     # eine Datei
vendor/bin/phpunit --filter test_name                           # einzelne Tests
npm run test:js                                                 # JS-Tests (Node, ohne Abhängigkeiten)
php artisan dusk                                                # Browser-Tests (im Sail-Container, siehe unten)
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

## Browser-Tests (Dusk)

Ganze Abläufe im echten Chrome (Selenium-Container) gegen die App unter `http://laravel.test`. Laufen nicht in der CI.

```bash
./vendor/bin/sail up -d selenium      # einmal, auf dem Host
npm run build                         # Dusk testet public/build
php artisan dusk                      # im App-Container (sail shell)
```

- **Umgebung:** `.env.dusk.local` (Dusk tauscht während des Laufs die `.env` aus), Datenbank `database/dusk.sqlite`. `tests/DuskTestCase.php` leert vor jedem Test alle Tabellen und seedet `TestingSeeder`; es bricht ab, wenn nicht `dusk.sqlite` konfiguriert ist. Den Lauf nie abbrechen, sonst bleibt `.env` ersetzt (Original in `.env.backup`).
- **Keine echten Dienste:** Der Browser erreicht Pusher und Google Maps nicht (`--host-resolver-rules`), ausgehende HTTPS-Anfragen des Servers scheitern (`HTTPS_PROXY` auf eine tote Adresse). Deshalb nicht testbar: Bestellung bearbeiten und Aktion bearbeiten (Geocoding bei Google).
- **Bei Fehlern:** `tests/Browser/screenshots`, `console`, `source`.

| Datei | Prüft |
|---|---|
| [`Browser/AdminListsTest`](Browser/AdminListsTest.php) (5) | Routen, Bestellungen, Leiter, Aktionen, Gruppen: Zeilen erscheinen, kein DataTables-Alert, keine JS-Fehler |
| [`Browser/FormsTest`](Browser/FormsTest.php) (7) | umgebaute Formulare (Form:: → Komponenten): Route, Gruppe, Benutzer bearbeiten; Profil mit Validierungsfehler (Eingabe bleibt); Logbuch-Eintrag auf dem Dashboard (Zeitfeld vorbelegt, HTML maskiert, auch nach `UpdateList()`); Bestellung zum Abholen; Leiter übergibt Bestellung |
| [`Browser/SetupTest`](Browser/SetupTest.php) (1) | Grundaufbau: Login, Testdaten |

---

## CI und Code-Stil

- **GitHub Actions** ([`.github/workflows/tests.yml`](../.github/workflows/tests.yml)): Jobs `pint`, `phpunit` und `js` bei jedem Push auf `develop` und bei jedem Pull Request. Der Prod-Build (`prod_build.yml`, Push auf `master`) ruft die Tests zuerst auf und baut nur, wenn sie grün sind. Die CI läuft ohne `.env`-Werte und ohne `public/build`: Was lokal nur dank der eigenen `.env` grün ist, fällt dort auf.
- **Code-Stil:** Laravel Pint, Preset `laravel` ([`pint.json`](../pint.json)). Lokal: `vendor/bin/pint` (formatieren) bzw. `vendor/bin/pint --test` (nur prüfen).
- **Git-Hook** ([`.githooks/pre-commit`](../.githooks/pre-commit)): formatiert gestagte PHP-Dateien vor jedem Commit. `composer install` aktiviert ihn (`core.hooksPath`). Teilweise gestagte Dateien blockieren den Commit; überspringen mit `git commit --no-verify`.
- **VS Code:** formatiert PHP beim Speichern mit Pint (Erweiterung `open-southeners.laravel-pint`, wird beim Öffnen empfohlen).
- **Formatierungs-Commit:** steht in [`.git-blame-ignore-revs`](../.git-blame-ignore-revs), damit `git blame` die ursprünglichen Autoren zeigt. Lokal einmal: `git config blame.ignoreRevsFile .git-blame-ignore-revs`.

---

## Übersicht aller Tests

| Bereich | Datei | Fälle | Prüft |
|---|---|---:|---|
| Grundlagen | `TestInfrastructureTest` | 9 | Test-DB, Seeder, Factories, Szenario, Demo-Seeder, Seitenaufruf |
| Smoke | `Smoke/AllRoutesSmokeTest` | 6 | alle GET-Routen pro Rolle ohne Serverfehler, bekannte Fehler in `KNOWN_ISSUES` |
| Formulare | `FormComponentsTest` | 13 | Formular-Komponenten (Ersatz für `Form::`): old() vor Wert vor Model |
| Dashboard | `AdminDashboardTest` | 3 | Dashboard mit und ohne Aktion, Logbuch-Links |
| Tabellen | `DataTablesTest` | 2 | Routen- und Bestell-Liste: Sortieren nach Status (seit Laravel 13 500) |
| Dashboard | `TimeChartDataTest` | 5 | Zeitdiagramm: Zöpfe pro halbe Stunde |
| Ajax | `AjaxEndpointsTest` | 4 | JSON-Endpunkte ohne Parameter (früher 500) |
| Rückmeldungen | `FeedbackTest` | 3 | Seite `admin/changes`: Rückmeldung speichern und per Mail melden |
| Logbuch | `LogbookTest` | 3 | nur eigene Einträge, nur Formularfelder |
| Routenblatt | `RoutePdfTest` | 4 | `downloadPDF`: Google gefakt, Fehler von Google, fremde Route |
| Entfernt | `RemovedPagesTest` | 3 | Adressverwaltung `admin/addresses` entfernt |
| Wartung | `MaintenancePageTest` | 3 | öffentliche Wartungsseite (503) für `php artisan down` |
| Sicherheit | `Security/SideEffectRoutesTest` | 12 | Aktionen mit Seiteneffekt nur per POST, keine Request-Felder beim Abschliessen |
| Sicherheit | `Security/GroupsActionsTest` | 15 | Gruppen und Aktionen: Wechseln nur mit Zugehörigkeit, Bearbeiten nur Besitzer bzw. eigene Gruppe |
| Geocoding | `GeocoderTest` | 2 | `FakeGeocoder` statt Google in Tests und Dusk |
| Sicherheit | `Security/UserAccountsTest` | 18 | Profil und Benutzerverwaltung: keine Rechte-Ausweitung, keine fremden Konten |
| Sicherheit | `Security/ForeignIdsTest` | 13 | fremde Bestellungen, Routen, Aktionen → 403 (Policies) |
| JS | `tests/js/escape-html.test.js` | 4 | `escapeHtml` für Logbuch und Benachrichtigungen |
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

- `EXCLUDED`: OAuth, Demo-Logins, Pusher, signierte URL. Google Maps ist gefakt (`Http::fake`).
- `KNOWN_ISSUES`: bekannte Serverfehler mit Rolle und Grund (zurzeit leer). Der Test bleibt grün, solange genau diese auftreten; ein behobener Fehler macht ihn rot, bis der Eintrag entfernt ist.
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

### Fehler aus dem Smoke-Test

**[`AjaxEndpointsTest`](Feature/AjaxEndpointsTest.php)**

| Test | Prüft |
|---|---|
| `test_orders_datatable_without_filters_lists_own_orders` | Bestellungen-Tabelle ohne Filter `status`/`pickup` |
| `test_user_search_without_term_returns_placeholder` | Benutzersuche ohne Treffer |
| `test_icon_array_without_action_is_not_found` | Zahlen ohne Aktion → 404 statt 500 |
| `test_icon_array_with_action_returns_icons` | Zahlen einer Aktion |

**[`FeedbackTest`](Feature/FeedbackTest.php)**: Das Formular auf `admin/changes` zeigte auf `FeedbackController@store`, die Route fehlte (Seite 500).

| Test | Prüft |
|---|---|
| `test_groupleader_sees_changes_page` | Seite mit Formular |
| `test_groupleader_sends_feedback` | gespeichert mit eigenem `user_id` (nicht aus dem Request), Mail versendet |
| `test_leader_cannot_send_feedback` | Leiter wird abgewiesen |

**[`LogbookTest`](Feature/LogbookTest.php)**

| Test | Prüft |
|---|---|
| `test_actionleader_edits_own_entry` | Bearbeiten und Speichern |
| `test_foreign_entry_is_not_found` | Eintrag einer fremden Aktion → 404 (vorher 500), unverändert |
| `test_update_only_changes_form_fields` | `update()` übernahm `$request->all()` (z. B. `notifiable_id` einer fremden Aktion) |

**[`RoutePdfTest`](Feature/RoutePdfTest.php)**: `Helper::CreateRouteSequence()` und die Static-Maps-Karte laufen über Laravels `Http`-Client (vorher Guzzle bzw. `file_get_contents`, in Tests nicht abfangbar: der Smoke-Test schickte echte Anfragen an Google).

| Test | Prüft |
|---|---|
| `test_google_error_redirects_back_with_message` | Fehlerantwort von Google → zurück mit Meldung (vorher 500) |
| `test_existing_map_is_reused_without_google` | vorhandene Karte, kein Aufruf an Google |
| `test_foreign_route_is_forbidden` | fremde Route → 403 (`RoutePolicy::view`), kein Aufruf an Google |
| `test_overview_shows_error_message` | Meldung auf der Routen-Übersicht |

**[`RemovedPagesTest`](Feature/RemovedPagesTest.php)**: `admin/addresses` (Liste, Erfassen, Bearbeiten) war nirgends verlinkt, nutzte die alte City-Tabelle und gab überall 500. Entfernt; Adressen entstehen über Bestellungen und den Import.

### Wartung

**[`MaintenancePageTest`](Feature/MaintenancePageTest.php)**: die öffentliche Seite `errors/503`, die während `php artisan down` erscheint.

| Test | Prüft |
|---|---|
| `test_page_renders_without_vite_assets` | Seite rendert eigenständig (Logo eingebettet, keine `/build/`-Dateien) |
| `test_maintenance_mode_shows_page` | im Wartungsmodus kommt 503 mit dieser Seite |
| `test_custom_message_and_retry_after_are_shown` | eigene Nachricht und `Retry-After` als Neuladen-Intervall |

### Sicherheit

**[`Security/SideEffectRoutesTest`](Feature/Security/SideEffectRoutesTest.php)**: Bestellung übergeben/hinterlegt, Route "Alle Bestellungen abschliessen" und Aktion starten/abschliessen waren GET-Links. Ein Link oder `<img>` auf einer fremden Seite konnte sie für eingeloggte Benutzer auslösen. Jetzt POST mit CSRF-Token.

| Test | Prüft |
|---|---|
| `test_get_is_not_allowed_and_changes_nothing` (4) | GET gibt 405, nichts geändert |
| `test_leader_marks_order_as_delivered` / `_deposited` | Leiter auf eigener Route |
| `test_actionleader_delivers_all_orders_of_route` | Route abschliessen |
| `test_actionleader_completes_action` | Aktion abschliessen |
| `test_complete_ignores_request_fields` | `complete()` übernahm `$request->all()` (z. B. `group_id`, `APIKey`, `demo`) |
| `test_leader_route_page_uses_post_forms` / `test_route_overview_uses_post_form` | Knöpfe sind Formulare, keine Links |

**[`Security/GroupsActionsTest`](Feature/Security/GroupsActionsTest.php)**: `GroupPolicy` (`switchTo`, `manage`, `assignOwner`) und `ActionPolicy` (`switchTo`, `manage`). Vorher konnte jede registrierte Person in jede Gruppe und Aktion wechseln (danach Namen und Adressen sichtbar), jede Aktion ändern und löschen und jede Gruppe ändern (inkl. Besitzer übernehmen).

| Test | Prüft |
|---|---|
| `test_stranger_cannot_switch_into_foreign_group` / `_action` | Wechsel ohne Zugehörigkeit → 403, keine Mitgliedschaft angelegt |
| `test_member_switches_between_own_groups` / `test_member_switches_into_action_of_own_group` / `test_everybody_may_switch_to_global_group_and_action` | erlaubte Wechsel |
| `test_stranger_cannot_edit_foreign_group` / `test_actionleader_of_group_cannot_edit_group_he_does_not_own` | Gruppe nur durch Besitzer |
| `test_owner_edits_group` / `test_owner_cannot_hand_group_to_non_member` | Besitzer ändert Name und Besitzer (nur an Mitglieder), `global` nie aus dem Request |
| `test_group_create_ignores_global_and_foreign_owner` | Gruppe erfassen |
| `test_stranger_cannot_edit_or_delete_foreign_action` / `test_actionleader_of_other_group_cannot_edit_action` / `test_leader_of_own_group_cannot_edit_action` | Aktion nur Aktionsleiter+ der eigenen Gruppe |
| `test_actionleader_edits_action_of_own_group` / `test_action_create_stays_in_own_group` | nur Formularfelder; Gruppe, `global`, `demo`, Besitzer nie aus dem Request |

**[`Security/UserAccountsTest`](Feature/Security/UserAccountsTest.php)**: `UserPolicy` (`updateProfile`, `manage`, `assignRole`). Vorher konnte jeder eingeloggte Leiter sich über das Profil zum Administrator machen (`role_id` aus `$request->all()`), fremde Profile und Benutzer ändern (E-Mail, Passwort → Konto übernehmen) und Aktionsleiter konnten Administratoren anlegen bzw. hinzufügen.

| Test | Prüft |
|---|---|
| `test_leader_updates_own_profile` / `test_leader_changes_own_password_with_confirmation` | eigenes Profil |
| `test_profile_ignores_role_and_status` | Rolle, Gruppe, Aktion, Demo nie aus dem Profil |
| `test_foreign_profile_is_forbidden` | fremdes Profil → 403, unverändert |
| `test_groupleader_edits_leader_of_own_group` | Benutzer der eigenen Gruppe |
| `test_role_select_offers_only_allowed_roles` / `test_create_form_offers_only_allowed_roles` | Auswahl zeigt nur erlaubte Rollen |
| `test_nobody_but_admin_can_grant_admin` | Aktions-/Gruppenleiter machen sich nicht zum Admin |
| `test_actionleader_cannot_promote_to_groupleader` | Rolle höchstens die eigene |
| `test_actionleader_cannot_edit_groupleader` / `test_admin_cannot_be_edited_by_groupleader` | keine Benutzer mit mehr Rechten |
| `test_user_of_other_group_is_forbidden` | fremde Gruppe → 403 |
| `test_admin_grants_any_role` | Admin darf alles |
| `test_user_update_ignores_unknown_fields` | nur Formularfelder |
| `test_actionleader_cannot_create_admin` / `test_actionleader_creates_leader` | Benutzer erfassen |
| `test_add_cannot_grant_higher_role` / `test_add_with_allowed_role` | bestehenden Benutzer hinzufügen |

**[`Security/ForeignIdsTest`](Feature/Security/ForeignIdsTest.php)**: Policies `OrderPolicy::deliver`, `RoutePolicy::deliverAll`, `ActionPolicy::complete`. Regeln siehe [Fachliche Regeln](#fachliche-regeln-die-die-tests-festhalten).

| Test | Prüft |
|---|---|
| `test_foreign_objects_are_forbidden` (9) | fremde Gruppe bzw. fremde Route → 403, nichts geändert |
| `test_actionleader_marks_any_order_of_own_action` | Aktionsleiter darf alle Bestellungen der eigenen Aktion |
| `test_groupleader_completes_action_of_own_group` | Aktionen der eigenen Gruppe |
| `test_admin_may_act_on_any_group` / `test_admin_may_mark_any_order` | Admin darf alles |

**[`tests/js/escape-html.test.js`](js/escape-html.test.js)**: `resources/js/escape-html.js`. Logbuch-Liste (Dashboard) und Live-Benachrichtigungen setzten Benutzertext ungeprüft als HTML ein. Ausführen: `npm run test:js`.

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
- Bestellung übergeben/hinterlegt: Leiter nur auf der eigenen Route, Aktionsleiter und höher alle Bestellungen der eigenen aktuellen Aktion, Admin alle.
- Route "Alle Bestellungen abschliessen" und Routenblatt: Routen der eigenen aktuellen Aktion, Admin alle.
- Aktion starten/abschliessen: Aktionen der eigenen Gruppe (wie die Aktionen-Tabelle), Admin alle.
- Nicht erlaubter Zugriff auf fremde Objekte: 403.
- Gruppe wechseln: nur Gruppen mit Mitgliedschaft und die globale Gruppe; Aktion wechseln: nur Aktionen der aktuellen Gruppe und die globale Aktion. Admin: alle.
- Gruppe bearbeiten/löschen: Besitzer und Admin; neuer Besitzer muss Mitglied sein. Aktion bearbeiten/löschen: wie abschliessen. Aktion erfassen: nur in der eigenen Gruppe.
- Profil: nur das eigene; nur Benutzername, E-Mail, Passwort (mit Bestätigung), Avatar.
- Benutzerverwaltung: Nicht-Admins nur Benutzer der eigenen aktuellen Gruppe mit gleich vielen oder weniger Rechten; als Rolle (auch beim Erfassen und Hinzufügen) höchstens die eigene. Admin: alles.

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
