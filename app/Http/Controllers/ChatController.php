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
            $prompt = "You are a teacher and you are helping me learn how to study. I need you to help me learn and give me as much useful information as possible. I am exploring the topic of " . $userPrompt . ". I want to learn through the inquiry learning method. Could you help me by answering some open-ended questions and guiding me through the process? Here are my initial questions:

1. What are the key concepts and important aspects of " . $userPrompt . "?
2. How does " . $userPrompt . " relate to real-world situations or other fields of study?
3. What are some common misconceptions or challenges associated with understanding " . $userPrompt . "?
4. Can you suggest some reliable resources or references where I can learn more about " . $userPrompt . "? If possible, make them online sources.
5. What are some critical questions I should consider asking to deepen my understanding of " . $userPrompt . "?
6. As I gather information, I might have more questions. Could you help me explore and connect new insights I discover along the way?

Please provide a well-structured response with clear headings and bullet points where appropriate. Ensure that the information is broken down into digestible sections and use short paragraphs to enhance readability.

Use the following format for your response:
- **Title**: Use '<!--title-->' before the title text.
- **Link**: Use '<!--link-->' before the link text.
- **Question**: Use '<!--question-->' before the question text.
- **Answer**: Use '<!--answer-->' before the answer text.
- **Bullet Points**: Use '-' or '*' for bullet points.

  Please provide your response in HTML format. Each question should have a corresponding title and content. Ensure that the information is well-structured and broken down into digestible sections. Here is an example of the format you should use:

   make sure to make it not write classes as text and things like that and make it so it puts a title and content for every question
Make title bold and bigger letters make so every section is in a div so in that div is a title and content

okay so don't put h3 put h1 and on h1 writ inline class text-2xl and font-bold put pb-5

can you make it so the div that will have h1 and all of it  to have padding around it


  "

            ;

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
