<?php

namespace Gregoriohc\Seedable;

use Illuminate\Database\Seeder;

class SeedableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seedables = collect($this->seedables())->filter(function($seedable) {
            try {
                return in_array(\Gregoriohc\Seedable\IsSeedable::class, class_uses_recursive(app($seedable)));
            } catch (\Exception $e) {}
            return false;
        });

        foreach ($seedables as $seedable) {
            $this->command->line("<info>Seeding Seedable:</info> $seedable");
            app($seedable)->seed();
        }
    }

    private function seedables()
    {
        $seedables = [];

        if (config('seedable.autoload_enabled', true)) {
            foreach (config('seedable.autoload_namespaces', []) as $namespace) {
                $namespace = trim($namespace, '\\');
                $seedables = array_merge($seedables, \Gregoriohc\Seedable\ClassFinder::getClassesInNamespace($namespace));
            }
        }

        foreach (config('seedable.seedables', []) as $seedable) {
            if (($key = array_search($seedable, $seedables)) !== false) {
                unset($seedables[$key]);
            }
            $seedables[] = $seedable;
        }

        return $seedables;
    }
}
