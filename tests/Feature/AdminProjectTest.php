<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->client = User::factory()->create(['role' => 'client']);
});

it('allows admin to create a project for a client', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.clients.projects.store', $this->client), [
        'name'                => 'Nuevo Proyecto EBT',
        'status'              => 'active',
        'progress_percentage' => 25,
    ]);

    $response->assertRedirect(route('admin.clients.show', $this->client));
    $response->assertSessionHas('success', 'Proyecto creado correctamente.');

    $this->assertDatabaseHas('projects', [
        'user_id'             => $this->client->id,
        'name'                => 'Nuevo Proyecto EBT',
        'status'              => 'active',
        'progress_percentage' => 25,
    ]);
});

it('validates project input fields', function () {
    $this->actingAs($this->admin);

    $response = $this->post(route('admin.clients.projects.store', $this->client), [
        'name'                => '',
        'status'              => 'invalid-status',
        'progress_percentage' => 150,
    ]);

    $response->assertSessionHasErrors(['name', 'status', 'progress_percentage']);
});

it('prevents client from creating a project', function () {
    $this->actingAs($this->client);

    $response = $this->post(route('admin.clients.projects.store', $this->client), [
        'name'                => 'Proyecto No Autorizado',
        'status'              => 'active',
        'progress_percentage' => 0,
    ]);

    $response->assertRedirect(route('client.projects.index'));
});

it('allows admin to delete a project', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $this->actingAs($this->admin);

    $response = $this->delete(route('admin.clients.projects.destroy', [$this->client, $project]));

    $response->assertRedirect(route('admin.clients.show', $this->client));
    $response->assertSessionHas('success', 'Proyecto eliminado correctamente.');

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

it('allows admin to update a project', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
        'name' => 'Old Name',
        'status' => 'active',
        'progress_percentage' => 10,
    ]);

    $this->actingAs($this->admin);

    $response = $this->put(route('admin.clients.projects.update', [$this->client, $project]), [
        'name'                => 'Updated Name',
        'status'              => 'paused',
        'progress_percentage' => 50,
    ]);

    $response->assertRedirect(route('admin.clients.projects.show', [$this->client, $project]));
    $response->assertSessionHas('success', 'Proyecto actualizado correctamente.');

    $this->assertDatabaseHas('projects', [
        'id'                  => $project->id,
        'name'                => 'Updated Name',
        'status'              => 'paused',
        'progress_percentage' => 50,
    ]);
});

it('validates project update fields', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $this->actingAs($this->admin);

    $response = $this->put(route('admin.clients.projects.update', [$this->client, $project]), [
        'name'                => '',
        'status'              => 'invalid-status',
        'progress_percentage' => 150,
    ]);

    $response->assertSessionHasErrors(['name', 'status', 'progress_percentage']);
});

it('prevents client from updating a project', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $this->actingAs($this->client);

    $response = $this->put(route('admin.clients.projects.update', [$this->client, $project]), [
        'name'                => 'Unauthorized Update',
        'status'              => 'completed',
        'progress_percentage' => 100,
    ]);

    $response->assertRedirect(route('client.projects.index'));
});
