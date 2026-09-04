<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotConversation;
use App\Models\BotCustomRule;
use App\Models\BotMessageLog;
use App\Models\BotSetting;
use App\Models\FacebookPostDirective;
use Illuminate\Http\Request;

class BotManagerController extends Controller
{
    /**
     * Display the Bot Automation Dashboard
     */
    public function index()
    {
        $settings = BotSetting::all()->pluck('value', 'key');

        $directives = FacebookPostDirective::latest()->paginate(10, ['*'], 'directives_page');
        $rules = BotCustomRule::latest()->paginate(10, ['*'], 'rules_page');
        $conversations = BotConversation::with('messages')->latest('updated_at')->paginate(15, ['*'], 'conversations_page');

        $stats = [
            'total_conversations' => BotConversation::count(),
            'whatsapp_count' => BotConversation::where('channel', 'whatsapp')->count(),
            'facebook_count' => BotConversation::whereIn('channel', ['facebook_comment', 'facebook_dm'])->count(),
            'human_takeover_count' => BotConversation::where('status', 'human_takeover')->count(),
            'total_messages_sent' => BotMessageLog::where('sender', 'bot')->count(),
            'active_directives_count' => FacebookPostDirective::where('is_active', true)->count(),
        ];

        return view('admin.bot.index', compact('settings', 'directives', 'rules', 'conversations', 'stats'));
    }

    /**
     * Update Global Bot Settings & System Prompt
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'bot_enabled' => 'nullable|in:0,1',
            'bot_system_prompt' => 'required|string',
            'comment_delay_min' => 'required|integer|min:0|max:120',
            'comment_delay_max' => 'required|integer|min:1|max:300',
            'admin_phone_alert' => 'required|string',
        ]);

        BotSetting::set('bot_enabled', $request->has('bot_enabled') ? '1' : '0', 'حالة تشغيل البوت العامة');
        BotSetting::set('bot_system_prompt', $validated['bot_system_prompt'], 'التوجيه العام لشخصية ونبرة الزيتوني');
        BotSetting::set('comment_delay_min', (string) $validated['comment_delay_min'], 'الحد الأدنى للتأخير البشري');
        BotSetting::set('comment_delay_max', (string) $validated['comment_delay_max'], 'الحد الأقصى للتأخير البشري');
        BotSetting::set('admin_phone_alert', $validated['admin_phone_alert'], 'رقم واتساب المدير لاستقبال التنبيهات');

        return redirect()->back()->with('success', 'تم حفظ إعدادات وتوجيهات الزيتوني بنجاح!');
    }

    /**
     * Store a new Facebook Post Marketing Directive
     */
    public function storeDirective(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|string|unique:facebook_post_directives,post_id',
            'title' => 'required|string|max:255',
            'post_url' => 'nullable|url',
            'hook_goal' => 'required|string',
            'custom_prompt' => 'nullable|string',
            'target_action_link' => 'nullable|url',
        ]);

        $validated['is_active'] = $request->has('is_active');

        FacebookPostDirective::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة توجيه المنشور التسويقي بنجاح!');
    }

    /**
     * Delete a Facebook Post Directive
     */
    public function destroyDirective($id)
    {
        $directive = FacebookPostDirective::findOrFail($id);
        $directive->delete();

        return redirect()->back()->with('success', 'تم حذف توجيه المنشور بنجاح.');
    }

    /**
     * Store a new custom keyword rule
     */
    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
            'match_type' => 'required|in:contains,exact',
            'action_type' => 'required|in:reply_text,send_link',
            'action_payload' => 'required|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        BotCustomRule::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة القاعدة المخصصة بنجاح!');
    }

    /**
     * Delete a custom rule
     */
    public function destroyRule($id)
    {
        $rule = BotCustomRule::findOrFail($id);
        $rule->delete();

        return redirect()->back()->with('success', 'تم حذف القاعدة بنجاح.');
    }

    /**
     * Toggle Conversation Status (Automated <-> Human Takeover)
     */
    public function toggleConversationStatus($id)
    {
        $conversation = BotConversation::findOrFail($id);
        $conversation->status = ($conversation->status === 'human_takeover') ? 'automated' : 'human_takeover';
        $conversation->save();

        $statusName = $conversation->status === 'human_takeover' ? 'وضع التدخل البشري' : 'وضع الرد الآلي';
        return redirect()->back()->with('success', "تم تحويل المحادثة إلى {$statusName} بنجاح.");
    }
}
