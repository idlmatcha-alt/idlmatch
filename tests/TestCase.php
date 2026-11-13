<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prevent interactive migrate:fresh confirmation in environments that resolve as production.
     *
     * @return array<string, mixed>
     */
    protected function migrateFreshUsing()
    {
        $defaultConnection = (string) config('database.default', '');
        $databaseName = (string) config("database.connections.{$defaultConnection}.database", '');
        $safeSqliteDatabases = [
            ':memory:',
            database_path('database.sqlite'),
        ];

        $isSafeTestingDatabase = $defaultConnection === 'sqlite'
            && in_array($databaseName, $safeSqliteDatabases, true);

        if (!$isSafeTestingDatabase) {
            throw new RuntimeException(
                sprintf(
                    "Unsafe test database configuration detected (connection=%s, database=%s). Refusing to run migrate:fresh.",
                    $defaultConnection !== '' ? $defaultConnection : 'unknown',
                    $databaseName !== '' ? $databaseName : 'unknown'
                )
            );
        }

        $seeder = property_exists($this, 'seeder') ? $this->seeder : false;
        $seed = property_exists($this, 'seed') ? $this->seed : false;
        $dropViews = property_exists($this, 'dropViews') ? $this->dropViews : false;
        $dropTypes = property_exists($this, 'dropTypes') ? $this->dropTypes : false;

        return array_merge(
            [
                '--drop-views' => $dropViews,
                '--drop-types' => $dropTypes,
                '--force' => true,
            ],
            $seeder ? ['--seeder' => $seeder] : ['--seed' => $seed]
        );
    }
}
