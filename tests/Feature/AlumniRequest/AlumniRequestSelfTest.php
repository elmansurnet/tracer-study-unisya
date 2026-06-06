<?php

namespace Tests\Feature\AlumniRequest;

use App\Models\Alumni;
use App\Models\AlumniRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniRequestSelfTest extends TestCase
{
    use RefreshDatabase;

    private User   $alumniUser;
    private Alumni $alumni;

    private User   $otherAlumniUser;
    private Alumni $otherAlumni;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alumniUser = User::factory()->alumni()->create();
        $this->alumni     = Alumni::factory()->create([
            'user_id' => $this->alumniUser->id,
            'phone'   => '08123456789',
        ]);

        // Alumni lain — untuk uji isolasi kepemilikan
        $this->otherAlumniUser = User::factory()->alumni()->create();
        $this->otherAlumni     = Alumni::factory()->create([
            'user_id' => $this->otherAlumniUser->id,
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

    public function test_alumni_can_list_own_requests(): void
    {
        $this->makeRequest();
        // Permohonan alumni lain — tidak boleh muncul di respons
        AlumniRequest::factory()->create([
            'alumni_id'  => $this->otherAlumni->id,
            'field_name' => 'email',
            'new_value'  => 'other@example.com',
            'created_by' => $this->otherAlumniUser->id,
            'updated_by' => $this->otherAlumniUser->id,
        ]);

        $response = $this->actingAs($this->alumniUser)
            ->getJson('/api/v1/alumni/requests');

        $response->assertOk();

        // Hasil yang dikembalikan hanya milik alumni ini
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue(
            $ids->every(fn ($id) => AlumniRequest::find($id)?->alumni_id === $this->alumni->id)
        );
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function test_alumni_can_view_own_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->alumniUser)
             ->getJson("/api/v1/alumni/requests/{$req->id}")
             ->assertOk()
             ->assertJsonPath('data.id', $req->id);
    }

    public function test_alumni_cannot_view_others_request(): void
    {
        // Permohonan milik alumni lain
        $otherReq = AlumniRequest::factory()->create([
            'alumni_id'  => $this->otherAlumni->id,
            'field_name' => 'email',
            'new_value'  => 'other@example.com',
            'created_by' => $this->otherAlumniUser->id,
            'updated_by' => $this->otherAlumniUser->id,
        ]);

        $this->actingAs($this->alumniUser)
             ->getJson("/api/v1/alumni/requests/{$otherReq->id}")
             ->assertForbidden();
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function test_alumni_can_submit_request(): void
    {
        $response = $this->actingAs($this->alumniUser)
            ->postJson('/api/v1/alumni/requests', [
                'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
                'field_name' => 'address',
                'old_value'  => 'Jl. Lama No. 1',
                'new_value'  => 'Jl. Baru No. 2',
                'reason'     => 'Pindah alamat.',
            ]);

        $response->assertCreated()
                 ->assertJsonPath('data.status', AlumniRequest::STATUS_MENUNGGU)
                 ->assertJsonPath('data.alumni_id', $this->alumni->id);
    }

    public function test_alumni_cannot_inject_other_alumni_id(): void
    {
        // alumni_id di body harus diabaikan — di-inject dari sesi
        $response = $this->actingAs($this->alumniUser)
            ->postJson('/api/v1/alumni/requests', [
                'alumni_id'  => $this->otherAlumni->id,  // Injeksi alumni lain
                'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
                'field_name' => 'phone',
                'new_value'  => '08100000000',
            ]);

        // Harus 201 tapi alumni_id tetap milik sendiri
        $response->assertCreated()
                 ->assertJsonPath('data.alumni_id', $this->alumni->id);
    }

    public function test_alumni_cannot_submit_duplicate_pending_field(): void
    {
        $this->makeRequest();  // pending 'phone' sudah ada

        $this->actingAs($this->alumniUser)
             ->postJson('/api/v1/alumni/requests', [
                 'type'       => AlumniRequest::TYPE_UPDATE_PROFIL,
                 'field_name' => 'phone',
                 'new_value'  => '08100000001',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['field_name']);
    }

    // ─── Cancel ───────────────────────────────────────────────────────────────

    public function test_alumni_can_cancel_pending_request(): void
    {
        $req = $this->makeRequest();

        $this->actingAs($this->alumniUser)
             ->deleteJson("/api/v1/alumni/requests/{$req->id}")
             ->assertOk()
             ->assertJsonPath('message', fn ($v) => str_contains($v, 'dibatalkan'));

        $this->assertSoftDeleted('alumni_requests', ['id' => $req->id]);
    }

    public function test_alumni_cannot_cancel_approved_request(): void
    {
        $req = $this->makeRequest(['status' => AlumniRequest::STATUS_DISETUJUI]);

        $this->actingAs($this->alumniUser)
             ->deleteJson("/api/v1/alumni/requests/{$req->id}")
             ->assertStatus(422);
    }

    public function test_alumni_cannot_cancel_others_request(): void
    {
        $otherReq = AlumniRequest::factory()->create([
            'alumni_id'  => $this->otherAlumni->id,
            'field_name' => 'email',
            'new_value'  => 'other@example.com',
            'status'     => AlumniRequest::STATUS_MENUNGGU,
            'created_by' => $this->otherAlumniUser->id,
            'updated_by' => $this->otherAlumniUser->id,
        ]);

        $this->actingAs($this->alumniUser)
             ->deleteJson("/api/v1/alumni/requests/{$otherReq->id}")
             ->assertForbidden();
    }
}
