<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAiService;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $openAiService;

    public function __construct(OpenAiService $openAiService)
    {
        $this->openAiService = $openAiService;
    }

    public function getChatResponse(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'prompt' => 'required|string',
            'conversation_id' => 'nullable|string',
            'role' => 'required|string',
            'prewritten_prompt' => 'nullable|boolean',
        ]);

        $userPrompt = $request->input('prompt');
        $prewrittenPrompt = $request->input('prewritten_prompt', false);
        $conversationId = $request->input('conversation_id');

        $prompt = $userPrompt;
        if ($prewrittenPrompt) {
            $prompt = " You are a teacher and you are helping me  learn how to study, i need you to help me learn and give them as much useful information. I am exploring the topic of " . $userPrompt . ". I want to learn through the inquiry learning method. Could you help me by answering some open-ended questions and guiding me through the process? Here are my initial questions:



            What are the key concepts and important aspects of " . $userPrompt . "?
            How does " . $userPrompt . " relate to real-world situations or other fields of study?
            What are some common misconceptions or challenges associated with understanding " . $userPrompt . "?
            Can you suggest some reliable resources or references where I can learn more about, if possible make them online sources " . $userPrompt . "?
            What are some critical questions I should consider asking to deepen my understanding  " . $userPrompt .  "?
            As I gather information, I might have more questions. Could you help me explore and connect new insights I discover along the way?
            Give answers without saying for example 'key concepts:' just write text.

            ";
        }

        $promptData = [
            'prompt' => $prompt,
            'role' => $request->input('role'),
            'model' => $request->input('model', 'gpt-4'),  // Use GPT-4 by default
        ];

        try {
            $response = $this->openAiService->getResponse($promptData, $conversationId);
            return response()->json(['response' => $response]);
        } catch (\Exception $e) {
            Log::error('ChatController error: ' . $e->getMessage());
            Log::error('ChatController trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }
}
