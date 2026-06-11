<?php

use App\Models\Post;
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

    $customDate = '2026-05-15 10:30:00';

    $response = $this->post(route('admin.clients.projects.store', $this->client), [
        'name'                => 'Nuevo Proyecto EBT',
        'status'              => 'active',
        'progress_percentage' => 25,
        'created_at'          => $customDate,
    ]);

    $response->assertRedirect(route('admin.clients.show', $this->client));
    $response->assertSessionHas('success', 'Proyecto creado correctamente.');

    $this->assertDatabaseHas('projects', [
        'user_id'             => $this->client->id,
        'name'                => 'Nuevo Proyecto EBT',
        'status'              => 'active',
        'progress_percentage' => 25,
        'created_at'          => $customDate,
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

    $customDate = '2026-05-20 15:45:00';

    $response = $this->put(route('admin.clients.projects.update', [$this->client, $project]), [
        'name'                => 'Updated Name',
        'status'              => 'paused',
        'progress_percentage' => 50,
        'created_at'          => $customDate,
    ]);

    $response->assertRedirect(route('admin.clients.projects.show', [$this->client, $project]));
    $response->assertSessionHas('success', 'Proyecto actualizado correctamente.');

    $this->assertDatabaseHas('projects', [
        'id'                  => $project->id,
        'name'                => 'Updated Name',
        'status'              => 'paused',
        'progress_percentage' => 50,
        'created_at'          => $customDate,
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

it('allows admin to update a post', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $post = Post::create([
        'project_id'  => $project->id,
        'title'       => 'Old Title',
        'description' => 'Old Description',
    ]);

    $this->actingAs($this->admin);

    $response = $this->put(route('admin.clients.projects.posts.update', [$this->client, $project, $post]), [
        'title'       => 'Updated Title',
        'description' => 'Updated Description',
    ]);

    $response->assertRedirect(route('admin.clients.projects.show', [$this->client, $project]));
    $response->assertSessionHas('success', 'Publicación actualizada correctamente.');

    $this->assertDatabaseHas('posts', [
        'id'          => $post->id,
        'title'       => 'Updated Title',
        'description' => 'Updated Description',
    ]);
});

it('prevents client from updating a post', function () {
    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    $post = Post::create([
        'project_id'  => $project->id,
        'title'       => 'Old Title',
        'description' => 'Old Description',
    ]);

    $this->actingAs($this->client);

    $response = $this->put(route('admin.clients.projects.posts.update', [$this->client, $project, $post]), [
        'title'       => 'Unauthorized Update',
        'description' => 'Should fail',
    ]);

    $response->assertRedirect(route('client.projects.index'));
});

it('allows admin to create a post with docx, xlsx, zip, rar attachments', function () {
    $this->actingAs($this->admin);

    $project = Project::factory()->create([
        'user_id' => $this->client->id,
    ]);

    \Illuminate\Support\Facades\Storage::fake('public');

    $docx = \Illuminate\Http\UploadedFile::fake()->create('documento.docx', 0);
    $xlsx = \Illuminate\Http\UploadedFile::fake()->create('planilla.xlsx', 100);
    $zip = \Illuminate\Http\UploadedFile::fake()->create('archivos.zip', 100);
    $rar = \Illuminate\Http\UploadedFile::fake()->create('respaldo.rar', 100);

    $response = $this->post(route('admin.clients.projects.posts.store', [$this->client, $project]), [
        'title'       => 'Avance con Adjuntos Especiales',
        'description' => 'Prueba de subida de archivos varios.',
        'attachments' => [$docx, $xlsx, $zip, $rar],
    ]);

    $response->assertRedirect(route('admin.clients.projects.show', [$this->client, $project]));
    $response->assertSessionHas('success', 'Publicación creada correctamente.');

    $this->assertDatabaseHas('posts', [
        'project_id' => $project->id,
        'title'      => 'Avance con Adjuntos Especiales',
    ]);

    $post = Post::where('title', 'Avance con Adjuntos Especiales')->first();
    $this->assertCount(4, $post->attachments);

    $this->assertDatabaseHas('attachments', ['file_name' => 'documento.docx', 'type' => 'document']);
    $this->assertDatabaseHas('attachments', ['file_name' => 'planilla.xlsx', 'type' => 'document']);
    $this->assertDatabaseHas('attachments', ['file_name' => 'archivos.zip', 'type' => 'document']);
    $this->assertDatabaseHas('attachments', ['file_name' => 'respaldo.rar', 'type' => 'document']);
});
