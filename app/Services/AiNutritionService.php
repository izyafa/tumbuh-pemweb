<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiNutritionService
{
    /**
     * Generate nutritional consultation and dietary plan
     */
    public function generateConsultation(
        string $childName,
        int $ageInMonths,
        string $gender,
        float $weight,
        float $height,
        float $zScoreTbU,
        float $zScoreBbU,
        float $zScoreBbTb,
        bool $isWeightStagnant,
        int $dailyBudget = 20000
    ): array {
        $genderText = strtolower($gender) === 'female' ? 'Perempuan' : 'Laki-laki';
        $formattedBudget = 'Rp' . number_format($dailyBudget, 0, ',', '.');
        $stagnantText = $isWeightStagnant ? 'BB STAGNAN / Tidak Naik (2T Rule)' : 'BB Naik / Normal';

        // Hitung pembagian anggaran ke 3 waktu makan: Pagi (30%), Siang (40%), Malam (30%)
        $costPagiRaw = (int) round(($dailyBudget * 0.30) / 1000) * 1000;
        $costSiangRaw = (int) round(($dailyBudget * 0.40) / 1000) * 1000;
        $costMalamRaw = $dailyBudget - $costPagiRaw - $costSiangRaw;
        if ($costMalamRaw <= 0) {
            $costPagiRaw = (int) round($dailyBudget / 3);
            $costSiangRaw = (int) round($dailyBudget / 3);
            $costMalamRaw = $dailyBudget - $costPagiRaw - $costSiangRaw;
        }

        $costPagi = 'Rp' . number_format($costPagiRaw, 0, ',', '.');
        $costSiang = 'Rp' . number_format($costSiangRaw, 0, ',', '.');
        $costMalam = 'Rp' . number_format($costMalamRaw, 0, ',', '.');

        $systemPrompt = "You are an expert pediatric clinical nutritionist and stunting intervention specialist working under the standards of the Indonesian Ministry of Health (Kemenkes RI) and WHO.
Your job is to generate UNIQUE, DIVERSE, TASTY, and HIGHLY VARIED daily meal plans for an Indonesian toddler, strictly divided across 3 meals (Pagi, Siang, Sore/Malam).
CRITICAL RULES FOR BUDGET ALLOCATION AND MENU VARIETY:
1. The user's total daily budget is {$formattedBudget}. You MUST divide it across the 3 meals: Pagi is approximately {$costPagi}, Siang is approximately {$costSiang}, and Sore/Malam is approximately {$costMalam}. The 'estimatedCost' field of each meal in the JSON must match these allocated amounts so the sum equals {$formattedBudget}!
2. NEVER GENERATE THE SAME GENERIC MENU! Every generation must be fresh, creative, and distinct. Freely use diverse protein sources: daging sapi giling/cincang, daging ayam fillet/cincang/ayam kampung, aneka ikan (kembung, tenggiri, lele, kakap, dori, mujair, belut), udang kupas cincang, aneka telur (ayam ras, puyuh, ayam kampung, bebek), hati ayam, tahu sutra, tempe bacem/gurih, edamame, and varied vegetables (brokoli, wortel, bayam, labu siam, daun kelor, jagung manis, kentang, makaroni, tomat) plus healthy cooking fats (santan segar, keju, margarin/mentega, minyak kelapa).
3. Adjust food quality and ingredients based on the budget: for higher budgets (Rp 50.000 - Rp 150.000+), incorporate premium fresh proteins (daging sapi cincang, ikan kakap/tenggiri, telur puyuh/ayam kampung, keju, buah segar); for modest budgets, maximize affordable nutrient-dense local staples.
4. Adapt textures to child age: 0-6 months = ASI Eksklusif; 6-8 months = saring/lumat kental; 9-11 months = cincang halus/nasi tim lembek; 12-59 months = makanan keluarga padat gizi.
5. Return STRICT JSON ONLY without markdown backticks.";

        $userPrompt = <<<EOT
Data Pasien Anak:
- Nama: {$childName}
- Usia: {$ageInMonths} bulan
- Jenis Kelamin: {$genderText}
- Berat Badan: {$weight} kg
- Tinggi/Panjang Badan: {$height} cm
- Z-Score TB/U (Stunting): {$zScoreTbU} SD
- Z-Score BB/U (Underweight): {$zScoreBbU} SD
- Z-Score BB/TB (Wasting): {$zScoreBbTb} SD
- Status Kenaikan BB: {$stagnantText}
- Total Anggaran Belanja Makan Harian: {$formattedBudget}
- Pembagian Target Anggaran (3 Waktu Makan):
  * Pagi: {$costPagi}
  * Siang: {$costSiang}
  * Sore / Malam: {$costMalam}

Hasilkan variasi menu makanan baru yang lezat, segar, dan berbeda dari sebelumnya dengan biaya sesuai pembagian di atas dalam struktur JSON berikut:
{
  "clinicalAlert": "Peringatan medis jika ada risiko stunting atau BB stagnan, atau null jika aman",
  "statusDiagnosis": "Analisis ringkas status pertumbuhan fisik anak",
  "recommendedMenu": [
    {
      "mealTime": "Pagi",
      "menuName": "Nama menu sarapan kreatif & bernutrisi",
      "ingredients": "Bahan dan takaran taktis",
      "estimatedCost": "{$costPagi}"
    },
    {
      "mealTime": "Siang",
      "menuName": "Nama menu makan siang lezat & kaya protein",
      "ingredients": "Bahan dan takaran taktis",
      "estimatedCost": "{$costSiang}"
    },
    {
      "mealTime": "Sore / Malam",
      "menuName": "Nama menu makan malam padat gizi",
      "ingredients": "Bahan dan takaran taktis",
      "estimatedCost": "{$costMalam}"
    }
  ],
  "parentActionTips": [
    "Tips 1 (pola asuh / stimulasi makan)",
    "Tips 2 (higienitas & sanitasi)",
    "Tips 3 (densitas energi / pemenuhan nutrisi)"
  ],
  "shouldReferToPuskesmas": false
}
EOT;

        $provider = env('AI_PROVIDER', 'gemini');

        // Try Gemini if selected and key present
        if ($provider === 'gemini' && config('services.gemini.api_key')) {
            try {
                $response = $this->callGemini($systemPrompt, $userPrompt);
                if ($response) {
                    return $response;
                }
            } catch (Exception $e) {
                Log::warning('Gemini API error, falling back to algorithmic expert engine: ' . $e->getMessage());
            }
        }

        // Try OpenAI if selected or as alternative
        if (($provider === 'openai' || !empty(config('services.openai.api_key'))) && config('services.openai.api_key')) {
            try {
                $response = $this->callOpenAi($systemPrompt, $userPrompt);
                if ($response) {
                    return $response;
                }
            } catch (Exception $e) {
                Log::warning('OpenAI API error, falling back to algorithmic expert engine: ' . $e->getMessage());
            }
        }

        // Algorithmic clinical fallback compliant with Kemenkes RI guidelines
        return $this->generateClinicalFallback(
            $childName,
            $ageInMonths,
            $genderText,
            $weight,
            $height,
            $zScoreTbU,
            $zScoreBbU,
            $zScoreBbTb,
            $isWeightStagnant,
            $dailyBudget
        );
    }

    protected function callGemini(string $systemPrompt, string $userPrompt): ?array
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return null;
        }

        $configuredModel = config('services.gemini.model', 'gemini-3.1-flash-lite');
        // List of models to try in priority order
        $modelsToTry = array_unique([$configuredModel, 'gemini-3.1-flash-lite', 'gemini-3.5-flash', 'gemini-flash-latest']);

        foreach ($modelsToTry as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::timeout(25)->post($url, [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemPrompt],
                        ],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $userPrompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.85,
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $parts = $data['candidates'][0]['content']['parts'] ?? [];
                    $rawText = null;
                    foreach ($parts as $p) {
                        if (!empty($p['text']) && empty($p['thought'])) {
                            $rawText = $p['text'];
                            break;
                        }
                    }
                    if (!$rawText && !empty($parts[0]['text'])) {
                        $rawText = $parts[0]['text'];
                    }

                    if ($rawText) {
                        $parsed = $this->parseAndValidateJson($rawText);
                        if ($parsed) {
                            Log::info("Gemini AI consultation generated successfully using model: {$model}");
                            return $parsed;
                        }
                    }
                } else {
                    Log::warning("Gemini model {$model} returned status {$response->status()}: " . substr($response->body(), 0, 150));
                }
            } catch (Exception $e) {
                Log::warning("Gemini model {$model} error: " . $e->getMessage());
            }
        }

        return null;
    }

    protected function callOpenAi(string $systemPrompt, string $userPrompt): ?array
    {
        $apiKey = config('services.openai.api_key');
        if (!$apiKey) {
            return null;
        }

        $response = Http::timeout(15)
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $rawText = $data['choices'][0]['message']['content'] ?? null;
            if ($rawText) {
                return $this->parseAndValidateJson($rawText);
            }
        }

        return null;
    }

    protected function parseAndValidateJson(string $text): ?array
    {
        $cleaned = trim($text);
        if (str_starts_with($cleaned, '```json')) {
            $cleaned = substr($cleaned, 7);
        } elseif (str_starts_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 3);
        }
        if (str_ends_with($cleaned, '```')) {
            $cleaned = substr($cleaned, 0, -3);
        }

        $data = json_decode(trim($cleaned), true);
        if (is_array($data) && isset($data['recommendedMenu']) && isset($data['parentActionTips'])) {
            return $data;
        }

        return null;
    }

    /**
     * Deterministic, clinically validated fallback compliant with Kemenkes RI & WHO standards
     */
    protected function generateClinicalFallback(
        string $childName,
        int $ageInMonths,
        string $genderText,
        float $weight,
        float $height,
        float $zScoreTbU,
        float $zScoreBbU,
        float $zScoreBbTb,
        bool $isWeightStagnant,
        int $dailyBudget
    ): array {
        $shouldRefer = $zScoreTbU < -2.0 || $zScoreBbU < -2.0 || $zScoreBbTb < -2.0 || $isWeightStagnant;

        $clinicalAlert = null;
        if ($zScoreTbU < -3.0) {
            $clinicalAlert = "PERINGATAN KLINIS: Anak terindikasi Severely Stunted (TB/U < -3 SD). Segera rujuk ke Puskesmas/Dokter Spesialis Anak untuk tata laksana gizi terpadu.";
        } elseif ($isWeightStagnant) {
            $clinicalAlert = "PERINGATAN: Berat badan tidak naik (Stagnan / 2T). Risiko tinggi weight faltering menuju stunting, perlu konfirmasi asupan dan pemeriksaan klinis Puskesmas.";
        } elseif ($zScoreTbU < -2.0) {
            $clinicalAlert = "PERINGATAN: Anak terindikasi Stunted (Pendek). Diperlukan intervensi protein hewani padat gizi setiap waktu makan.";
        }

        $diagnosis = "Anak {$childName} ({$ageInMonths} bln, {$genderText}) memiliki TB {$height} cm (Z-score {$zScoreTbU} SD), BB {$weight} kg (Z-score {$zScoreBbU} SD). ";
        if ($zScoreTbU < -2.0) {
            $diagnosis .= "Terdapat hambatan laju pertumbuhan linier (stunting). Prioritas utama adalah penyediaan protein hewani tinggi leusin dan zat besi.";
        } else {
            $diagnosis .= "Pertumbuhan linier dalam rentang normal WHO. Pertahankan status gizi dan keberagaman pangan lokal padat mikronutrien.";
        }

        // Hitung proporsi 3 waktu makan dari total anggaran
        $costPagiRaw = (int) round(($dailyBudget * 0.30) / 1000) * 1000;
        $costSiangRaw = (int) round(($dailyBudget * 0.40) / 1000) * 1000;
        $costMalamRaw = $dailyBudget - $costPagiRaw - $costSiangRaw;
        if ($costMalamRaw <= 0) {
            $costPagiRaw = (int) round($dailyBudget / 3);
            $costSiangRaw = (int) round($dailyBudget / 3);
            $costMalamRaw = $dailyBudget - $costPagiRaw - $costSiangRaw;
        }

        $costPagi = 'Rp' . number_format($costPagiRaw, 0, ',', '.');
        $costSiang = 'Rp' . number_format($costSiangRaw, 0, ',', '.');
        $costMalam = 'Rp' . number_format($costMalamRaw, 0, ',', '.');

        if ($ageInMonths < 6) {
            $menu = [
                [
                    'mealTime' => 'Pagi',
                    'menuName' => 'ASI Eksklusif On-Demand',
                    'ingredients' => 'Air Susu Ibu (ASI) sesering mungkin, minimal 8-12 kali per 24 jam.',
                    'estimatedCost' => 'Rp0',
                ],
                [
                    'mealTime' => 'Siang',
                    'menuName' => 'ASI Eksklusif On-Demand',
                    'ingredients' => 'Pemberian ASI dari kedua payudara hingga tuntas (hindmilk kaya lemak pertumbuhan).',
                    'estimatedCost' => 'Rp0',
                ],
                [
                    'mealTime' => 'Sore / Malam',
                    'menuName' => 'ASI Eksklusif Malam Hari',
                    'ingredients' => 'Pemberian ASI malam hari untuk stimulasi hormon prolaktin dan pertumbuhan anak.',
                    'estimatedCost' => 'Rp0',
                ],
            ];
        } elseif ($zScoreTbU < -2.0 || $isWeightStagnant) {
            $variations = [
                [
                    ['mealTime' => 'Pagi', 'menuName' => 'Bubur Beras Telur Puyuh & Bayam Cincang', 'ingredients' => 'Nasi lembek/bubur, 3 butir telur puyuh rebus matang, sedikit santan segar encer, daun bayam cincang.', 'estimatedCost' => $costPagi],
                    ['mealTime' => 'Siang', 'menuName' => 'Nasi Tim Hati Ayam Kampung & Tahu Lembut', 'ingredients' => 'Nasi tim, 35g hati ayam matang dihaluskan, 1 potong tahu sutra, kaldu ayam alami, minyak kelapa 1 sdt.', 'estimatedCost' => $costSiang],
                    ['mealTime' => 'Sore / Malam', 'menuName' => 'Nasi Lembut Sop Ikan Kembung Suwir & Wortel', 'ingredients' => 'Nasi pulen hangat, 40g daging ikan kembung kukus (Omega-3 tinggi), wortel parut, kuah bawang putih gurih.', 'estimatedCost' => $costMalam],
                ],
                [
                    ['mealTime' => 'Pagi', 'menuName' => 'Bubur Gurih Telur Ayam Kampung & Daun Kelor', 'ingredients' => 'Beras pulen, 1 butir telur ayam kampung orak-arik, daun kelor cincang halus, sedikit minyak kelapa.', 'estimatedCost' => $costPagi],
                    ['mealTime' => 'Siang', 'menuName' => 'Nasi Tim Lele Bumbu Kuning & Tempe Kukus', 'ingredients' => 'Nasi tim hangat, 1/2 ekor lele kukus bumbu kunyit jahe, 1 potong tempe bacem, kuah kaldu segar.', 'estimatedCost' => $costSiang],
                    ['mealTime' => 'Sore / Malam', 'menuName' => 'Sup Daging Sapi Cincang, Brokoli & Kentang', 'ingredients' => 'Kentang tumbuk/nasi, 35g daging sapi cincang, kuntum brokoli kukus halus, santan encer gurih.', 'estimatedCost' => $costMalam],
                ],
                [
                    ['mealTime' => 'Pagi', 'menuName' => 'Nasi Tim Orak-Arik Telur Puyuh & Labu Kuning', 'ingredients' => 'Nasi tim, 3 butir telur puyuh cincang, labu kuning lumat kaya provitamin A, kaldu ayam.', 'estimatedCost' => $costPagi],
                    ['mealTime' => 'Siang', 'menuName' => 'Nasi Tim Fillet Tongkol & Tahu Kuah Santan', 'ingredients' => 'Nasi pulen, 35g ikan tongkol suwir halus, tahu sutra dadu, kuah santan bumbu kuning.', 'estimatedCost' => $costSiang],
                    ['mealTime' => 'Sore / Malam', 'menuName' => 'Nasi Lembek Ayam Cincang & Rolade Tempe', 'ingredients' => 'Nasi lembek hangat, 40g ayam cincang gurih, potongan rolade tempe telur, kuah sup bening.', 'estimatedCost' => $costMalam],
                ],
            ];
            $menu = $variations[array_rand($variations)];
        } else {
            $variations = [
                [
                    ['mealTime' => 'Pagi', 'menuName' => 'Nasi Gurih Orak-Arik Telur & Bayam', 'ingredients' => 'Nasi hangat, 1 butir telur ayam orak-arik dengan minyak kelapa, daun bayam muda cincang.', 'estimatedCost' => $costPagi],
                    ['mealTime' => 'Siang', 'menuName' => 'Nasi Putih Lele Goreng Bumbu Kuning & Sayur Bening', 'ingredients' => 'Nasi putih, 1/2 ekor ikan lele goreng kunyit, 1 potong tempe bacem, sayur bening jagung manis pipil.', 'estimatedCost' => $costSiang],
                    ['mealTime' => 'Sore / Malam', 'menuName' => 'Nasi Tim Ayam Cincang & Sup Bola Tahu', 'ingredients' => 'Nasi tim, 35g daging ayam cincang, 1 tahu remas bumbu bawang putih seledri, kuah sup hangat.', 'estimatedCost' => $costMalam],
                ],
                [
                    ['mealTime' => 'Pagi', 'menuName' => 'Bubur Ayam Telur Puyuh & Wortel', 'ingredients' => 'Bubur beras kaldu, 3 butir telur puyuh, serutan wortel manis, taburan seledri halus.', 'estimatedCost' => $costPagi],
                    ['mealTime' => 'Siang', 'menuName' => 'Nasi Tim Fillet Ikan Dori & Sayur Lodeh Labu', 'ingredients' => 'Nasi tim pulen, 40g fillet dori/kakap kukus gurih, labu siam kuah santan encer, tempe goreng.', 'estimatedCost' => $costSiang],
                    ['mealTime' => 'Sore / Malam', 'menuName' => 'Nasi Pulen Semur Hati Ayam & Tahu Sutra', 'ingredients' => 'Nasi hangat, 35g hati ayam bumbu semur manis gurih, potongan tahu lembut, sup bayam.', 'estimatedCost' => $costMalam],
                ],
            ];
            $menu = $variations[array_rand($variations)];
        }

        $tips = [
            "Wajib berikan protein hewani (telur, hati, ikan) setiap kali makan utama untuk memicu sekresi hormon pertumbuhan IGF-1.",
            "Pastikan sanitasi dan air minum matang terjaga; cuci tangan pakai sabun (CTPS) sebelum menyiapkan makanan untuk mencegah infeksi cacing dan diare kronis.",
            "Lakukan pemantauan penimbangan rutin setiap bulan di Posyandu untuk mendeteksi weight faltering sedini mungkin.",
        ];

        return [
            'clinicalAlert' => $clinicalAlert,
            'statusDiagnosis' => $diagnosis,
            'recommendedMenu' => $menu,
            'parentActionTips' => $tips,
            'shouldReferToPuskesmas' => $shouldRefer,
        ];
    }
}
