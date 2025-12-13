<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionMethod;
use Tests\TestCase;

class UpdateDestroyRoutesRoleCoverageTest extends TestCase
{
    use RefreshDatabase;

    private bool $seeded = false;

    private ?Fakultas $seededFakultas = null;
    private ?ProgramStudi $seededProdi = null;

    protected function setUp(): void
    {
        parent::setUp();

        // This suite is about role/controller stability; CSRF is orthogonal.
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function ensureRole(string $name): Role
    {
        return Role::firstOrCreate(
            ['name' => $name],
            ['display_name' => Str::of($name)->replace('_', ' ')->title()->toString()]
        );
    }

    private function seedDatabaseOnce(): void
    {
        if ($this->seeded) {
            return;
        }

        $this->seed();
        $this->seeded = true;
    }

    private function seedFakultasProdi(): array
    {
        if ($this->seededFakultas && $this->seededProdi) {
            return [$this->seededFakultas, $this->seededProdi];
        }

        $this->seededFakultas = Fakultas::query()->first() ?? Fakultas::create([
            'kode_fakultas' => 'FK' . Str::upper(Str::random(6)),
            'nama_fakultas' => 'Fakultas Test',
            'singkatan' => 'FT',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        $this->seededProdi = ProgramStudi::query()->first() ?? ProgramStudi::create([
            'fakultas_id' => $this->seededFakultas->id,
            'kode_prodi' => 'PR' . Str::upper(Str::random(6)),
            'nama_prodi' => 'Prodi Test',
            'jenjang' => 'S1',
            'kaprodi' => null,
            'akreditasi' => 'A',
        ]);

        return [$this->seededFakultas, $this->seededProdi];
    }

    private function createUserForRole(string $roleName): User
    {
        $role = $this->ensureRole($roleName);

        if ($roleName === Role::ADMIN_FAKULTAS) {
            [$fakultas] = $this->seedFakultasProdi();
            return User::factory()->create([
                'role_id' => $role->id,
                'fakultas_id' => $fakultas->id,
            ]);
        }

        if ($roleName === Role::ADMIN_PRODI) {
            [$fakultas, $prodi] = $this->seedFakultasProdi();
            return User::factory()->create([
                'role_id' => $role->id,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
            ]);
        }

        if ($roleName === Role::DOSEN) {
            [$fakultas, $prodi] = $this->seedFakultasProdi();

            $user = User::factory()->create([
                'role_id' => $role->id,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
            ]);

            $dosen = Dosen::query()->first() ?? Dosen::create([
                'program_studi_id' => $prodi->id,
                'nip' => 'NIP' . Str::random(8),
                'nidn' => null,
                'nama_dosen' => 'Dosen Test',
                'jenis_kelamin' => 'L',
                'email' => Str::random(6) . '@example.test',
                'status' => 'Aktif',
            ]);

            if (Schema::hasColumn('dosen', 'user_id') && empty($dosen->user_id)) {
                $dosen->user_id = $user->id;
                $dosen->save();
            }

            if (Schema::hasColumn('users', 'dosen_id')) {
                $user->dosen_id = $dosen->id;
                $user->save();
            }

            return $user;
        }

        if ($roleName === Role::MAHASISWA) {
            [$fakultas, $prodi] = $this->seedFakultasProdi();

            $mahasiswa = Mahasiswa::query()->first() ?? Mahasiswa::create([
                'program_studi_id' => $prodi->id,
                'nim' => 'MHS' . Str::random(8),
                'nama_mahasiswa' => 'Mahasiswa Test',
                'jenis_kelamin' => 'L',
                'email' => Str::random(6) . '@example.test',
                'tahun_masuk' => '2025',
                'semester' => 1,
                'ipk' => 0.00,
                'status' => 'Aktif',
            ]);

            return User::factory()->create([
                'role_id' => $role->id,
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
                'mahasiswa_id' => $mahasiswa->id,
            ]);
        }

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function parseRoleMiddleware(array $middlewares): array
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware) && str_starts_with($middleware, 'role:')) {
                $csv = substr($middleware, strlen('role:'));
                return array_values(array_filter(array_map('trim', explode(',', $csv))));
            }
        }

        return [];
    }

    private function inferModelClassForRoute($route, string $paramName): ?string
    {
        $action = (string) $route->getActionName();
        if ($action === 'Closure') {
            return null;
        }

        if (!str_contains($action, '@')) {
            return null;
        }

        [$controllerClass, $method] = explode('@', $action, 2);
        if (!class_exists($controllerClass) || !method_exists($controllerClass, $method)) {
            return null;
        }

        $ref = new ReflectionMethod($controllerClass, $method);
        foreach ($ref->getParameters() as $p) {
            $type = $p->getType();
            if (!$type || $type->isBuiltin()) {
                continue;
            }

            $typeName = $type->getName();
            if (is_a($typeName, Model::class, true)) {
                return $typeName;
            }
        }

        $guessed = 'App\\Models\\' . Str::studly($paramName);
        return class_exists($guessed) ? $guessed : null;
    }

    private function resolveRouteParamValue(string $modelClass): ?Model
    {
        if (!is_a($modelClass, Model::class, true)) {
            return null;
        }

        $record = $modelClass::query()->first();
        return $record instanceof Model ? $record : null;
    }

