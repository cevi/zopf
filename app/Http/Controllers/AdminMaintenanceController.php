<?php

namespace App\Http\Controllers;

use App\Models\Help;
use Illuminate\Support\Facades\Artisan;

/**
 * Wartung für Admins ohne SSH-Zugang: Migrationen und Caches per Knopfdruck.
 * Aktionen nur per POST (CSRF-Schutz), destruktive Befehle (migrate:fresh, db:seed) werden bewusst nicht angeboten.
 */
class AdminMaintenanceController extends Controller
{
    public function index()
    {
        $title = 'Wartung';
        $help = Help::where('title', $title)->first();
        $pendingMigrations = $this->pendingMigrations();

        return view('admin.maintenance.index', compact('title', 'help', 'pendingMigrations'));
    }

    public function migrate()
    {
        Artisan::call('migrate', ['--force' => true]);

        return $this->back(Artisan::output());
    }

    /**
     * Ersetzt die frühere URL admin/run-deployment.
     */
    public function cache()
    {
        Artisan::call('config:cache');
        $output = Artisan::output();
        Artisan::call('view:cache');
        $output .= Artisan::output();

        return $this->back($output);
    }

    public function clear()
    {
        Artisan::call('optimize:clear');

        return $this->back(Artisan::output());
    }

    private function back(string $output)
    {
        return redirect()->route('admin.maintenance')->with('maintenance_output', $output);
    }

    /**
     * Namen der Migrationen, die noch nicht gelaufen sind.
     *
     * @return array<int, string>
     */
    private function pendingMigrations(): array
    {
        $migrator = app('migrator');
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));

        if (! $migrator->repositoryExists()) {
            return $files;
        }

        return array_values(array_diff($files, $migrator->getRepository()->getRan()));
    }
}
