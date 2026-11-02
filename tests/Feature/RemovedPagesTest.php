<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\ActionScenario;
use Tests\TestCase;

/**
 * Entfernte Seiten: Adressverwaltung (admin/addresses) war nirgends verlinkt und nutzte noch die alte City-Tabelle.
 * Adressen werden über Bestellungen und den Import gepflegt.
 */
class RemovedPagesTest extends TestCase
{
    public static function urls(): array
    {
        return [
            'Liste' => ['/admin/addresses'],
            'Erfassen' => ['/admin/addresses/create'],
            'Bearbeiten' => ['/admin/addresses/{address}/edit'],
        ];
    }

    #[DataProvider('urls')]
    public function test_address_pages_are_removed(string $url): void
    {
        // Echte Adresse, damit der 404 nicht nur von findOrFail kommt.
        $address = ActionScenario::create()->openOrder->address_id;

        $this->actingAs(ActionScenario::admin())->get(str_replace('{address}', $address, $url))->assertNotFound();
    }
}
