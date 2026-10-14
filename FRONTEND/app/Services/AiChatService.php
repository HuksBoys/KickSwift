<?php

namespace App\Services;

use App\Models\Field;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected string $rustMicroserviceUrl;

    public function __construct()
    {
        $this->rustMicroserviceUrl = env('RUST_AI_SERVICE_URL', 'http://127.0.0.1:5000');
    }

    /**
     * Send user message to Rust AI Microservice with rich database context.
     *
     * @param string $prompt
     * @param array|null $history
     * @return array
     */
    public function askAssistant(string $prompt, ?array $history = null): array
    {
        $fieldContext = $this->buildDatabaseContext();

        try {
            $response = Http::timeout(45)->post("{$this->rustMicroserviceUrl}/api/v1/chat", [
                'prompt' => $prompt,
                'history' => $history ?? [],
                'field_context' => $fieldContext,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Rust AI microservice returned non-200 status', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => 'error',
                'response' => 'AI Microservice sedang sibuk atau merespons lambat. Silakan coba kembali sesaat lagi.',
                'suggested_intent' => null,
                'model_used' => 'fallback',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to communicate with Rust AI Microservice: ' . $e->getMessage());

            // Intelligent fallback jika Rust microservice / Ollama belum di-start
            return [
                'status' => 'success',
                'response' => $this->generateRuleBasedFallbackResponse($prompt),
                'suggested_intent' => 'VIEW_FIELDS',
                'model_used' => 'kickswift-rule-engine-fallback',
            ];
        }
    }

    /**
     * Build dynamic database context string for the LLM.
     *
     * @return string
     */
    protected function buildDatabaseContext(): string
    {
        $today = Carbon::today()->format('Y-m-d');
        $fields = Field::where('is_active', true)->get();

        $contextLines = ["Daftar Lapangan Aktif di KickSwift:"];

        foreach ($fields as $field) {
            $typeLabel = match ($field->type) {
                'vinyl' => 'Vinyl Pro (Standar Internasional, Empuk & Aman Sendi)',
                'synthetic_grass' => 'Rumput Sintetis Monofilamen (Sensasi Sepak Bola Alami)',
                'interlocking' => 'Modular Interlocking (Anti-Slip, Pantulan Cepat & Presisi)',
                default => ucfirst($field->type),
            };

            $contextLines[] = "- Lapangan: {$field->name} ({$typeLabel})";
            $contextLines[] = "  Tarif Reguler: Rp " . number_format($field->price_per_hour, 0, ',', '.') . "/jam (Jam 08:00 - 18:00)";
            $contextLines[] = "  Tarif Malam (Peak Hour): Rp " . number_format($field->price_per_hour + 25000, 0, ',', '.') . "/jam (Jam 18:00 - 23:00)";
            $contextLines[] = "  Deskripsi: {$field->description}";
        }

        $contextLines[] = "Jam Operasional: Setiap hari pukul 08:00 - 23:00 WIB.";
        $contextLines[] = "Metode Pembayaran: QRIS (Semua E-Wallet/BCA/Mandiri/BRI), Transfer Bank, dan Bayar di Tempat.";

        return implode("\n", $contextLines);
    }

    /**
     * Rule-based fallback if LLM microservice is temporarily unreachable.
     *
     * @param string $prompt
     * @return string
     */
    protected function generateRuleBasedFallbackResponse(string $prompt): string
    {
        $p = strtolower($prompt);

        if (str_contains($p, 'harga') || str_contains($p, 'biaya') || str_contains($p, 'tarif')) {
            return "Tarif sewa lapangan KickSwift: Lapangan Interlocking (Rp 130.000/jam), Vinyl Pro (Rp 150.000/jam), dan Rumput Sintetis (Rp 175.000/jam). Untuk jam malam (18:00-23:00) ada penyesuaian tarif +Rp 25.000/jam.";
        }

        if (str_contains($p, 'rumput') || str_contains($p, 'sintetis')) {
            return "KickSwift menyediakan Lapangan Arena 2 (Rumput Sintetis Monofilamen Premium) dengan rubber infill empuk seharga Rp 175.000/jam. Sangat cocok bagi Anda yang menyukai kontrol bola empuk.";
        }

        if (str_contains($p, 'vinyl')) {
            return "KickSwift Arena 1 menggunakan lantai Vinyl Pro berstandar internasional dengan shock absorber untuk keamanan lutut dan akselerasi lincah seharga Rp 150.000/jam.";
        }

        return "Halo! Saya KickSwift AI Assistant. Anda dapat memesan Lapangan Vinyl Pro, Rumput Sintetis, atau Interlocking Floor. Jam operasional kami dari pukul 08:00 hingga 23:00 WIB. Silakan pilih lapangan yang Anda inginkan di katalog!";
    }
}
