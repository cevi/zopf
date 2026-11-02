<?php

namespace Tests\Feature\Smoke;

use App\Models\BakeryProgress;
use App\Models\Feedback;
use App\Models\Help;
use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Ruft jede GET-Route der App mit jeder Rolle auf und erwartet keinen Serverfehler.
 * Neue Routen werden automatisch mitgetestet; fehlt ein Wert für einen Routenparameter,
 * schlägt der Test fehl und routeParameters() muss ergänzt werden.
 */
class AllRoutesSmokeTest extends TestCase
{
    /**
     * Routen, die hier bewusst nicht aufgerufen werden, jeweils mit Grund.
     */
    private const EXCLUDED = [
        'email/verify/{id}/{hash}' => 'signierte URL',
        'login/hitobito' => 'OAuth-Redirect zu Hitobito',
        'login/hitobito/callback' => 'OAuth-Callback, braucht Hitobito',
        'loginChef' => 'Demo-Login, braucht DemoActionSeeder',
        'loginLeiter' => 'Demo-Login, braucht DemoActionSeeder',
        'broadcasting/auth' => 'Pusher',
    ];

    /**
     * Bekannte, noch nicht behobene Serverfehler. Der Test bleibt grün, solange genau diese auftreten.
     * Ist einer behoben, schlägt der Test fehl, bis der Eintrag hier entfernt ist.
     */
    private const KNOWN_ISSUES = [
        // 'route/{uri}' => ['roles' => ['rolle'], 'reason' => 'Warum es (noch) kracht'],
    ];

    public static function roles(): array
    {
        return [
            'gast' => ['gast'],
            'leader' => ['leader'],
            'leaderWithoutRoute' => ['leaderWithoutRoute'],
            'actionleader' => ['actionleader'],
            'groupleader' => ['groupleader'],
            'admin' => ['admin'],
        ];
    }

    #[DataProvider('roles')]
    public function test_no_get_route_returns_a_server_error(string $role)
    {
        // Google Maps (Routenblatt) nie wirklich aufrufen
        Http::fake(['maps.googleapis.com/*' => Http::response(['status' => 'REQUEST_DENIED', 'routes' => []])]);
        $s = ActionScenario::create();
        $user = $role === 'gast' ? null : ($role === 'admin' ? ActionScenario::admin() : $s->users()[$role]);
        $values = $this->routeParameters($s);
        $errors = [];

        foreach ($this->getRoutes() as $route) {
            foreach ($this->buildUris($route, $values) as $uri) {
                $error = $this->visit($uri, $user);
                if ($error) {
                    $errors[$route->uri()][] = "$uri → $error";
                }
            }
        }

        $known = array_keys(array_filter(self::KNOWN_ISSUES, fn (array $issue) => in_array($role, $issue['roles'])));
        $unexpected = array_merge([], ...array_values(array_diff_key($errors, array_flip($known))));
        $fixed = array_values(array_diff($known, array_keys($errors)));

        $this->assertSame([], $unexpected, "Neue Serverfehler als $role:\n".implode("\n", $unexpected));
        $this->assertSame([], $fixed, "Behoben als $role? Bitte aus KNOWN_ISSUES entfernen:\n".implode("\n", $fixed));
    }

    /**
     * @return string|null Fehlerbeschreibung bei Status >= 500
     */
    private function visit(string $uri, ?User $user): ?string
    {
        // Frische Instanz wie bei einem echten Request, sonst hängen geladene Relationen vom vorherigen Request am User.
        if ($user) {
            $this->actingAs($user->fresh());
        }
        $response = $this->get($uri);

        // Jeder Request startet ohne Login und Session des vorherigen.
        $this->app['auth']->forgetGuards();
        $this->flushSession();

        if ($response->getStatusCode() < 500) {
            return null;
        }
        $exception = $response->exception;

        return $response->getStatusCode().($exception
            ? ' '.$exception->getMessage().' ('.basename($exception->getFile()).':'.$exception->getLine().')'
            : '');
    }

    /**
     * @return Route[]
     */
    private function getRoutes(): array
    {
        return collect(Router::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('GET', $route->methods()))
            ->reject(fn (Route $route) => str_starts_with($route->getActionName(), 'Laravel\\')
                || str_starts_with($route->uri(), '_ignition')
                || str_starts_with($route->uri(), 'telescope')
                || str_starts_with($route->uri(), 'storage/')
                || $route->uri() === 'up'
                || array_key_exists($route->uri(), self::EXCLUDED))
            ->values()
            ->all();
    }

    /**
     * Jede Route mit den Standardwerten und zusätzlich mit Randfall-Objekten aus dem Szenario.
     *
     * @return string[]
     */
    private function buildUris(Route $route, array $values): array
    {
        $edgeCases = [
            // Route in Planung, noch niemandem zugewiesen
            'route' => $values['plannedRoute'],
            'id' => str_starts_with($route->uri(), 'routes/order/') ? $values['id'] : $values['plannedRoute'],
        ] + $values;

        return array_values(array_unique([
            $this->buildUri($route, $values),
            $this->buildUri($route, $edgeCases),
        ]));
    }

    private function buildUri(Route $route, array $values): string
    {
        // {id} ist je nach Route eine Bestellung oder eine Route.
        if (str_starts_with($route->uri(), 'routes/order/')) {
            $values['id'] = $values['order'];
        }

        return '/'.ltrim(preg_replace_callback('/\{(\w+)\??\}/', function ($match) use ($values, $route) {
            $this->assertArrayHasKey(
                $match[1],
                $values,
                "Kein Testwert für Parameter {{$match[1]}} der Route {$route->uri()}. Bitte in routeParameters() ergänzen."
            );

            return $values[$match[1]];
        }, $route->uri()), '/');
    }

    /**
     * Werte für Routenparameter, jeweils so, wie die App sie in Links erzeugt.
     */
    private function routeParameters(ActionScenario $s): array
    {
        $logbook = (string) Str::uuid();
        DB::table('notifications')->insert([
            'id' => $logbook,
            'type' => 'App\Notifications\CreateLogEntry',
            'notifiable_type' => get_class($s->action),
            'notifiable_id' => $s->action->id,
            'quantity' => 0,
            'when' => '10:00:00',
            'user' => $s->leader->username,
            'content' => 'Testeintrag',
        ]);

        $progress = BakeryProgress::create([
            'action_id' => $s->action->id,
            'user_id' => $s->actionleader->id,
            'when' => '08:00:00',
            'raw_material' => 0, 'dough' => 0, 'braided' => 0, 'baked' => 0, 'delivered' => 0, 'total' => 0,
        ]);

        return [
            'action' => $s->action->id,
            'address' => $s->openOrder->address_id,
            'feedback' => Feedback::create(['user_id' => $s->leader->id, 'feedback' => 'Test'])->id,
            'group' => $s->group->id,
            'help' => Help::create(['title' => 'Test', 'content' => 'Test'])->id,
            'logbook' => $logbook,
            'order' => $s->openOrder->id,
            'progress' => $progress->id,
            'route' => $s->route->id,
            'plannedRoute' => $s->plannedRoute->id,
            'id' => $s->route->id,
            'user' => $s->leader->id,
            'token' => 'invalid-token',
        ];
    }
}
