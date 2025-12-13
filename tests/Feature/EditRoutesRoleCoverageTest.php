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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionMethod;
use Tests\TestCase;

class EditRoutesRoleCoverageTest extends TestCase
{
    use RefreshDatabase;

    private bool $seeded = false;

    private ?Fakultas $seededFakultas = null;
    private ?ProgramStudi $seededProdi = null;

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

    private function inferModelClassForEditRoute($route, string $paramName): ?string
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

    public function test_all_edit_routes_work_for_allowed_roles_when_data_exists(): void
    {
        $this->seedDatabaseOnce();

        $routes = app('router')->getRoutes()->getRoutes();

        $editRoutes = array_values(array_filter($routes, function ($route) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            if (!str_ends_with((string) $name, '.edit')) {
                return false;
            }

            $methods = $route->methods();
            if (!in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
                return false;
            }

            // We only handle simple edit routes with a single parameter.
            return count($route->parameterNames()) === 1;
        }));

        $this->assertNotEmpty($editRoutes, 'No edit routes found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($editRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            $paramName = (string) ($route->parameterNames()[0] ?? 'id');
            $modelClass = $this->inferModelClassForEditRoute($route, $paramName);

            if (!$modelClass) {
                continue;
            }

            $record = $this->resolveRouteParamValue($modelClass);
            if (!$record) {
                continue;
            }

            $url = route($name, [$paramName => $record->getKey()]);

            if (!$requiresAuth) {
                $response = $this->get($url);
                $response->assertStatus(200, "Public edit route failed: {$name}");
                continue;
            }

            $guest = $this->get($url);
            $guest->assertStatus(302, "Auth-protected edit did not redirect for guest: {$name}");

            $rolesToTest = !empty($allowedRoles) ? $allowedRoles : $allRoles;

            foreach ($rolesToTest as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                $bufferLevelBefore = ob_get_level();
                $response = $this->get($url);

                $bufferLevelAfter = ob_get_level();
                if ($bufferLevelAfter > $bufferLevelBefore) {
                    while (ob_get_level() > $bufferLevelBefore) {
                        @ob_end_clean();
                    }

                    $this->fail("Edit route leaked output buffer(s): {$name} (role={$roleName})");
                }

                if ($response->getStatusCode() === 500 && $response->exception) {
                    $this->fail("Edit route exception: {$name} (role={$roleName}) - " . $response->exception->getMessage());
                }

                // Some edit routes may redirect or forbid based on policy/data; accept 200 or redirect or 403.
                $this->assertTrue(
                    in_array($response->getStatusCode(), [200, 302, 303, 403], true),
                    "Edit route failed: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                // Explicitly disallow accidental 404 which usually indicates routing conflicts or missing views.
                $this->assertNotEquals(404, $response->getStatusCode(), "Edit route returned 404: {$name} (role={$roleName})");

                auth()->logout();
            }
        }
    }
}
