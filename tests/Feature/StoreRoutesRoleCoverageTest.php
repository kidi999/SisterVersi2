<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreRoutesRoleCoverageTest extends TestCase
{
    use RefreshDatabase;

    private bool $seeded = false;

    private ?Fakultas $seededFakultas = null;
    private ?ProgramStudi $seededProdi = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Focus on stability & authorization; CSRF is tested elsewhere.
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

    private function assertNo500($response, string $name, string $roleName): void
    {
        if ($response->getStatusCode() === 500 && $response->exception) {
            $this->fail("Route exception: {$name} (role={$roleName}) - " . $response->exception->getMessage());
        }
    }

    public function test_all_store_routes_are_stable_for_allowed_roles(): void
    {
        $this->seedDatabaseOnce();

        $routes = app('router')->getRoutes()->getRoutes();

        $storeRoutes = array_values(array_filter($routes, function ($route) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            if (!str_ends_with((string) $name, '.store')) {
                return false;
            }

            $methods = $route->methods();
            if (!in_array('POST', $methods, true)) {
                return false;
            }

            // Store routes should not require route params.
            return count($route->parameterNames()) === 0;
        }));

        $this->assertNotEmpty($storeRoutes, 'No store routes found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($storeRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            $url = route($name);

            if ($requiresAuth) {
                $guestResponse = $this->post($url, []);
                $this->assertTrue(in_array($guestResponse->getStatusCode(), [302, 303], true), "Guest not redirected on store: {$name}");
            }

            foreach ($allRoles as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                DB::beginTransaction();
                try {
                    $response = $this->from('/')->post($url, []);
                } finally {
                    DB::rollBack();
                }

                $this->assertNo500($response, $name, $roleName);

                // 404 usually indicates route mismatch or unexpected guards.
                $this->assertNotEquals(404, $response->getStatusCode(), "Store route returned 404: {$name} (role={$roleName})");

                if (!empty($allowedRoles) && !in_array($roleName, $allowedRoles, true)) {
                    // Disallowed roles should be blocked (403) or redirected.
                    $this->assertTrue(
                        in_array($response->getStatusCode(), [403, 302, 303], true),
                        "Disallowed role not blocked on store: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                    );

                    auth()->logout();
                    continue;
                }

                // Allowed roles: we mainly expect validation redirect (302/303) or 422, or sometimes a success redirect.
                $this->assertTrue(
                    in_array($response->getStatusCode(), [200, 201, 302, 303, 403, 422], true),
                    "Store route unexpected status: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                auth()->logout();
            }
        }
    }
}
