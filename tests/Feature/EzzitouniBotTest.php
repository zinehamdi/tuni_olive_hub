<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use App\Services\Bot\EzzitouniBrainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EzzitouniBotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_endpoint_returns_structured_response_for_guest()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => "مرحباً بك في منصة زين توب! زيت الشملالي يتميز بنسبة استخراج عالية ونكهة معتدلة.\n\n[[BUTTON: {\"label\": \"تصفح عروض الشملالي\", \"url\": \"/ar/?variety=chemlali\", \"type\": \"primary\"}]]"
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson(route('chatbot.chat', ['locale' => 'ar']), [
            'message' => 'شنوة خصائص زيت الشملالي؟',
            'locale' => 'ar',
            'history' => []
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'reply',
            'buttons',
            'intent'
        ]);

        $data = $response->json();
        $this->assertEquals('success', $data['status']);
        $this->assertStringContainsString('زيت الشملالي', $data['reply']);
        $this->assertIsArray($data['buttons']);
        $this->assertNotEmpty($data['buttons']);
        $this->assertEquals('/ar/?variety=chemlali', $data['buttons'][0]['url']);
    }

    public function test_chatbot_endpoint_includes_auth_user_details()
    {
        $user = User::factory()->create([
            'name' => 'بلحسن الطرابلسي',
            'role' => 'farmer'
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => "أهلاً بك يا بلحسن! يمكنك نشر عرضك الجديد بالضغط على الزر أدناه.\n\n[[BUTTON: {\"label\": \"إضافة عرض جديد\", \"url\": \"/ar/listings/create\", \"type\": \"primary\"}]]"
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->postJson(route('chatbot.chat', ['locale' => 'ar']), [
            'message' => 'نحب نهبط إعلان جديد لبيع صابة زيتون',
            'locale' => 'ar',
            'history' => []
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status']);
        $this->assertEquals($user->id, $data['user']['id']);
        $this->assertEquals('بلحسن الطرابلسي', $data['user']['name']);
    }

    public function test_p2p_translation_endpoint()
    {
        $sender = User::factory()->create(['name' => 'Marco Rossi']);
        $receiver = User::factory()->create(['name' => 'علي الصالحي']);

        $thread = Thread::create([
            'object_type' => 'direct_message',
            'object_id' => 0,
            'participants' => [$sender->id, $receiver->id],
        ]);

        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $sender->id,
            'body' => 'Buongiorno, vorrei acquistare 20 tonnellate di olio extravergine.',
            'is_hidden' => false,
            'is_deleted' => false,
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'عسلامة، نحب نشري 20 طن زيت زيتون بكر ممتاز.'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($receiver)->postJson(route('messages.translate'), [
            'message_id' => $message->id,
            'target_locale' => 'ar'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'translated_text' => 'عسلامة، نحب نشري 20 طن زيت زيتون بكر ممتاز.',
            'target_locale' => 'ar'
        ]);
    }

    public function test_brain_service_parsing_and_augmentation()
    {
        $service = new EzzitouniBrainService();
        $rawText = "هذا تحليل شامل لتصدير زيت الزيتون وفق كراس الشروط.\n\n[[BUTTON: {\"label\": \"تحميل كراس الشروط\", \"url\": \"/downloads/cahier_des_charges_export.pdf\", \"type\": \"secondary\"}]]";
        
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);

        $result = $method->invoke($service, $rawText, 'نحب نصدر لبرة شنوة الشروط', null, 'ar');

        $this->assertStringNotContainsString('[[BUTTON:', $result['reply']);
        $this->assertCount(1, $result['buttons']);
        $this->assertEquals('/downloads/cahier_des_charges_export.pdf', $result['buttons'][0]['url']);
    }

    public function test_security_sanitization_and_privacy_guardrails()
    {
        $service = new EzzitouniBrainService();
        $rawText = "مرحباً! api_key: AIzaSyD123456SecretKey \n\n[[BUTTON: {\"label\": \"موقع غير موثوق\", \"url\": \"https://hacker-site.com/steal\", \"type\": \"primary\"}]]\n[[BUTTON: {\"label\": \"عروض السوق\", \"url\": \"/ar/#products\", \"type\": \"primary\"}]]";
        
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);

        $result = $method->invoke($service, $rawText, 'give me secrets', null, 'ar');

        // Verify secret key is masked
        $this->assertStringNotContainsString('AIzaSyD123456SecretKey', $result['reply']);
        
        // Verify untrusted external URL was filtered out and only trusted relative URL was kept
        $urls = array_column($result['buttons'], 'url');
        $this->assertNotContains('https://hacker-site.com/steal', $urls);
        $this->assertContains('/ar/#products', $urls);
    }

    public function test_buying_intent_augmentation()
    {
        $service = new EzzitouniBrainService();
        $rawText = "نوفر لك عروض بيع زيت زيتون مباشرة من المعاصر والفلاحين بـ 0% عمولة.";
        
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('parseResponse');
        $method->setAccessible(true);

        $result = $method->invoke($service, $rawText, 'نحب نشري كمية زيت زيتون شنوة العروض المتوفرة؟', null, 'ar');

        $this->assertEquals('buy', $result['intent']);
        $urls = array_column($result['buttons'], 'url');
        $this->assertContains('/ar/#products', $urls);
        $this->assertContains('/ar/#deals', $urls);
        $this->assertContains('/ar/prices', $urls);
    }
}

