<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->company = Company::factory()->create([
        'name'       => 'Original Company',
        'rfc'        => 'TEST123456TS1',
        'phone'      => '1234567890',
        'address'    => 'Original Address',
        'tax_regime' => 'moral',
    ]);
    $this->clientUser = User::factory()->create([
        'name'     => 'Cliente Original',
        'email'    => 'original@client.com',
        'phone'    => '1234567890',
        'role'     => 'client',
        'password' => Hash::make('password123'),
    ]);
    $this->clientUser->companies()->attach($this->company);
});

/* ── COMPANY TESTS ───────────────────────────────────────────────────────── */

it('allows admin to view companies index page', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.companies.index'));

    $response->assertStatus(200);
    $response->assertSee('Original Company');
});

it('allows admin to create a new company', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.companies.store'), [
        'name'       => 'Nueva Empresa S.A.',
        'rfc'        => 'NEW123456TS2',
        'tax_regime' => 'moral',
        'phone'      => '1122334455',
        'address'    => 'Nueva Calle #123',
    ]);

    $response->assertRedirect(route('admin.companies.index'));
    $response->assertSessionHas('success', 'Empresa creada correctamente.');

    $this->assertDatabaseHas('companies', [
        'name' => 'Nueva Empresa S.A.',
        'rfc'  => 'NEW123456TS2',
    ]);
});

it('allows admin to edit a company', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.companies.update', $this->company), [
        'name'       => 'Empresa Editada',
        'rfc'        => 'EDIT123456TS3',
        'tax_regime' => 'fisica',
        'phone'      => '9988776655',
        'address'    => 'Calle Editada #456',
    ]);

    $response->assertRedirect(route('admin.companies.show', $this->company));
    $response->assertSessionHas('success', 'Empresa actualizada correctamente.');

    $this->company->refresh();
    expect($this->company->name)->toBe('Empresa Editada');
    expect($this->company->rfc)->toBe('EDIT123456TS3');
    expect($this->company->tax_regime)->toBe('fisica');
});

it('allows admin to soft delete a company and its projects', function () {
    $project = Project::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $this->actingAs($this->admin);

    $response = $this->delete(route('admin.companies.destroy', $this->company));

    $response->assertRedirect(route('admin.companies.index'));
    $response->assertSessionHas('success', 'Empresa eliminada correctamente.');

    $this->assertSoftDeleted('companies', [
        'id' => $this->company->id,
    ]);

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

/* ── USER TESTS ──────────────────────────────────────────────────────────── */

it('allows admin to view users index page', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.users.index'));

    $response->assertStatus(200);
    $response->assertSee('Cliente Original');
});

it('allows admin to create a new user and assign companies', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.users.store'), [
        'name'                  => 'Nuevo Cliente',
        'email'                 => 'nuevo@client.com',
        'phone'                 => '5544332211',
        'role'                  => 'client',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'company_ids'           => [$this->company->id],
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Usuario creado correctamente.');

    $newUser = User::where('email', 'nuevo@client.com')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->companies->pluck('id')->toArray())->toContain($this->company->id);
});

it('allows admin to edit a user and change password', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.users.update', $this->clientUser), [
        'name'                  => 'Cliente Modificado',
        'email'                 => 'modificado@client.com',
        'phone'                 => '9998887776',
        'role'                  => 'client',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'company_ids'           => [$this->company->id],
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Usuario actualizado correctamente.');

    $this->clientUser->refresh();
    expect($this->clientUser->name)->toBe('Cliente Modificado');
    expect($this->clientUser->email)->toBe('modificado@client.com');
    expect(Hash::check('newpassword123', $this->clientUser->password))->toBeTrue();
});

it('prevents clients from accessing user management', function () {
    $this->actingAs($this->clientUser);

    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('client.dashboard'));
});
