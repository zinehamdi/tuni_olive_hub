<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Post Directives (Marketing Hooks per Facebook Post)
        Schema::create('facebook_post_directives', function (Blueprint $table) {
            $table->id();
            $table->string('post_id')->unique();
            $table->string('post_url')->nullable();
            $table->string('title')->nullable();
            $table->text('hook_goal')->nullable();
            $table->text('custom_prompt')->nullable();
            $table->string('target_action_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Bot Conversations (Facebook & WhatsApp Lead Tracking)
        Schema::create('bot_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->default('whatsapp'); // facebook_comment, facebook_dm, whatsapp
            $table->string('external_id')->index(); // Sender ID / Phone number / PSID
            $table->string('user_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('intent')->nullable(); // prices, buy, sell, mill, export, support, general
            $table->string('status')->default('automated'); // automated, human_takeover, closed
            $table->text('last_user_message')->nullable();
            $table->text('last_bot_reply')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 3. Bot Messages Log (Historical record of all interactions)
        Schema::create('bot_messages_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->index();
            $table->string('sender'); // user, bot, admin
            $table->text('message_text');
            $table->string('channel')->nullable();
            $table->integer('latency_seconds')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Custom Keyword Rules & Triggers
        Schema::create('bot_custom_rules', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('match_type')->default('contains'); // contains, exact
            $table->string('action_type')->default('reply_text'); // reply_text, send_link, escalate_admin
            $table->text('action_payload');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Bot Global Settings (General Tone, Persona Prompt, Delays)
        Schema::create('bot_global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed initial default global settings
        DB::table('bot_global_settings')->insert([
            [
                'key' => 'bot_enabled',
                'value' => '1',
                'description' => 'تفعيل أو تعطيل الرد الآلي للزيتوني بشكل كامل',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bot_system_prompt',
                'value' => 'أنت "الزيتوني" المساعد الذكي الرسمي لمنصة زين توب (ZinToop) - المنصة الوطنية الأولى لربط فلاحي الزيتون، المعاصر، التجار، والمصدرين في تونس.
أسلوبك: تونسي أصيل، مهذب، ودود، يتحدث بالدارجة التونسية الحية (مثال: عسلامة، يعيشك، مرحبا بيك خويا، ربي يباركلك في صابتك).
القواعد:
1. ابدأ دائما بترحيب خفيف وسؤال استكشافي قصير: "عسلامة ومرحباً بك في زين توب 🫒! كيفاش نجم نعاونك؟".
2. إذا كتب العميل بالدارجة أجب بالدارجة التونسية فورا، وإذا كتب بالفرنسية أو الإنجليزية أجب بلغته باحترافية.
3. قدم معلومات دقيقة عن أسعار اليوم ووجه العميل دائما لروابط المنصة المناسبة (تسجيل فلاح، تسجيل معصرة، سوق عروض الزيت بالجملة والمعلب، وجدول الأسعار الحية).
4. عند الصفقات الكبرى (كميات ضخمة / عقود تصدير) أو طلب التحدث مع الإدارة، اقترح التحويل البشري بلباقة.',
                'description' => 'التوجيه العام لشخصية ونبرة الزيتوني',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'comment_delay_min',
                'value' => '15',
                'description' => 'الحد الأدنى للتأخير البشري في التعليقات (بالثواني)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'comment_delay_max',
                'value' => '45',
                'description' => 'الحد الأقصى للتأخير البشري في التعليقات (بالثواني)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'admin_phone_alert',
                'value' => '+21625777926',
                'description' => 'رقم واتساب المدير لاستقبال إشعارات التدخل البشري',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Seed Europe distribution directive
        DB::table('facebook_post_directives')->insertOrIgnore([
            'post_id' => '1Bo7wTRviZ',
            'post_url' => 'https://www.facebook.com/share/p/1Bo7wTRviZ/',
            'title' => 'توزيع زيت الزيتون في أوروبا للجالية والتجار التوانسة',
            'hook_goal' => 'استقطاب التجار وأبناء الجالية التونسية في أوروبا (ألمانيا، فرنسا، سويسرا، بريطانيا، إيطاليا) لتوزيع زيت الزيتون التونسي، وطمأنتهم بأنهم يتعاملون مع شركات تونسية قانونية مسجلة ومقيمة في أوروبا بفواتير قانونية باليورو وتسليم محلي، مع أسعار وتخفيضات خاصة وحصرية بالجالية عبر اتفاقيات شراكة رسمية مع ZinToop.',
            'custom_prompt' => 'توجيهات الرد على هذا المنشور والخاص: 1) في التعليقات العامة رحب بالعميل وأكد توفر الزيت بأسعار جملة وتخفيضات حصرية للجالية واطلب التواصل بالخاص. 2) بالخاص أكد التعامل مع شركات تونسية قانونية مسجلة بأوروبا بفواتير باليورو وتسليم محلي. 3) اطرح أسئلة الفرز بلباقة لمعرفة الدولة والصفة القانونية والكمية. 4) عند الجدية اطلب الواتساب واختم بـ [ESCALATE_ADMIN] لتحويل الصفقة للإدارة.',
            'target_action_link' => 'https://zintoop.com/ar/services/pricing',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_global_settings');
        Schema::dropIfExists('bot_custom_rules');
        Schema::dropIfExists('bot_messages_log');
        Schema::dropIfExists('bot_conversations');
        Schema::dropIfExists('facebook_post_directives');
    }
};
