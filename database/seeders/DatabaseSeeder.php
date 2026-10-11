<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\EducationalVideo;
use App\Models\ImmunizationRecord;
use App\Models\MeasurementRecord;
use App\Models\User;
use App\Services\WhoGrowthService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $whoService = new WhoGrowthService();

        // 1. Admin & Cadre Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@tumbuh.id'],
            [
                'name' => 'dr. Hendra Pratama, Sp.A',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'posyandu_name' => 'Puskesmas Pembina Sejahtera',
                'phone' => '081234567890',
            ]
        );

        $kader1 = User::firstOrCreate(
            ['email' => 'kader1@tumbuh.id'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'posyandu_name' => 'Posyandu Melati RW 03',
                'phone' => '081234567891',
            ]
        );

        $kader2 = User::firstOrCreate(
            ['email' => 'kader2@tumbuh.id'],
            [
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'posyandu_name' => 'Posyandu Melati RW 03',
                'phone' => '081234567892',
            ]
        );

        // 2. Children Data
        $childrenData = [
            [
                'nik' => '3201014501230001',
                'full_name' => 'Muhammad Kenzo Al-Fatih',
                'dob' => Carbon::now()->subMonths(14)->startOfDay(),
                'gender' => 'male',
                'birth_weight' => 3.3,
                'birth_height' => 50.0,
                'parent_name' => 'Ahmad Fauzi & Rina Wijaya',
                'parent_phone' => '081399887766',
                'address' => 'Jl. Mawar No. 12, RT 02/RW 03, Kel. Sukamaju',
                'logs' => [
                    ['months_ago' => 2, 'weight' => 9.4, 'height' => 75.5, 'head' => 45.5, 'arm' => 14.5, 'notes' => 'Tumbuh sehat, nafsu makan baik.'],
                    ['months_ago' => 1, 'weight' => 9.8, 'height' => 77.0, 'head' => 46.0, 'arm' => 14.8, 'notes' => 'Imunisasi PCV 3 lengkap.'],
                    ['months_ago' => 0, 'weight' => 10.2, 'height' => 78.5, 'head' => 46.5, 'arm' => 15.0, 'notes' => 'Makan lahap dengan lauk telur & ikan.'],
                ],
            ],
            [
                'nik' => '3201014501230002',
                'full_name' => 'Aisyah Nur Salsabila',
                'dob' => Carbon::now()->subMonths(18)->startOfDay(),
                'gender' => 'female',
                'birth_weight' => 2.8,
                'birth_height' => 47.5,
                'parent_name' => 'Bambang Irawan & Nurul Hidayah',
                'parent_phone' => '081288776655',
                'address' => 'Jl. Dahlia No. 4, RT 01/RW 03, Kel. Sukamaju',
                'logs' => [
                    ['months_ago' => 2, 'weight' => 8.2, 'height' => 74.0, 'head' => 44.0, 'arm' => 13.0, 'notes' => 'Tinggi badan di bawah garis -2 SD, perlu perhatian.'],
                    ['months_ago' => 1, 'weight' => 8.4, 'height' => 75.2, 'head' => 44.3, 'arm' => 13.2, 'notes' => 'Diberikan edukasi menu kaya hati ayam.'],
                    ['months_ago' => 0, 'weight' => 8.6, 'height' => 76.0, 'head' => 44.5, 'arm' => 13.3, 'notes' => 'Masih tergolong stunting, rujuk konsultasi gizi.'],
                ],
            ],
            [
                'nik' => '3201014501230003',
                'full_name' => 'Rayyan Danendra',
                'dob' => Carbon::now()->subMonths(24)->startOfDay(),
                'gender' => 'male',
                'birth_weight' => 3.2,
                'birth_height' => 49.5,
                'parent_name' => 'Dedi Kurniawan & Maya Septiani',
                'parent_phone' => '081577665544',
                'address' => 'Jl. Kenanga No. 18, RT 03/RW 03, Kel. Sukamaju',
                'logs' => [
                    ['months_ago' => 2, 'weight' => 11.5, 'height' => 85.5, 'head' => 48.0, 'arm' => 15.2, 'notes' => 'Kondisi fisik aktif.'],
                    ['months_ago' => 1, 'weight' => 11.8, 'height' => 86.8, 'head' => 48.2, 'arm' => 15.3, 'notes' => 'Batuk pilek ringan seminggu lalu.'],
                    ['months_ago' => 0, 'weight' => 11.8, 'height' => 87.5, 'head' => 48.4, 'arm' => 15.2, 'notes' => 'BB stagnan (2T Rule), nafsu makan turun.'],
                ],
            ],
            [
                'nik' => '3201014501230004',
                'full_name' => 'Nafisha Zanna Kirana',
                'dob' => Carbon::now()->subMonths(8)->startOfDay(),
                'gender' => 'female',
                'birth_weight' => 2.4,
                'birth_height' => 46.0,
                'parent_name' => 'Ferry Gunawan & Tri Handayani',
                'parent_phone' => '081966554433',
                'address' => 'Jl. Anggrek No. 7, RT 04/RW 03, Kel. Sukamaju',
                'logs' => [
                    ['months_ago' => 2, 'weight' => 5.8, 'height' => 61.5, 'head' => 40.5, 'arm' => 11.8, 'notes' => 'Berat dan tinggi badan tertinggal jauh (-3 SD).'],
                    ['months_ago' => 1, 'weight' => 6.1, 'height' => 62.8, 'head' => 41.0, 'arm' => 12.0, 'notes' => 'Diberikan PMT pemulihan lokal.'],
                    ['months_ago' => 0, 'weight' => 6.4, 'height' => 64.0, 'head' => 41.5, 'arm' => 12.2, 'notes' => 'Severely stunted, memerlukan intervensi dokter spesialis anak.'],
                ],
            ],
            [
                'nik' => '3201014501230005',
                'full_name' => 'Gibran Arkanza Putra',
                'dob' => Carbon::now()->subMonths(11)->startOfDay(),
                'gender' => 'male',
                'birth_weight' => 3.4,
                'birth_height' => 51.0,
                'parent_name' => 'Rahmat Hidayat & Sri Wahyuni',
                'parent_phone' => '081255443322',
                'address' => 'Jl. Cempaka No. 22, RT 02/RW 03, Kel. Sukamaju',
                'logs' => [
                    ['months_ago' => 2, 'weight' => 8.8, 'height' => 72.0, 'head' => 45.0, 'arm' => 14.2, 'notes' => 'ASI aktif ditambah MPASI bubur kental.'],
                    ['months_ago' => 1, 'weight' => 9.2, 'height' => 73.5, 'head' => 45.4, 'arm' => 14.5, 'notes' => 'Kenaikan BB mengikuti pita hijau KMS.'],
                    ['months_ago' => 0, 'weight' => 9.6, 'height' => 74.8, 'head' => 45.8, 'arm' => 14.8, 'notes' => 'Gizi baik, perkembangan motorik sesuai umur.'],
                ],
            ],
        ];

        foreach ($childrenData as $childInfo) {
            $child = Child::firstOrCreate(
                ['nik' => $childInfo['nik']],
                [
                    'full_name' => $childInfo['full_name'],
                    'date_of_birth' => $childInfo['dob'],
                    'gender' => $childInfo['gender'],
                    'birth_weight' => $childInfo['birth_weight'],
                    'birth_height' => $childInfo['birth_height'],
                    'parent_name' => $childInfo['parent_name'],
                    'parent_phone' => $childInfo['parent_phone'],
                    'address' => $childInfo['address'],
                    'public_token' => (string) Str::uuid(),
                ]
            );

            $prevWeight = null;
            foreach ($childInfo['logs'] as $log) {
                $measurementDate = Carbon::now()->subMonths($log['months_ago'])->startOfDay();
                $ageInMonths = $whoService->calculateAgeInMonths($child->date_of_birth, $measurementDate);

                $tbResult = $whoService->calculateZScoreTbU($child->gender, $ageInMonths, (float) $log['height']);
                $bbResult = $whoService->calculateZScoreBbU($child->gender, $ageInMonths, (float) $log['weight']);
                $bbtbResult = $whoService->calculateZScoreBbTb($child->gender, (float) $log['height'], (float) $log['weight']);

                $isStagnant = false;
                if ($prevWeight !== null && (float) $log['weight'] <= $prevWeight) {
                    $isStagnant = true;
                }
                $prevWeight = (float) $log['weight'];

                MeasurementRecord::create([
                    'child_id' => $child->id,
                    'measured_by_id' => $kader1->id,
                    'measurement_date' => $measurementDate,
                    'age_in_months' => $ageInMonths,
                    'weight' => $log['weight'],
                    'height' => $log['height'],
                    'head_circum' => $log['head'],
                    'arm_circum' => $log['arm'],
                    'zscore_tb_u' => $tbResult['zScore'],
                    'zscore_bb_u' => $bbResult['zScore'],
                    'zscore_bb_tb' => $bbtbResult['zScore'],
                    'stunting_status' => $tbResult['status'],
                    'is_weight_stagnant' => $isStagnant,
                    'notes' => $log['notes'],
                ]);
            }

            // Seed typical immunization milestones for this child
            $dob = $child->date_of_birth;
            $sampleVaccines = [
                ['name' => 'HB0 (Hepatitis B)', 'cat' => 'imunisasi_dasar', 'days' => 1],
                ['name' => 'BCG', 'cat' => 'imunisasi_dasar', 'days' => 30],
                ['name' => 'Polio 1', 'cat' => 'imunisasi_dasar', 'days' => 30],
                ['name' => 'DPT-HB-Hib 1', 'cat' => 'imunisasi_dasar', 'days' => 60],
                ['name' => 'Polio 2', 'cat' => 'imunisasi_dasar', 'days' => 60],
                ['name' => 'PCV 1', 'cat' => 'imunisasi_dasar', 'days' => 60],
                ['name' => 'DPT-HB-Hib 2', 'cat' => 'imunisasi_dasar', 'days' => 90],
                ['name' => 'Polio 3', 'cat' => 'imunisasi_dasar', 'days' => 90],
                ['name' => 'PCV 2', 'cat' => 'imunisasi_dasar', 'days' => 90],
                ['name' => 'DPT-HB-Hib 3', 'cat' => 'imunisasi_dasar', 'days' => 120],
                ['name' => 'Polio 4', 'cat' => 'imunisasi_dasar', 'days' => 120],
                ['name' => 'IPV (Polio Suntik)', 'cat' => 'imunisasi_dasar', 'days' => 120],
                ['name' => 'Vitamin A Biru (100.000 IU)', 'cat' => 'vitamin_a', 'days' => 180],
                ['name' => 'Campak-Rubela (MR)', 'cat' => 'imunisasi_dasar', 'days' => 270],
                ['name' => 'Vitamin A Merah (200.000 IU)', 'cat' => 'vitamin_a', 'days' => 365],
                ['name' => 'Obat Cacing (Albendazole)', 'cat' => 'obat_cacing', 'days' => 365],
            ];

            foreach ($sampleVaccines as $vac) {
                $vacDate = $dob->copy()->addDays($vac['days']);
                if ($vacDate->isPast()) {
                    ImmunizationRecord::create([
                        'child_id' => $child->id,
                        'recorded_by_id' => $kader1->id,
                        'vaccine_name' => $vac['name'],
                        'category' => $vac['cat'],
                        'administered_date' => $vacDate,
                        'batch_number' => 'BIO-' . rand(1000, 9999),
                        'notes' => 'Diberikan sesuai jadwal rutin di Posyandu Melati.',
                    ]);
                }
            }
        }

        // 3. Educational Videos (Official Accessible YouTube Content from Kemenkes RI & UNICEF)
        $videos = [
            [
                'title' => 'Pentingnya Protein Hewani Cegah Stunting Sejak Dini',
                'category' => 'MPASI 6-23 Bln',
                'youtube_url' => 'https://www.youtube.com/watch?v=Bx6bY19vQMA',
                'summary' => "• Pemberian MPASI berkualitas dengan fokus protein hewani (telur, ikan lokal, ayam, hati) setiap hari adalah kunci utama pencegahan stunting.\n• Protein hewani mengandung asam amino esensial lengkap dan bioavailabilitas tinggi untuk memicu sekresi hormon IGF-1.\n• Telur dan ikan kembung/lele adalah sumber protein berkualitas tinggi yang sangat terjangkau bagi keluarga.",
                'quiz_data' => [
                    [
                        'question' => 'Mengapa protein hewani lebih diutamakan daripada protein nabati dalam pencegahan stunting anak?',
                        'options' => [
                            'Protein hewani lebih murah harganya di pasar',
                            'Protein hewani mengandung asam amino esensial lengkap dengan daya serap (bioavailabilitas) lebih tinggi',
                            'Protein nabati tidak mengandung vitamin sama sekali',
                            'Protein hewani membuat anak cepat kenyang',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Protein hewani memiliki nilai biologis dan profil asam amino esensial (seperti leusin) yang optimal untuk memicu sintesis protein otot dan pertumbuhan tulang linier.',
                    ],
                    [
                        'question' => 'Berapa frekuensi konsumsi protein hewani yang dianjurkan oleh Kemenkes RI untuk balita MPASI?',
                        'options' => [
                            'Cukup 1 kali seminggu',
                            'Hanya saat anak sedang sakit',
                            'Setiap kali makan utama (2–3 kali sehari)',
                            'Sebulan sekali saat Posyandu',
                        ],
                        'correct_index' => 2,
                        'explanation' => 'Kemenkes menganjurkan protein hewani disajikan dalam setiap waktu makan utama (misal: telur pagi hari, hati siang hari, ikan sore hari).',
                    ],
                ],
            ],
            [
                'title' => 'Manajemen ASI Eksklusif dan Cara Mengatasi Masalah Pelekatan',
                'category' => 'ASI Eksklusif 0-6 Bln',
                'youtube_url' => 'https://www.youtube.com/watch?v=NU_MDQ2iNYE',
                'summary' => "• Bayi usia 0–6 bulan hanya membutuhkan ASI tanpa tambahan air putih, madu, maupun makanan lain.\n• Pelekatan yang benar (areola bagian bawah masuk lebih banyak ke mulut bayi) mencegah puting lecet dan memastikan bayi mendapat hindmilk kaya lemak.\n• Tanda kecukupan ASI: BAK minimal 6 kali sehari dan kenaikan berat badan sesuai kurva KMS.",
                'quiz_data' => [
                    [
                        'question' => 'Apa tanda utama pelekatan menyusui yang baik dan benar?',
                        'options' => [
                            'Hanya puting susu saja yang masuk ke mulut bayi',
                            'Dagu menempel ke payudara ibu, mulut terbuka lebar, dan bibir bawah terlipat keluar',
                            'Terdengar bunyi decak keras saat bayi menyusu',
                            'Ibu merasakan nyeri tajam sepanjang proses menyusui',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Pelekatan yang tepat ditandai dengan dagu menempel pada payudara, mulut terbuka lebar, dan lebih banyak areola bagian bawah masuk ke mulut bayi.',
                    ],
                    [
                        'question' => 'Kapan bayi diperbolehkan mulai diperkenalkan dengan makanan pendamping ASI (MPASI)?',
                        'options' => [
                            'Tepat saat usia genap 6 bulan (180 hari)',
                            'Usia 3 bulan jika bayi sering menangis',
                            'Usia 4 bulan dengan pisang lumat',
                            'Usia 1 tahun',
                        ],
                        'correct_index' => 0,
                        'explanation' => 'WHO dan Kemenkes merekomendasikan ASI eksklusif penuh hingga 6 bulan (180 hari) sebelum memulai MPASI bertahap.',
                    ],
                ],
            ],
            [
                'title' => 'PHBS, Sanitasi Air Bersih, dan Hubungannya dengan Stunting',
                'category' => 'PHBS & Sanitasi',
                'youtube_url' => 'https://www.youtube.com/watch?v=PBHFf-eNG2E',
                'summary' => "• Penyakit infeksi berulang seperti diare kronis dan cacingan adalah penyebab langsung stunting kedua setelah kekurangan asupan gizi.\n• Kondisi Environmental Enteropathy (kerusakan dinding usus akibat bakteri lingkungan) menghalangi penyerapan nutrisi makanan bergizi.\n• Terapkan Cuci Tangan Pakai Sabun (CTPS) 5 waktu penting dan pastikan jamban sehat keluarga.",
                'quiz_data' => [
                    [
                        'question' => 'Bagaimana sanitasi lingkungan yang buruk dapat menyebabkan anak menjadi stunting?',
                        'options' => [
                            'Anak menjadi malas berolahraga di luar rumah',
                            'Infeksi bakteri berulang merusak dinding usus (enteropati lingkungan) sehingga zat gizi gagal diserap tubuh',
                            'Udara kotor menurunkan tinggi badan secara langsung',
                            'Sanitasi buruk hanya mempengaruhi batuk dan pilek',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Bakteri dari tinja atau air terkontaminasi menyebabkan inflamasi subklinis pada usus halus, membuat vili usus tumpul sehingga nutrisi tidak terserap maksimal.',
                    ],
                    [
                        'question' => 'Kapan salah satu waktu paling krusial untuk mencuci tangan pakai sabun (CTPS) bagi pengasuh balita?',
                        'options' => [
                            'Sebelum menyiapkan makanan / menyuapi anak dan setelah membersihkan tinja bayi',
                            'Hanya saat mandi pagi',
                            'Setelah anak selesai tidur siang',
                            'Saat anak berulang tahun',
                        ],
                        'correct_index' => 0,
                        'explanation' => 'Mencuci tangan dengan air mengalir dan sabun sebelum menyiapkan makanan dan setelah membersihkan kotoran memutus penularan patogen fekal-oral.',
                    ],
                ],
            ],
            [
                'title' => 'Deteksi Dini Weight Faltering: Selamatkan Anak dari Stunting',
                'category' => 'MPASI 6-23 Bln',
                'youtube_url' => 'https://www.youtube.com/watch?v=ItNQjPn3OGQ',
                'summary' => "• Weight faltering (kenaikan berat badan tidak adekuat atau kurva KMS melandai) adalah sinyal awal sebelum anak jatuh ke kondisi stunting klinis.\n• Deteksi dini di Posyandu dengan penimbangan rutin setiap bulan memungkinkan intervensi gizi sebelum tinggi badan terhambat.\n• Segera evaluasi aturan pemberian makan (feeding rules), atasi infeksi penyerta, dan tingkatkan asupan kalori serta protein hewani.",
                'quiz_data' => [
                    [
                        'question' => 'Apa yang dimaksud dengan kondisi Weight Faltering pada pemantauan tumbuh kembang balita?',
                        'options' => [
                            'Berat badan anak melonjak melebihi garis batas atas kurva KMS',
                            'Kenaikan berat badan melambat atau tidak memenuhi Kenaikan Berat Badan Minimal (KBM) pada KMS',
                            'Tinggi badan anak bertambah sangat drastis dalam satu bulan',
                            'Kondisi balita yang selalu tertidur setelah menyusu',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Weight faltering adalah kegagalan mencapai target kenaikan berat badan bulanan (T / tidak naik), yang bila dibiarkan akan memicu gangguan pertumbuhan linier (stunting).',
                    ],
                    [
                        'question' => 'Kapan langkah intervensi gizi paling efektif dilakukan untuk mencegah stunting permanen?',
                        'options' => [
                            'Menunggu hingga anak berusia di atas 5 tahun',
                            'Segera saat berat badan pertama kali tidak naik (T1/T2) atau kurva KMS melandai di Posyandu',
                            'Hanya saat anak sudah didiagnosis gizi buruk di rumah sakit',
                            'Tidak perlu intervensi khusus karena berat badan akan naik sendiri',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Intervensi paling efektif adalah pada fase awal weight faltering sebelum defisit nutrisi kronis mengorbankan tinggi badan anak.',
                    ],
                ],
            ],
            [
                'title' => 'Siaran Sehat Kemenkes: 1000 Hari Pertama Kehidupan Cegah Stunting',
                'category' => 'Gizi Ibu Hamil',
                'youtube_url' => 'https://www.youtube.com/watch?v=C5GW-uLfzTA',
                'summary' => "• Periode 1000 Hari Pertama Kehidupan (270 hari masa kehamilan + 730 hari hingga usia 2 tahun) adalah masa emas pembentukan 80% organ otak balita.\n• Ibu hamil wajib mengonsumsi minimal 90 Tablet Tambah Darah (TTD) untuk mencegah anemia dan risiko kelahiran bayi Berat Badan Lahir Rendah (BBLR).\n• Lakukan pemeriksaan kehamilan (ANC) teratur minimal 6 kali dengan 2 kali pemeriksaan dokter dan USG di Puskesmas.",
                'quiz_data' => [
                    [
                        'question' => 'Berapa lama rentang waktu yang dimaksud dalam periode emas "1000 Hari Pertama Kehidupan" (1000 HPK)?',
                        'options' => [
                            'Dimulai sejak anak lahir hingga usia 3 tahun',
                            '270 hari selama kehamilan dalam kandungan ditambah 730 hari hingga anak genap 2 tahun',
                            '1000 hari setelah anak masuk sekolah PAUD/TK',
                            'Hanya selama ibu menyusui secara eksklusif',
                        ],
                        'correct_index' => 1,
                        'explanation' => '1000 HPK mencakup 270 hari kehamilan (9 bulan) ditambah 730 hari (2 tahun pertama kehidupan) yang merupakan jendela peluang pertumbuhan kritis.',
                    ],
                    [
                        'question' => 'Berapa jumlah minimal konsumsi Tablet Tambah Darah (TTD) yang diwajibkan bagi ibu hamil selama masa kehamilan?',
                        'options' => [
                            'Minimal 90 tablet selama kehamilan',
                            'Cukup 5 tablet saat trimester pertama',
                            'Hanya saat ibu hamil merasa pusing atau lemas',
                            'Tidak perlu diminum jika sudah mengonsumsi sayuran hijau',
                        ],
                        'correct_index' => 0,
                        'explanation' => 'Kemenkes mewajibkan konsumsi minimal 90 tablet tambah darah (zat besi dan asam folat) untuk mencegah anemia maternal penyebab utama stunting.',
                    ],
                ],
            ],
            [
                'title' => 'Takaran Porsi MPASI Standar WHO & Isi Piringku Kemenkes RI',
                'category' => 'MPASI 6-23 Bln',
                'youtube_url' => 'https://www.youtube.com/watch?v=GAE4dNzcXxY',
                'summary' => "• Komposisi menu Isi Piringku untuk MPASI harus mencakup karbohidrat, protein hewani minimal 30–35% energi, lemak tambahan, serta sayur dan buah.\n• Takaran porsi: usia 6–8 bulan (mulai 2–3 sdm bertahap hingga 1/2 mangkuk 250ml), 9–11 bulan (1/2 mangkuk), 12–23 bulan (3/4 hingga 1 mangkuk penuh).\n• Frekuensi pemberian: 2–3 kali makan utama ditambah 1–2 kali makanan selingan sehat kaya zat gizi setiap hari.",
                'quiz_data' => [
                    [
                        'question' => 'Berapa porsi awal perkenalan MPASI untuk bayi usia genap 6 bulan menurut standar Kemenkes RI/WHO?',
                        'options' => [
                            '1 mangkuk penuh ukuran 500 ml sekali makan',
                            'Mulai dari 2–3 sendok makan dewasa secara bertahap ditingkatkan hingga 1/2 mangkuk (125 ml)',
                            'Cukup 1 tetes kuah sayur bening saja',
                            'Langsung 5 kali makan porsi besar setiap hari',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Lambung bayi usia 6 bulan baru seukuran kepalan tangannya, sehingga dimulai dari 2–3 sendok makan kental bertahap hingga setengah mangkuk 250 ml.',
                    ],
                    [
                        'question' => 'Mengapa menu MPASI "tunggal" (hanya satu jenis buah atau sari beras) tidak lagi dianjurkan oleh Kemenkes dan IDAI?',
                        'options' => [
                            'Karena buah dan sayuran membahayakan kesehatan bayi',
                            'Karena menu tunggal kekurangan zat besi, kalori, dan asam amino protein hewani yang sangat dibutuhkan perkembangan otak bayi',
                            'Karena buah terlalu sulit untuk dihaluskan oleh ibu',
                            'Karena bayi belum boleh merasakan rasa manis alami',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'Sejak usia 6 bulan cadangan zat besi bawaan lahir bayi habis, sehingga MPASI harus langsung berupa menu lengkap (kaya protein hewani dan zat besi) bukan menu tunggal.',
                    ],
                ],
            ],
        ];

        foreach ($videos as $video) {
            EducationalVideo::create($video);
        }
    }
}
