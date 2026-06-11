<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Original Admin', 'email' => 'admin@ebt.com']);
    $this->client = User::factory()->create(['role' => 'client']);
});

it('allows admin to access the profile edit page', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.profile.edit'));

    $response->assertStatus(200);
    $response->assertSee('Configuración');
    $response->assertSee('Original Admin');
    $response->assertSee('admin@ebt.com');
});

it('prevents client from accessing the admin profile edit page', function () {
    $this->actingAs($this->client);

    $response = $this->get(route('admin.profile.edit'));

    $response->assertRedirect(route('client.projects.index'));
});

it('allows admin to update their profile name and email', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.profile.update'), [
        'name'  => 'New Name',
        'email' => 'newadmin@ebt.com',
    ]);

    $response->assertRedirect(route('admin.profile.edit'));
    $response->assertSessionHas('success', 'Perfil actualizado con éxito.');

    $this->assertDatabaseHas('users', [
        'id'    => $this->admin->id,
        'name'  => 'New Name',
        'email' => 'newadmin@ebt.com',
    ]);
});

it('allows admin to update their password', function () {
    $this->actingAs($this->admin);

    $response = $this->put(route('admin.profile.update'), [
        'name'                  => 'Original Admin',
        'email'                 => 'admin@ebt.com',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('admin.profile.edit'));
    $response->assertSessionHas('success', 'Perfil actualizado con éxito.');

    $this->admin->refresh();
    $this->assertTrue(Hash::check('newpassword123', $this->admin->password));
});
