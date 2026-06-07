<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->client = User::factory()->create([
        'name'         => 'Cliente Original',
        'email'        => 'original@client.com',
        'company_name' => 'Original Company',
        'phone'        => '1234567890',
        'role'         => 'client',
        'password'     => Hash::make('password123'),
    ]);
});

it('allows admin to edit a client', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.clients.update', $this->client), [
        'name'                  => 'Cliente Editado',
        'email'                 => 'editado@client.com',
        'company_name'          => 'Edited Company',
        'phone'                 => '0987654321',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('admin.clients.show', $this->client));
    $response->assertSessionHas('success', 'Cliente actualizado correctamente.');

    $this->client->refresh();

    expect($this->client->name)->toBe('Cliente Editado');
    expect($this->client->email)->toBe('editado@client.com');
    expect($this->client->company_name)->toBe('Edited Company');
    expect($this->client->phone)->toBe('0987654321');
    expect(Hash::check('newpassword123', $this->client->password))->toBeTrue();
});

it('allows admin to edit a client without changing the password', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.clients.update', $this->client), [
        'name'                  => 'Cliente Editado 2',
        'email'                 => 'editado2@client.com',
        'company_name'          => 'Edited Company 2',
        'phone'                 => '1112223333',
        'password'              => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('admin.clients.show', $this->client));
    $response->assertSessionHas('success', 'Cliente actualizado correctamente.');

    $this->client->refresh();

    expect($this->client->name)->toBe('Cliente Editado 2');
    expect(Hash::check('password123', $this->client->password))->toBeTrue();
});

it('validates client edit inputs', function () {
    $this->actingAs($this->admin);

    // Create another client to test unique email validation
    $otherClient = User::factory()->create(['email' => 'taken@email.com', 'role' => 'client']);

    $response = $this->put(route('admin.clients.update', $this->client), [
        'name'                  => '',
        'email'                 => 'taken@email.com',
        'password'              => 'short',
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

it('allows admin to soft delete a client and their projects', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $this->actingAs($this->admin);

    $response = $this->delete(route('admin.clients.destroy', $this->client));

    $response->assertRedirect(route('admin.clients.index'));
    $response->assertSessionHas('success', 'Cliente eliminado correctamente.');

    $this->assertSoftDeleted('users', [
        'id' => $this->client->id,
    ]);

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

it('prevents clients from updating client details', function () {
    $this->actingAs($this->client);

    $response = $this->put(route('admin.clients.update', $this->client), [
        'name'  => 'Intento No Autorizado',
        'email' => 'unauthorized@client.com',
    ]);

    $response->assertRedirect(route('client.projects.index'));
});

it('prevents clients from deleting client accounts', function () {
    $this->actingAs($this->client);

    $response = $this->delete(route('admin.clients.destroy', $this->client));

    $response->assertRedirect(route('client.projects.index'));
});
