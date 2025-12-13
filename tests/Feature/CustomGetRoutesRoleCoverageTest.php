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

class CustomGetRoutesRoleCoverageTest extends TestCase
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

    public function test_all_custom_get_routes_without_params_are_stable_per_role(): void
    {
        $this->seedDatabaseOnce();

        $routes = app('router')->getRoutes()->getRoutes();

        $excludedSuffixes = [
            '.index',
            '.create',
            '.store',
            '.show',
            '.edit',
            '.update',
            '.destroy',
            '.export.excel',
            '.export.pdf',
            '.export',
            '.exportExcel',
            '.exportPdf',
            'exportExcel',
            'exportPdf',
        ];

        $customGetRoutes = array_values(array_filter($routes, function ($route) use ($excludedSuffixes) {
            $name = $route->getName();
            if (!$name) {
                return false;
            }

            $name = (string) $name;

            $methods = $route->methods();
            if (!in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
                return false;
            }

            if (count($route->parameterNames()) !== 0) {
                return false;
            }

            foreach ($excludedSuffixes as $suffix) {
                if (str_ends_with($name, $suffix)) {
                    return false;
                }
            }

            // Skip Laravel internal tooling routes if any.
            if (str_starts_with($name, 'ignition.') || str_starts_with($name, 'telescope.')) {
                return false;
            }

            return true;
        }));

        $this->assertNotEmpty($customGetRoutes, 'No custom GET routes without params found to audit.');

        $allRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN_UNIVERSITAS,
            Role::ADMIN_FAKULTAS,
            Role::ADMIN_PRODI,
            Role::DOSEN,
            Role::MAHASISWA,
        ];

        foreach ($customGetRoutes as $route) {
            $name = (string) $route->getName();
            $middlewares = $route->gatherMiddleware();

            $requiresAuth = in_array('auth', $middlewares, true);
            $allowedRoles = $this->parseRoleMiddleware($middlewares);

            $url = route($name);

            if (!$requiresAuth) {
                $response = $this->get($url);
                if ($response->getStatusCode() === 500 && $response->exception) {
                    $this->fail("Custom GET exception: {$name} - " . $response->exception->getMessage());
                }
                $this->assertNotEquals(404, $response->getStatusCode(), "Custom GET returned 404: {$name}");
                $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 303], true), "Custom GET unexpected status: {$name} ({$response->getStatusCode()})");
                continue;
            }

            $guest = $this->get($url);
            $this->assertTrue(in_array($guest->getStatusCode(), [302, 303], true), "Custom GET auth route did not redirect guest: {$name}");

            $rolesToTest = !empty($allowedRoles) ? $allowedRoles : $allRoles;

            foreach ($rolesToTest as $roleName) {
                $user = $this->createUserForRole($roleName);
                $this->actingAs($user);

                $response = $this->get($url);

                if ($response->getStatusCode() === 500 && $response->exception) {
                    $this->fail("Custom GET exception: {$name} (role={$roleName}) - " . $response->exception->getMessage());
                }

                $this->assertNotEquals(404, $response->getStatusCode(), "Custom GET returned 404: {$name} (role={$roleName})");
                $this->assertTrue(
                    in_array($response->getStatusCode(), [200, 302, 303, 403], true),
                    "Custom GET unexpected status: {$name} (role={$roleName}, status={$response->getStatusCode()})"
                );

                auth()->logout();
            }
        }
    }
}
