<?php

namespace App\Http\Controllers;

use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    protected AiChatService $aiChatService;

    public function __construct(AiChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
    }

    /**
     * Handle incoming chat message and forward to Rust Ollama service.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $result = $this->aiChatService->askAssistant(
            $validated['prompt'],
            $validated['history'] ?? []
        );

        return response()->json($result);
    }
}
