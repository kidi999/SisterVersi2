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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexRoutesRoleCoverageTest extends TestCase
{
    use RefreshDatabase;

    private ?Fakultas $seededFakultas = null;
    private ?ProgramStudi $seededProdi = null;

    private function ensureRole(string $name): Role
    {
        return Role::firstOrCreate(
            ['name' => $name],
            ['display_name' => Str::of($name)->replace('_', ' ')->title()->toString()]
        );
    }

    private function seedFakultasProdi(): array
    {
        if ($this->seededFakultas && $this->seededProdi) {
            return [$this->seededFakultas, $this->seededProdi];
        }

        $this->seededFakultas = Fakultas::create([
            'kode_fakultas' => 'FK' . Str::upper(Str::random(6)),
            'nama_fakultas' => 'Fakultas Test',
            'singkatan' => 'FT',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        $this->seededProdi = ProgramStudi::create([
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

            $dosen = Dosen::create([
                'program_studi_id' => $prodi->id,
                'nip' => 'NIP' . Str::random(8),
                'nidn' => null,
                'nama_dosen' => 'Dosen Test',
                'jenis_kelamin' => 'L',
                'email' => Str::random(6) . '@example.test',
                'status' => 'Aktif',
            ]);

            // Link dosen to user when columns exist.
            if (Schema::hasColumn('dosen', 'user_id')) {
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

            $mahasiswa = Mahasiswa::create([
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

    public function test_all_index_routes_work_for_allowed_roles(): void
    {
        $routes = app('router')->getRoutes()->getRoutes();

        $indexRoutes = array_values(array_filter($routes, function ($route) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            // Target index-like pages only
            $isIndex = str_ends_with($name, '.index') || $name === 'dashboard';
            if (!$isIndex) {
                return false;
            }

            // Only GET/HEAD
            $methods = $route->methods();
            if (!in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
                return false;
            }

            // Avoid routes requiring params
            return count($route->parameterNames()) === 0;
        }));

        $this->assertNotEmpty($indexRoutes, 'No index routes found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($indexRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            if (!$requiresAuth) {
                $response = $this->get(route($name));
                $response->assertStatus(200, "Public index route failed: {$name}");
                continue;
            }

            // Auth required: guest must redirect to login
            $guest = $this->get(route($name));
            $guest->assertStatus(302, "Auth-protected index did not redirect for guest: {$name}");

            $rolesToTest = !empty($allowedRoles) ? $allowedRoles : $allRoles;

            foreach ($rolesToTest as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                $bufferLevelBefore = ob_get_level();
                $response = $this->get(route($name));

                $bufferLevelAfter = ob_get_level();
                if ($bufferLevelAfter > $bufferLevelBefore) {
                    // Clean any leaked buffers so the rest of the suite isn't affected.
                    while (ob_get_level() > $bufferLevelBefore) {
                        @ob_end_clean();
                    }

                    $this->fail("Index route leaked output buffer(s): {$name} (role={$roleName})");
                }

                if ($response->getStatusCode() === 500 && $response->exception) {
                    $this->fail("Index route exception: {$name} (role={$roleName}) - " . $response->exception->getMessage());
                }

                // Some index routes may redirect based on data presence; accept 200 or redirect.
                $this->assertTrue(
                    in_array($response->getStatusCode(), [200, 302, 303], true),
                    "Index route failed: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                auth()->logout();
            }
        }
    }
}
