<?php

namespace Tests\Feature\AlumniRequest;

use App\Models\Alumni;
use App\Models\AlumniRequest;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniRequestAdminTest extends TestCase
{
    use RefreshDatabase;

    private User   $admin;
    private User   $alumniUser;
    private Alumni $alumni;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->alumniUser = User::factory()->alumni()->create();
        $this->alumni     = Alumni::factory()->create([
            'user_id'    => $this->alumniUser->id,
            'name'       => 'Budi Santoso',
            'phone'      => '08123456789',
            'email'      => 'budi@example.com',
            'is_employed' => false,
        ]);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function makeRequest(array $overrides = []): AlumniRequest
    {
        return AlumniRequest::factory()->create(array_merge([
            'alumni_id'  => $this->alumni->id,
            'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
            'field_name' => 'phone',
            'old_value'  => '08123456789',
            'new_value'  => '08199999999',
            'status'     => AlumniRequest::STATUS_MENUNGGU,
            'created_by' => $this->alumniUser->id,
            'updated_by' => $this->alumniUser->id,
        ], $overrides));
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function test_admin_can_list_alumni_requests(): void
    {
        $this->makeRequest();
        $this->makeRequest(['field_name' => 'email', 'old_value' => 'budi@example.com', 'new_value' => 'new@example.com']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/alumni-requests');

        $response->assertOk()
                 ->assertJsonStructure(['data', 'meta']);
    }

    public function test_guest_cannot_access_admin_alumni_requests(): void
    {
        $this->getJson('/api/v1/admin/alumni-requests')
             ->assertUnauthorized();
    }

    // ─── Count Pending ────────────────────────────────────────────────────────

    public function test_admin_can_get_count_pending(): void
    {
        $this->makeRequest();
        $this->makeRequest(['field_name' => 'email', 'old_value' => 'budi@example.com', 'new_value' => 'new@example.com']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/admin/alumni-requests/count-pending');

        $response->assertOk()
                 ->assertJsonPath('data.count', 2);
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function test_admin_can_view_single_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->admin)
             ->getJson("/api/v1/admin/alumni-requests/{$req->id}")
             ->assertOk()
             ->assertJsonPath('data.id', $req->id);
    }

    public function test_admin_gets_404_for_nonexistent_request(): void
    {
        $this->actingAs($this->admin)
             ->getJson('/api/v1/admin/alumni-requests/nonexistent-id')
             ->assertNotFound();
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function test_admin_can_create_alumni_request(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/admin/alumni-requests', [
                'alumni_id'  => $this->alumni->id,
                'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
                'field_name' => 'city',
                'old_value'  => 'Surabaya',
                'new_value'  => 'Malang',
            ]);

        $response->assertCreated()
                 ->assertJsonPath('data.field_name', 'city')
                 ->assertJsonPath('data.status', AlumniRequest::STATUS_MENUNGGU);
    }

    public function test_duplicate_pending_field_is_rejected(): void
    {
        // Buat permohonan pending untuk field 'phone' lebih dahulu
        $this->makeRequest();

        $this->actingAs($this->admin)
             ->postJson('/api/v1/admin/alumni-requests', [
                 'alumni_id'  => $this->alumni->id,
                 'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
                 'field_name' => 'phone',
                 'new_value'  => '08111111111',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['field_name']);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function test_admin_can_update_pending_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->admin)
             ->putJson("/api/v1/admin/alumni-requests/{$req->id}", [
                 'new_value' => '08188888888',
             ])
             ->assertOk()
             ->assertJsonPath('data.new_value', '08188888888');
    }

    public function test_cannot_update_approved_request(): void
    {
        $req = $this->makeRequest(['status' => AlumniRequest::STATUS_DISETUJUI]);

        $this->actingAs($this->admin)
             ->putJson("/api/v1/admin/alumni-requests/{$req->id}", [
                 'new_value' => '08188888888',
             ])
             ->assertUnprocessable();
    }

    // ─── Destroy + Restore ───────────────────────────────────────────────────

    public function test_admin_can_soft_delete_pending_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->admin)
             ->deleteJson("/api/v1/admin/alumni-requests/{$req->id}")
             ->assertOk()
             ->assertJsonPath('message', fn ($v) => str_contains($v, 'dihapus'));

        $this->assertSoftDeleted('alumni_requests', ['id' => $req->id]);
    }

    public function test_admin_cannot_delete_approved_request(): void
    {
        $req = $this->makeRequest(['status' => AlumniRequest::STATUS_DISETUJUI]);

        $this->actingAs($this->admin)
             ->deleteJson("/api/v1/admin/alumni-requests/{$req->id}")
             ->assertStatus(422);
    }

    public function test_admin_can_restore_deleted_request(): void
    {
        $req = $this->makeRequest();
        $req->delete();

        $this->actingAs($this->admin)
             ->patchJson("/api/v1/admin/alumni-requests/{$req->id}/restore")
             ->assertOk()
             ->assertJsonPath('data.id', $req->id);

        $this->assertDatabaseHas('alumni_requests', ['id' => $req->id, 'deleted_at' => null]);
    }

    // ─── Approve ─────────────────────────────────────────────────────────────

    public function test_admin_can_approve_request_and_applies_to_alumni(): void
    {
        $req = $this->makeRequest([
            'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
            'field_name' => 'phone',
            'old_value'  => '08123456789',
            'new_value'  => '08199999999',
        ]);

        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/alumni-requests/{$req->id}/approve", [
                 'review_notes' => 'Disetujui oleh admin.',
             ])
             ->assertOk()
             ->assertJsonPath('data.status', AlumniRequest::STATUS_DISETUJUI);

        // Pastikan field alumni benar-benar diperbarui
        $this->assertDatabaseHas('alumni', [
            'id'    => $this->alumni->id,
            'phone' => '08199999999',
        ]);
    }

    public function test_cannot_approve_non_pending_request(): void
    {
        $req = $this->makeRequest(['status' => AlumniRequest::STATUS_DISETUJUI]);

        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/alumni-requests/{$req->id}/approve")
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['status']);
    }

    // ─── Reject ──────────────────────────────────────────────────────────────

    public function test_admin_can_reject_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/alumni-requests/{$req->id}/reject", [
                 'review_notes' => 'Data tidak sesuai.',
             ])
             ->assertOk()
             ->assertJsonPath('data.status', AlumniRequest::STATUS_DITOLAK);

        // Pastikan data alumni TIDAK berubah setelah reject
        $this->assertDatabaseHas('alumni', [
            'id'    => $this->alumni->id,
            'phone' => '08123456789',
        ]);
    }

    public function test_cannot_reject_non_pending_request(): void
    {
        $req = $this->makeRequest(['status' => AlumniRequest::STATUS_DITOLAK]);

        $this->actingAs($this->admin)
             ->postJson("/api/v1/admin/alumni-requests/{$req->id}/reject")
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['status']);
    }
}
