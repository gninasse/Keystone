<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

class GenerateModulePermissions extends Command
{
    protected $signature = 'permissions:generate {module?} {--sync}';
    protected $description = 'Générer les permissions pour les modules';

    public function handle()
    {
        $moduleName = $this->argument('module');
        $sync = $this->option('sync');

        if ($moduleName) {
            $this->generateForModule($moduleName, $sync);
        } else {
            $this->generateForAllModules($sync);
        }

        $this->info('Permissions générées avec succès!');
    }

    protected function generateForAllModules($sync)
    {
        $modules = Module::all();

        foreach ($modules as $module) {
            $this->generateForModule($module->getName(), $sync);
        }
    }

    protected function generateForModule($moduleName, $sync)
    {
        $module = Module::find($moduleName);

        if (!$module) {
            $this->error("Le module {$moduleName} n'existe pas.");
            return;
        }

        $this->info("Génération des permissions pour le module: {$moduleName}");

        // Lire le fichier de configuration des permissions
        $configPath = $module->getPath() . '/config/permissions.php';

        if (!file_exists($configPath)) {
            $this->warn("Aucun fichier permissions.php trouvé dans {$moduleName}/config/");
            return;
        }

        $permissions = require $configPath;

        if ($sync) {
            // Supprimer les anciennes permissions
            Permission::where('module', strtolower($moduleName))->delete();
            $this->warn("Anciennes permissions supprimées pour {$moduleName}");
        }

        foreach ($permissions as $permission) {
            $permissionModel = Permission::firstOrCreate(
                ['name' => $permission],
                [
                    'module' => strtolower($moduleName),
                    'guard_name' => 'web'
                ]
            );

            if ($permissionModel->wasRecentlyCreated) {
                $this->line("  ✓ {$permission}");
            } else {
                $this->line("  - {$permission} (existe déjà)");
            }
        }
    }
}
