# Tests

Übersicht über die automatisierten Tests des Zopfaktions-Tools: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 1. November 2026: **9 Testfälle in 1 Datei**, Laufzeit rund 1 Sekunde.
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
