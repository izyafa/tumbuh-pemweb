<?php

namespace App\Http\Controllers;

use App\Models\EducationalVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationalVideoController extends Controller
{
    public function index(Request $request): View
    {
        $categories = [
            'Semua Kategori',
            'MPASI 6-23 Bln',
            'ASI Eksklusif 0-6 Bln',
            'PHBS & Sanitasi',
            'Gizi Ibu Hamil',
        ];

        $selectedCategory = $request->input('category', 'Semua Kategori');
        $search = trim((string) $request->input('search', $request->input('q', '')));

        $query = EducationalVideo::query();
        if ($selectedCategory && $selectedCategory !== 'Semua Kategori') {
            $query->where('category', $selectedCategory);
        }

        if ($search !== '') {
            $searchTerm = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(summary) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(category) LIKE ?', [$searchTerm]);
            });
        }

        $videos = $query->orderBy('created_at', 'asc')->get();

        return view('edukasi.index', compact('videos', 'categories', 'selectedCategory', 'search'));
    }

    public function show(string $id): View
    {
        $video = EducationalVideo::findOrFail($id);
        $relatedVideos = EducationalVideo::where('id', '!=', $id)->limit(3)->get();

        return view('edukasi.show', compact('video', 'relatedVideos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'youtube_url' => ['required', 'url'],
            'summary' => ['required', 'string'],
            'quiz_data_raw' => ['nullable'],
            'quiz_data_json' => ['nullable'],
            'quizzes' => ['nullable', 'array'],
        ]);

        $quizData = $this->parseQuizData($request);

        EducationalVideo::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'youtube_url' => $validated['youtube_url'],
            'summary' => $validated['summary'],
            'quiz_data' => $quizData,
        ]);

        return redirect()->route('admin.videos')
            ->with('success', 'Modul video edukasi & kuis pemahaman berhasil ditambahkan.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $video = EducationalVideo::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'youtube_url' => ['required', 'url'],
            'summary' => ['required', 'string'],
            'quiz_data_raw' => ['nullable'],
            'quiz_data_json' => ['nullable'],
            'quizzes' => ['nullable', 'array'],
        ]);

        $quizData = $this->parseQuizData($request);

        $video->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'youtube_url' => $validated['youtube_url'],
            'summary' => $validated['summary'],
            'quiz_data' => $quizData,
        ]);

        return redirect()->route('admin.videos')
            ->with('success', 'Modul video edukasi & kuis pemahaman berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $video = EducationalVideo::findOrFail($id);
        $title = $video->title;
        $video->delete();

        return redirect()->route('admin.videos')
            ->with('success', "Modul video '{$title}' telah dihapus.");
    }

    /**
     * Parse and sanitize quiz questions from request.
     * Supports both visual builder array payload and JSON payload.
     *
     * @param Request $request
     * @return array|null
     */
    protected function parseQuizData(Request $request): ?array
    {
        $raw = null;

        if ($request->has('quizzes') && is_array($request->input('quizzes'))) {
            $raw = $request->input('quizzes');
        } elseif ($request->filled('quiz_data_json')) {
            $raw = json_decode($request->input('quiz_data_json'), true);
        } elseif ($request->filled('quiz_data_raw')) {
            $input = $request->input('quiz_data_raw');
            $raw = is_array($input) ? $input : json_decode($input, true);
        }

        if (!is_array($raw)) {
            return null;
        }

        $cleanQuizzes = [];

        foreach ($raw as $q) {
            if (!is_array($q)) {
                continue;
            }

            $questionText = trim((string) ($q['question'] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $rawOptions = $q['options'] ?? [];
            if (!is_array($rawOptions)) {
                continue;
            }

            $options = [];
            foreach ($rawOptions as $opt) {
                $trimmedOpt = trim((string) $opt);
                if ($trimmedOpt !== '') {
                    $options[] = $trimmedOpt;
                }
            }

            // A valid quiz question must have at least 2 distinct choices
            if (count($options) < 2) {
                continue;
            }

            $correctIndex = isset($q['correct_index']) ? (int) $q['correct_index'] : 0;
            if ($correctIndex < 0 || $correctIndex >= count($options)) {
                $correctIndex = 0;
            }

            $explanation = trim((string) ($q['explanation'] ?? ''));

            $cleanQuizzes[] = [
                'question' => $questionText,
                'options' => array_values($options),
                'correct_index' => $correctIndex,
                'explanation' => $explanation,
            ];
        }

        return !empty($cleanQuizzes) ? $cleanQuizzes : null;
    }
}
