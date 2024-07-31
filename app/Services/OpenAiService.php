<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);
    }

    public function getResponse($prompt, $conversationId = null)
    {
        $filePath = storage_path("conversations/{$conversationId}.txt");


        if ($conversationId && file_exists($filePath)) {
            $conversation = json_decode(file_get_contents($filePath), true);
        } else {

            $conversation = [
                ["role" => 'system', "content" => $prompt['role']]
            ];
        }


        $conversation[] = ["role" => 'user', "content" => $prompt['prompt']];

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $prompt['model'] ?? 'gpt-4',  // Default to GPT-4
                    'messages' => $conversation,
                    'temperature' => 0.7,
                ],
            ]);

            Log::debug('OpenAI API request succeeded: ' . $response->getBody());

            $data = json_decode($response->getBody(), true);
            $modelResponse = end($data['choices'])['message']['content'];


            $conversation[] = ["role" => 'assistant', "content" => $modelResponse];

            file_put_contents($filePath, json_encode($conversation));

            return $modelResponse;

        } catch (\Exception $e) {
            Log::error('OpenAI API request failed: ' . $e->getMessage());
            Log::error('OpenAI API request failed: ' . $e->getTraceAsString());
            return 'An error occurred while processing your request.';
        }
    }
}
