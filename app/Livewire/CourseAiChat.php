<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourseAiChat extends Component
{
    public $courseSlug;
    public $courseContext = '';
    public $messages = [];
    public $newMessage = '';
    public $isOpen = false;

    protected $listeners = ['toggleAiChat' => 'toggleChat'];

    public function mount($course)
    {
        $this->courseSlug = $course['slug'] ?? 'course';
        
        $title = $course['title'] ?? 'Course';
        $recap = $course['recap'] ?? '';
        $concepts = implode(', ', $course['concepts'] ?? []);
        
        $this->courseContext = "You are an AI learning assistant exclusively for the course titled '{$title}'. " .
                               "Course Summary: {$recap}. Key Concepts covered: {$concepts}. " .
                               "Your only job is to answer questions related to this specific course and its concepts. " .
                               "If a user asks a question entirely unrelated to the course topic, politely decline to answer, " .
                               "stating that you are here specifically to help with '{$title}'. " .
                               "Keep your answers concise, helpful, and educational.";

        $this->messages[] = [
            'role' => 'assistant',
            'content' => "Hi! I'm your AI assistant for '{$title}'. Ask me anything about the course materials!"
        ];
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000'
        ]);

        $userMessage = $this->newMessage;
        
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];
        
        $this->newMessage = '';

        $this->callGeminiApi();
    }

    protected function callGeminiApi()
    {
        $apiKey = config('services.gemini.key');
        
        if (empty($apiKey)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "Error: Gemini API key is not configured in .env (GEMINI_API_KEY)."
            ];
            return;
        }

        $contents = [];
        foreach ($this->messages as $msg) {
            $role = $msg['role'] === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content']]
                ]
            ];
        }

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $this->courseContext]
                ]
            ],
            'contents' => $contents,
        ];

        try {
            $response = Http::timeout(15)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiText = $data['candidates'][0]['content']['parts'][0]['text'];
                    $this->messages[] = [
                        'role' => 'assistant',
                        'content' => $aiText
                    ];
                } else {
                    $this->messages[] = [
                        'role' => 'assistant',
                        'content' => "I received an unexpected response format from the server."
                    ];
                }
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => "Sorry, I couldn't connect to the AI service right now."
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "Sorry, an error occurred while processing your request."
            ];
        }
    }

    public function render()
    {
        return view('livewire.course-ai-chat');
    }
}
