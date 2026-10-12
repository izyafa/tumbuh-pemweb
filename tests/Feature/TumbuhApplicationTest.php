<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\EducationalVideo;
use App\Models\ImmunizationRecord;
use App\Models\MeasurementRecord;
use App\Models\User;
use App\Services\AiNutritionService;
use Carbon\Carbon;
use Tests\TestCase;

class TumbuhApplicationTest extends TestCase
{
    public function test_login_page_renders_without_masuk_petugas_button(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('>Masuk Petugas<', false);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/kader/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_kader_can_login_and_access_dashboard(): void
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->post('/login', [
            'email' => $kader->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('kader.dashboard'));
        $this->assertAuthenticatedAs($kader);

        $dashResponse = $this->actingAs($kader)->get(route('kader.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Operasional Posyandu');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Eksekutif & Analitik Agregat');
    }

    public function test_kader_cannot_access_admin_dashboard(): void
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_parent_portal_is_publicly_accessible_without_auth(): void
    {
        $child = Child::first();

        $response = $this->get(route('parent.portal', ['token' => $child->public_token]));
        $response->assertStatus(200);
        $response->assertSee($child->full_name);
        $response->assertSee('Grafik Digital KMS Balita');
        $response->assertSee('Bagikan ke WhatsApp Keluarga');
    }

    public function test_parent_can_generate_ai_consultation_without_login(): void
    {
        $child = Child::whereHas('latestMeasurement')->first();

        $response = $this->postJson(route('parent.ai-consult', ['token' => $child->public_token]), [
            'daily_budget' => 60000,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'consultation',
            'ai_response' => [
                'recommendedMenu',
                'parentActionTips',
                'statusDiagnosis',
            ],
        ]);
    }

    public function test_preview_zscore_ajax_endpoint(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::first();

        $response = $this->actingAs($kader)->postJson(route('measurements.preview'), [
            'child_id' => $child->id,
            'weight' => 9.5,
            'height' => 76.0,
            'measurement_date' => Carbon::today()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'age_in_months',
            'tb_u' => ['zScore', 'status', 'label'],
            'bb_u' => ['zScore', 'status', 'label'],
            'bb_tb' => ['zScore', 'status', 'label'],
            'is_weight_stagnant',
            'badge_type',
            'badge_text',
        ]);
    }

    public function test_ai_nutrition_service_contract(): void
    {
        $service = new AiNutritionService();

        $result = $service->generateConsultation(
            childName: 'Muhammad Kenzo',
            ageInMonths: 14,
            gender: 'male',
            weight: 9.8,
            height: 77.0,
            zScoreTbU: 0.1,
            zScoreBbU: 0.0,
            zScoreBbTb: 0.0,
            isWeightStagnant: false,
            dailyBudget: 25000
        );

        $this->assertArrayHasKey('clinicalAlert', $result);
        $this->assertArrayHasKey('statusDiagnosis', $result);
        $this->assertArrayHasKey('recommendedMenu', $result);
        $this->assertArrayHasKey('parentActionTips', $result);
        $this->assertArrayHasKey('shouldReferToPuskesmas', $result);

        $this->assertCount(3, $result['recommendedMenu']);
        $this->assertEquals('Pagi', $result['recommendedMenu'][0]['mealTime']);
        $this->assertEquals('Siang', $result['recommendedMenu'][1]['mealTime']);
        $this->assertEquals('Sore / Malam', $result['recommendedMenu'][2]['mealTime']);
        $this->assertNotEmpty($result['recommendedMenu'][0]['ingredients']);
    }

    public function test_batch_measurement_submission(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::first();

        $initialCount = MeasurementRecord::count();

        $response = $this->actingAs($kader)->post(route('kader.measurements.storeBatch'), [
            'measurement_date' => Carbon::today()->toDateString(),
            'measurements' => [
                [
                    'child_id' => $child->id,
                    'weight' => 10.5,
                    'height' => 80.0,
                    'head_circum' => 47.0,
                    'arm_circum' => 15.0,
                    'notes' => 'Pemeriksaan rutin uji coba.',
                ],
            ],
        ]);

        $response->assertRedirect(route('kader.dashboard'));
        $this->assertEquals($initialCount + 1, MeasurementRecord::count());

        $latest = MeasurementRecord::orderBy('created_at', 'desc')->first();
        $this->assertEquals($child->id, $latest->child_id);
        $this->assertEquals(10.5, $latest->weight);
        $this->assertEquals(80.0, $latest->height);
    }

    public function test_tumbuh_academy_listing(): void
    {
        $response = $this->get(route('edukasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Tumbuh Academy');
        $response->assertSee('Protein Hewani Cegah Stunting');
    }

    public function test_tumbuh_academy_search_by_keyword(): void
    {
        $response = $this->get(route('edukasi.index', ['search' => 'ASI Eksklusif']));
        $response->assertStatus(200);
        $response->assertSee('Manajemen ASI Eksklusif');
        $response->assertDontSee('PHBS, Sanitasi Air Bersih');

        // Test with keyword matching summary
        $responseTelur = $this->get(route('edukasi.index', ['search' => 'telur']));
        $responseTelur->assertStatus(200);
        $responseTelur->assertSee('Protein Hewani');
        $responseTelur->assertDontSee('Manajemen ASI Eksklusif');
    }

    public function test_tumbuh_academy_search_no_results(): void
    {
        $response = $this->get(route('edukasi.index', ['search' => 'kata_kunci_tidak_ada_xyz']));
        $response->assertStatus(200);
        $response->assertSee('Tidak ada modul video yang cocok dengan pencarian');
    }

    public function test_tumbuh_academy_show_detail_page(): void
    {
        $video = \App\Models\EducationalVideo::first();
        $response = $this->get(route('edukasi.show', $video->id));
        $response->assertStatus(200);
        $response->assertSee($video->title);
        $response->assertSee('Uji Pemahaman Mandiri');
        $response->assertSee('Bagikan ke WhatsApp');
    }

    public function test_public_growth_calculator_endpoint(): void
    {
        $response = $this->get(route('kalkulator.index'));
        $response->assertStatus(200);
        $response->assertSee('Kalkulator Z-Score');

        $calcResponse = $this->postJson(route('kalkulator.calculate'), [
            'gender' => 'male',
            'age_in_months' => 14,
            'height' => 77.0,
            'weight' => 9.8,
        ]);

        $calcResponse->assertStatus(200);
        $calcResponse->assertJsonStructure([
            'age_in_months',
            'tb_u' => ['zScore', 'status', 'label'],
            'bb_u' => ['zScore', 'status', 'label'],
            'bb_tb' => ['zScore', 'status', 'label'],
            'alert_level',
            'summary_title',
            'recommendation',
        ]);
    }

    public function test_kader_can_access_reports_and_export_csv(): void
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('kader.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Kinerja SKDN');

        $csvResponse = $this->actingAs($kader)->get(route('kader.reports.export-csv'));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $printResponse = $this->actingAs($kader)->get(route('kader.reports.print'));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('SKDN');
    }

    public function test_kader_can_access_broadcast_whatsapp(): void
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('kader.broadcast.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengingat Jadwal Penimbangan');
        $response->assertSee('Daftar Kirim Pesan ke Orang Tua Balita');
    }

    public function test_print_kms_accessible_by_kader_and_parent(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::first();

        $kaderPrint = $this->actingAs($kader)->get(route('kader.children.kms-print', $child->id));
        $kaderPrint->assertStatus(200);
        $kaderPrint->assertSee('Kartu Menuju Sehat (KMS) Digital');
        $kaderPrint->assertSee($child->full_name);

        $parentPrint = $this->get(route('parent.kms-print', ['token' => $child->public_token]));
        $parentPrint->assertStatus(200);
        $parentPrint->assertSee('Kartu Menuju Sehat (KMS) Digital');
        $parentPrint->assertSee($child->full_name);
    }

    public function test_kader_can_record_and_delete_immunization(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::first();

        $storeResponse = $this->actingAs($kader)->post(route('kader.children.immunizations.store', $child->id), [
            'vaccine_name' => 'Campak-Rubela (MR)',
            'category' => 'imunisasi_dasar',
            'administered_date' => Carbon::today()->toDateString(),
            'batch_number' => 'TEST-VACCINE-1',
            'notes' => 'Uji coba penambahan imunisasi.',
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('immunization_records', [
            'child_id' => $child->id,
            'vaccine_name' => 'Campak-Rubela (MR)',
            'batch_number' => 'TEST-VACCINE-1',
        ]);

        $record = ImmunizationRecord::where('child_id', $child->id)
            ->where('batch_number', 'TEST-VACCINE-1')
            ->first();

        $deleteResponse = $this->actingAs($kader)->delete(route('kader.immunizations.destroy', $record->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('immunization_records', [
            'id' => $record->id,
        ]);
    }

    public function test_admin_videos_renders_visual_quiz_builder_interface(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.videos'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Modul Edukasi');
        $response->assertSee('Kuis Evaluasi Pemahaman Mandiri');
        $response->assertSee('Simulasi Interaktif Tampilan Kuis');
        $response->assertDontSee('Data Kuis JSON (2–3 Soal Pilihan Ganda)');
    }

    public function test_admin_can_store_educational_video_with_quiz_data(): void
    {
        $admin = User::where('role', 'admin')->first();

        $quizQuestions = [
            [
                'question' => 'Mengapa protein hewani sangat penting bagi pertumbuhan anak?',
                'options' => [
                    'Mengandung asam amino esensial lengkap',
                    'Lebih murah dan mudah basi',
                    'Mengandung banyak serat nabati',
                    'Hanya sebagai pelengkap saja',
                ],
                'correct_index' => 0,
                'explanation' => 'Protein hewani memiliki bioavailabilitas tinggi untuk memicu sekresi hormon pertumbuhan IGF-1.',
            ],
            [
                'question' => 'Kapan MPASI pertama kali mulai diperkenalkan?',
                'options' => [
                    'Sejak usia 2 bulan',
                    'Tepat saat usia 6 bulan',
                    'Setelah anak berusia 1 tahun',
                    'Saat gigi anak sudah tumbuh semua',
                ],
                'correct_index' => 1,
                'explanation' => 'Menurut WHO dan Kemenkes, MPASI dimulai tepat pada usia 6 bulan saat ASI saja tidak lagi mencukupi.',
            ],
        ];

        $response = $this->actingAs($admin)->post(route('admin.videos.store'), [
            'title' => 'Edukasi MPASI Kaya Zat Besi',
            'category' => 'MPASI 6-23 Bln',
            'youtube_url' => 'https://www.youtube.com/watch?v=Bx6bY19vQMA',
            'summary' => 'Ringkasan penting tentang pemberian MPASI kaya zat besi.',
            'quiz_data_raw' => json_encode($quizQuestions),
        ]);

        $response->assertRedirect(route('admin.videos'));
        $this->assertDatabaseHas('educational_videos', [
            'title' => 'Edukasi MPASI Kaya Zat Besi',
            'category' => 'MPASI 6-23 Bln',
        ]);

        $video = EducationalVideo::where('title', 'Edukasi MPASI Kaya Zat Besi')->first();
        $this->assertNotNull($video);
        $this->assertCount(2, $video->quiz_data);
        $this->assertEquals('Mengapa protein hewani sangat penting bagi pertumbuhan anak?', $video->quiz_data[0]['question']);
        $this->assertEquals(0, $video->quiz_data[0]['correct_index']);
    }

    public function test_admin_can_update_educational_video_and_quizzes(): void
    {
        $admin = User::where('role', 'admin')->first();
        $video = EducationalVideo::first();

        $newQuizzes = [
            [
                'question' => 'Pertanyaan Pembaruan Soal 1?',
                'options' => ['Opsi Alpha', 'Opsi Beta', 'Opsi Gamma'],
                'correct_index' => 1,
                'explanation' => 'Penjelasan jawaban beta benar.',
            ],
        ];

        $response = $this->actingAs($admin)->put(route('admin.videos.update', $video->id), [
            'title' => $video->title . ' (Updated)',
            'category' => $video->category,
            'youtube_url' => $video->youtube_url,
            'summary' => 'Ringkasan yang diperbarui.',
            'quiz_data_raw' => json_encode($newQuizzes),
        ]);

        $response->assertRedirect(route('admin.videos'));
        $video->refresh();
        $this->assertStringContainsString('(Updated)', $video->title);
        $this->assertCount(1, $video->quiz_data);
        $this->assertEquals('Pertanyaan Pembaruan Soal 1?', $video->quiz_data[0]['question']);
    }

    public function test_admin_can_delete_educational_video(): void
    {
        $admin = User::where('role', 'admin')->first();
        $video = EducationalVideo::create([
            'title' => 'Video Sementara Untuk Dihapus',
            'category' => 'PHBS & Sanitasi',
            'youtube_url' => 'https://www.youtube.com/watch?v=dummy12345',
            'summary' => 'Video ini akan dihapus.',
            'quiz_data' => null,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.videos.destroy', $video->id));
        $response->assertRedirect(route('admin.videos'));
        $this->assertDatabaseMissing('educational_videos', [
            'id' => $video->id,
        ]);
    }

    public function test_delete_confirmation_modal_present_in_layout(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.videos'));
        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Hapus Data');
        $response->assertSee('Tindakan ini permanen dan tidak dapat dibatalkan.');
    }

    public function test_kader_can_access_broadcast_page_with_mass_broadcast_features(): void
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('kader.broadcast.index'));
        $response->assertStatus(200);
        $response->assertSee('Siaran Pengingat Hari Posyandu');
        $response->assertSee('Kirim ke Semua Orang');
        $response->assertSee('Bagikan Pengumuman Grup');
        $response->assertSee('Pengiriman Siaran WhatsApp Massal');
        $response->assertSee('Salin Nomor HP');
        $response->assertSee('Pilih Semua Hasil Filter');
    }

    public function test_kader_can_access_dedicated_ai_consult_page(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::whereHas('latestMeasurement')->first();

        $response = $this->actingAs($kader)->get(route('kader.children.ai-consult.show', $child->id));
        $response->assertStatus(200);
        $response->assertSee($child->full_name);
        $response->assertSee('AI Clinical Nutrition Advisor');
        $response->assertSee('Peracik Menu Gizi &amp; Diagnosis Klinis AI', false);
        $response->assertSee('Anggaran Belanja Makan Harian Balita');
        $response->assertSee('Kembali ke Grafik KMS & Rekam Gizi', false);
    }

    public function test_kader_monitoring_page_is_unbloated_and_links_to_ai_consult(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::whereHas('latestMeasurement')->first();

        $response = $this->actingAs($kader)->get(route('kader.children.show', $child->id));
        $response->assertStatus(200);
        $response->assertSee('Kurva Pertumbuhan Digital KMS');
        $response->assertSee('Rekomendasi Menu Makan &amp; Gizi Balita (AI)', false);
        $response->assertSee(route('kader.children.ai-consult.show', $child->id));
        $response->assertDontSee('id="budget_input"', false);
        $response->assertDontSee('aiAdvisorWidget()', false);
        $response->assertDontSee('Gemini AI Sedang Meracik Menu...', false);
    }

    public function test_parent_can_access_dedicated_menu_gizi_page_without_auth(): void
    {
        $child = Child::whereHas('latestMeasurement')->first();

        $response = $this->get(route('parent.menu-gizi', ['token' => $child->public_token]));
        $response->assertStatus(200);
        $response->assertSee($child->full_name);
        $response->assertSee('Panduan Nutrisi Balita');
        $response->assertSee('Racik Menu Makan Harian Sesuai Anggaran');
        $response->assertSee('Anggaran Belanja Makan Hari Ini');
        $response->assertSee('Kembali ke Grafik Pemantauan KMS');
    }

    public function test_parent_portal_monitoring_page_is_unbloated_and_links_to_menu_gizi(): void
    {
        $child = Child::whereHas('latestMeasurement')->first();

        $response = $this->get(route('parent.portal', ['token' => $child->public_token]));
        $response->assertStatus(200);
        $response->assertSee('Grafik Digital KMS Balita');
        $response->assertSee('Panduan Nutrisi &amp; Rekomendasi Menu Makan (AI)', false);
        $response->assertSee(route('parent.menu-gizi', $child->public_token));
        $response->assertDontSee('id="parent_budget"', false);
        $response->assertDontSee('parentAiWidget()', false);
        $response->assertDontSee('Meracik Menu AI...', false);
    }

    public function test_parent_ai_consult_get_redirects_to_menu_gizi(): void
    {
        $child = Child::first();

        $response = $this->get("/pantau/{$child->public_token}/ai-consult");
        $response->assertRedirect(route('parent.menu-gizi', $child->public_token));
    }

    public function test_parent_portal_has_dedicated_immunization_tab(): void
    {
        $child = Child::whereHas('immunizationRecords')->first();

        $response = $this->get(route('parent.portal', ['token' => $child->public_token]));
        $response->assertStatus(200);
        $response->assertSee('Grafik Pertumbuhan KMS');
        $response->assertSee('Buku Imunisasi &amp; Vitamin A', false);
        $response->assertSee('activeTab', false);
        $response->assertSee('x-show="activeTab === \'kms\'"', false);
        $response->assertSee('x-show="activeTab === \'imunisasi\'"', false);
    }

    public function test_kader_children_show_has_tabs_for_measurements_and_immunizations(): void
    {
        $kader = User::where('role', 'kader')->first();
        $child = Child::first();

        $response = $this->actingAs($kader)->get(route('kader.children.show', $child->id));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Penimbangan &amp; Pengukuran', false);
        $response->assertSee('Buku Imunisasi &amp; Vitamin A', false);
        $response->assertSee('x-show="activeTab === \'penimbangan\'"', false);
        $response->assertSee('x-show="activeTab === \'imunisasi\'"', false);
    }
}