    private function assertNo500($response, string $name, string $roleName): void
    {
        if ($response->getStatusCode() === 500 && $response->exception) {
            $this->fail("Route exception: {$name} (role={$roleName}) - " . $response->exception->getMessage());
        }
    }

    public function test_all_update_routes_are_stable_for_allowed_roles(): void
    {
        $this->seedDatabaseOnce();

        $routes = app('router')->getRoutes()->getRoutes();

        $updateRoutes = array_values(array_filter($routes, function ($route) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            if (!str_ends_with((string) $name, '.update')) {
                return false;
            }

            $methods = $route->methods();
            if (!in_array('PUT', $methods, true) && !in_array('PATCH', $methods, true)) {
                return false;
            }

            return count($route->parameterNames()) === 1;
        }));

        $this->assertNotEmpty($updateRoutes, 'No update routes found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($updateRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            $paramName = (string) ($route->parameterNames()[0] ?? 'id');
            $modelClass = $this->inferModelClassForRoute($route, $paramName);
            if (!$modelClass) {
                continue;
            }

            $record = $this->resolveRouteParamValue($modelClass);
            if (!$record) {
                continue;
            }

            $url = route($name, [$paramName => $record->getKey()]);

            $methods = $route->methods();
            $usePatch = in_array('PATCH', $methods, true);

            if ($requiresAuth) {
                $guestResponse = $usePatch ? $this->patch($url, []) : $this->put($url, []);
                $this->assertTrue(in_array($guestResponse->getStatusCode(), [302, 303], true), "Guest not redirected on update: {$name}");
            }

            foreach ($allRoles as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                if (!empty($allowedRoles) && !in_array($roleName, $allowedRoles, true)) {
                    DB::beginTransaction();
                    try {
                        $blocked = $usePatch ? $this->patch($url, []) : $this->put($url, []);
                    } finally {
                        DB::rollBack();
                    }
                    $this->assertNo500($blocked, $name, $roleName);
                    $this->assertTrue(in_array($blocked->getStatusCode(), [403, 302, 303], true), "Disallowed role not blocked on update: {$name} (role={$roleName}, status={$blocked->getStatusCode()})");
                    auth()->logout();
                    continue;
                }

                DB::beginTransaction();
                try {
                    $response = $usePatch
                        ? $this->from('/')->patch($url, [])
                        : $this->from('/')->put($url, []);
                } finally {
                    DB::rollBack();
                }

                $this->assertNo500($response, $name, $roleName);

                $this->assertNotEquals(404, $response->getStatusCode(), "Update route returned 404: {$name} (role={$roleName})");

                // Most controllers will fail validation and redirect; accept that.
                $this->assertTrue(
                    in_array($response->getStatusCode(), [302, 303, 403, 422], true),
                    "Update route unexpected status: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                auth()->logout();
            }
        }
    }

    public function test_all_destroy_routes_are_stable_for_allowed_roles(): void
    {
        $this->seedDatabaseOnce();

        $routes = app('router')->getRoutes()->getRoutes();

        $destroyRoutes = array_values(array_filter($routes, function ($route) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            if (!str_ends_with((string) $name, '.destroy')) {
                return false;
            }

            $methods = $route->methods();
            if (!in_array('DELETE', $methods, true)) {
                return false;
            }

            return count($route->parameterNames()) === 1;
        }));

        $this->assertNotEmpty($destroyRoutes, 'No destroy routes found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($destroyRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            $paramName = (string) ($route->parameterNames()[0] ?? 'id');
            $modelClass = $this->inferModelClassForRoute($route, $paramName);
            if (!$modelClass) {
                continue;
            }

            $record = $this->resolveRouteParamValue($modelClass);
            if (!$record) {
                continue;
            }

            $url = route($name, [$paramName => $record->getKey()]);

            if ($requiresAuth) {
                $guestResponse = $this->delete($url);
                $this->assertTrue(in_array($guestResponse->getStatusCode(), [302, 303], true), "Guest not redirected on destroy: {$name}");
            }

            foreach ($allRoles as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                if (!empty($allowedRoles) && !in_array($roleName, $allowedRoles, true)) {
                    DB::beginTransaction();
                    try {
                        $blocked = $this->delete($url);
                    } finally {
                        DB::rollBack();
                    }
                    $this->assertNo500($blocked, $name, $roleName);
                    $this->assertTrue(in_array($blocked->getStatusCode(), [403, 302, 303], true), "Disallowed role not blocked on destroy: {$name} (role={$roleName}, status={$blocked->getStatusCode()})");
                    auth()->logout();
                    continue;
                }

                DB::beginTransaction();
                try {
                    $response = $this->from('/')->delete($url);
                } finally {
                    DB::rollBack();
                }
                $this->assertNo500($response, $name, $roleName);
                $this->assertNotEquals(404, $response->getStatusCode(), "Destroy route returned 404: {$name} (role={$roleName})");

                // Most controllers will redirect after delete; accept 302/303.
                $this->assertTrue(
                    in_array($response->getStatusCode(), [302, 303, 403], true),
                    "Destroy route unexpected status: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                auth()->logout();
            }
        }
    }
}
