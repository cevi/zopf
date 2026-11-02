<?php

namespace Tests\Feature;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Öffentliche Wartungsseite (errors/503) für `php artisan down`: eigenständig, ohne Vite und Datenbank.
 */
class MaintenancePageTest extends TestCase
{
    public function test_page_renders_without_vite_assets(): void
    {
        $html = view('errors.503')->render();

        $this->assertStringContainsString('Wir sind gleich wieder da', $html);
        $this->assertStringContainsString('data:image/svg+xml;base64,', $html);
        $this->assertStringNotContainsString('/build/', $html);
    }

    public function test_maintenance_mode_shows_page(): void
    {
        $this->app->maintenanceMode()->activate([]);

        try {
            $this->get('/')
                ->assertStatus(503)
                ->assertSee('Wir sind gleich wieder da');
        } finally {
            $this->app->maintenanceMode()->deactivate();
        }
    }

    public function test_custom_message_and_retry_after_are_shown(): void
    {
        $exception = new HttpException(503, 'Update auf Version 2.0', null, ['Retry-After' => 120]);

        $html = view('errors.503', ['exception' => $exception])->render();

        $this->assertStringContainsString('Update auf Version 2.0', $html);
        $this->assertStringContainsString('content="120"', $html);
    }
}
