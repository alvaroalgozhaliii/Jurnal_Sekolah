<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaSekolahDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_approval_by_waka_is_visible_to_kepala_dashboard(): void
    {
        $waka = User::factory()->create([
            'nama' => 'Waka SDM',
            'username' => 'waka_sdm_1',
            'role' => 'waka_sdm',
            'aktif' => 1,
        ]);

        $guruUser = User::factory()->create([
            'nama' => 'Guru Test',
            'username' => 'guru_test',
            'role' => 'guru',
            'aktif' => 1,
        ]);

        $guru = Guru::create([
            'id_user' => $guruUser->id_user,
            'nama' => 'Guru Test',
            'nip' => '198001012020011001',
            'bidang_studi' => 'Matematika',
            'no_telp' => '081234567890',
        ]);

        $pengajuan = PengajuanIzin::create([
            'kategori' => 'izin_guru',
            'id_guru' => $guru->id_guru,
            'id_user_pengaju' => $guruUser->id_user,
            'tanggal' => '2026-08-24',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'perkiraan_kembali' => '10:30',
            'jenis_izin' => 'Izin Guru',
            'alasan' => 'Melayani tugas penting di luar sekolah',
            'status' => 'pending_waka',
            'butuh_satpam' => false,
        ]);

        $this->actingAs($waka)
            ->post(route('pengajuan.approve.waka', $pengajuan->id_pengajuan), [
                'keputusan' => 'setujui',
                'catatan' => 'Setuju, lanjut ke kepala sekolah',
            ]);

        $this->assertDatabaseHas('pengajuan_izin', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status' => 'pending_kepala',
        ]);

        $kepala = User::factory()->create([
            'nama' => 'Kepala Sekolah',
            'username' => 'kepsek_1',
            'role' => 'kepala_sekolah',
            'aktif' => 1,
        ]);

        $response = $this->actingAs($kepala)->get(route('kepala.dashboard'));

        $response->assertStatus(200)
            ->assertSee('Pending Final Approval')
            ->assertSee('Guru Test');
    }

    public function test_jurnal_harian_create_view_renders_without_selected_schedule(): void
    {
        $guruUser = User::factory()->create([
            'nama' => 'Guru Jurnal',
            'username' => 'guru_jurnal',
            'role' => 'guru',
            'aktif' => 1,
        ]);

        $guru = Guru::create([
            'id_user' => $guruUser->id_user,
            'nama' => 'Guru Jurnal',
            'nip' => '199001012022011002',
            'bidang_studi' => 'Biologi',
            'no_telp' => '081234567891',
        ]);

        $kelas = \App\Models\Kelas::create([
            'nama_kelas' => 'XI IPA 1',
            'id_guru_walikelas' => $guru->id_guru,
        ]);

        \App\Models\Jadwal::create([
            'hari' => 'Senin',
            'jam_ke' => 1,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
            'mapel' => 'Biologi',
            'ruang' => 'Lab 1',
            'waktu_mulai' => '07:00:00',
            'waktu_selesai' => '08:30:00',
            'aktif' => 1,
        ]);

        $response = $this->actingAs($guruUser)->get(route('jurnal-harian.create'));

        $response->assertStatus(200)
            ->assertSee('Formulir Jurnal Harian KBM')
            ->assertDontSee('Undefined variable');
    }

    public function test_jurnal_harian_index_view_renders_without_filter_variables(): void
    {
        $guruUser = User::factory()->create([
            'nama' => 'Guru List',
            'username' => 'guru_list',
            'role' => 'guru',
            'aktif' => 1,
        ]);

        $guru = Guru::create([
            'id_user' => $guruUser->id_user,
            'nama' => 'Guru List',
            'nip' => '199001012022011003',
            'bidang_studi' => 'Fisika',
            'no_telp' => '081234567892',
        ]);

        $kelas = \App\Models\Kelas::create([
            'nama_kelas' => 'XII IPA 2',
            'id_guru_walikelas' => $guru->id_guru,
        ]);

        \App\Models\Jadwal::create([
            'hari' => 'Selasa',
            'jam_ke' => 2,
            'id_kelas' => $kelas->id_kelas,
            'id_guru' => $guru->id_guru,
            'mapel' => 'Fisika',
            'ruang' => 'Lab 2',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '09:30:00',
            'aktif' => 1,
        ]);

        $response = $this->actingAs($guruUser)->get(route('jurnal-harian.index'));

        $response->assertStatus(200)
            ->assertSee('Jurnal Harian KBM')
            ->assertDontSee('Undefined variable');
    }
}
