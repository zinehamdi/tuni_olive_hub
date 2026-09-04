@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ activeTab: 'settings', sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside class="w-full md:w-72 bg-white shadow-lg z-10 flex flex-col p-4 border-l border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-2xl">🤖</span> {{ app()->getLocale() === 'ar' ? 'أتمتة الزيتوني الذكي' : 'Ezzitouni Bot Manager' }}
            </h2>
        </div>
        <nav class="mt-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-50 rounded-xl font-bold transition">
                <span>📊</span> {{ app()->getLocale() === 'ar' ? 'لوحة التحكم العامة' : 'Admin Dashboard' }}
            </a>
            <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-[#6A8F3B]/10 text-[#6A8F3B] font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-start flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
                <span>⚙️</span> {{ app()->getLocale() === 'ar' ? 'شخصية وسلوك الزيتوني' : 'Global Settings & Persona' }}
            </button>
            <button @click="activeTab = 'directives'" :class="activeTab === 'directives' ? 'bg-[#6A8F3B]/10 text-[#6A8F3B] font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-start flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
                <span>🎯</span> {{ app()->getLocale() === 'ar' ? 'أهداف وتوجيهات المنشورات' : 'Facebook Post Hooks' }}
            </button>
            <button @click="activeTab = 'conversations'" :class="activeTab === 'conversations' ? 'bg-[#6A8F3B]/10 text-[#6A8F3B] font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-start flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
                <span>💬</span> {{ app()->getLocale() === 'ar' ? 'المحادثات والتدخل البشري' : 'Live Chats & Handoff' }}
            </button>
            <button @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'bg-[#6A8F3B]/10 text-[#6A8F3B] font-bold' : 'text-gray-600 hover:bg-gray-50'" class="w-full text-start flex items-center gap-3 px-4 py-2.5 rounded-xl transition">
                <span>⚡</span> {{ app()->getLocale() === 'ar' ? 'الكلمات المفتاحية السريعة' : 'Custom Keyword Rules' }}
            </button>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-8 space-y-6 max-w-7xl mx-auto w-full">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-center gap-3 font-semibold shadow-sm">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <!-- KPI Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 font-bold">{{ app()->getLocale() === 'ar' ? 'إجمالي المحادثات' : 'Total Conversations' }}</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $stats['total_conversations'] }}</div>
                </div>
                <div class="text-3xl p-3 bg-blue-50 text-blue-600 rounded-2xl">📱</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 font-bold">{{ app()->getLocale() === 'ar' ? 'محادثات واتساب' : 'WhatsApp Chats' }}</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['whatsapp_count'] }}</div>
                </div>
                <div class="text-3xl p-3 bg-emerald-50 text-emerald-600 rounded-2xl">🟢</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 font-bold">{{ app()->getLocale() === 'ar' ? 'فيسبوك وتعليقات' : 'Facebook & DMs' }}</div>
                    <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['facebook_count'] }}</div>
                </div>
                <div class="text-3xl p-3 bg-indigo-50 text-indigo-600 rounded-2xl">🌐</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 font-bold">{{ app()->getLocale() === 'ar' ? 'تحت التدخل البشري' : 'Human Takeover' }}</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['human_takeover_count'] }}</div>
                </div>
                <div class="text-3xl p-3 bg-amber-50 text-amber-600 rounded-2xl">👤</div>
            </div>
        </div>

        <!-- TAB 1: Global Settings & Persona -->
        <div x-show="activeTab === 'settings'" class="space-y-6">
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'توجيهات وشخصية الزيتوني العامة' : 'Global Personality & System Prompt' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'تعديل نبرة الصوت، اللهجة التونسية، والتعليمات الأساسية للبوت في كافة القنوات.' : 'Configure global persona and system instructions.' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.bot.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <input type="checkbox" name="bot_enabled" value="1" id="bot_enabled" {{ ($settings['bot_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="w-5 h-5 text-[#6A8F3B] rounded focus:ring-[#6A8F3B]">
                        <label for="bot_enabled" class="font-bold text-gray-900 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'تفعيل الرد الآلي للزيتوني على فيسبوك وواتساب' : 'Enable Ezzitouni Auto-replies across Facebook & WhatsApp' }}
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            {{ app()->getLocale() === 'ar' ? 'التعليمات والشخصية الأساسية (System Prompt):' : 'System Prompt & Persona Instructions:' }}
                        </label>
                        <textarea name="bot_system_prompt" rows="8" class="w-full p-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#6A8F3B] focus:border-transparent font-sans text-sm leading-relaxed">{{ $settings['bot_system_prompt'] ?? '' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'أدنى تأخير للتعليقات (ثواني):' : 'Min Comment Delay (sec):' }}</label>
                            <input type="number" name="comment_delay_min" value="{{ $settings['comment_delay_min'] ?? 15 }}" min="0" max="120" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'أقصى تأخير للتعليقات (ثواني):' : 'Max Comment Delay (sec):' }}</label>
                            <input type="number" name="comment_delay_max" value="{{ $settings['comment_delay_max'] ?? 45 }}" min="1" max="300" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                        </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                {{ app()->getLocale() === 'ar' ? 'معرف الصفحة على فيسبوك (Page ID):' : 'Facebook Page ID:' }}
                            </label>
                            <input type="text" name="meta_page_id" value="{{ $settings['meta_page_id'] ?? '828942590302317' }}" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                {{ app()->getLocale() === 'ar' ? 'رمز وصول الصفحة (Page Access Token):' : 'Facebook Page Access Token:' }}
                            </label>
                            <input type="password" name="meta_page_access_token" value="{{ $settings['meta_page_access_token'] ?? '' }}" placeholder="EAAVQsbT0n7oBS..." class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]" dir="ltr">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-[#6A8F3B] hover:bg-[#587731] text-white font-bold rounded-2xl shadow-lg shadow-[#6A8F3B]/20 transition flex items-center gap-2">
                            <span>💾</span> {{ app()->getLocale() === 'ar' ? 'حفظ وتحديث الإعدادات' : 'Save Settings' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 2: Facebook Post Directives & Hooks -->
        <div x-show="activeTab === 'directives'" class="space-y-6">
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-gray-100 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ app()->getLocale() === 'ar' ? 'إضافة هدف تسويقي لمنشور فيسبوك (Hook Directive)' : 'Add Post Marketing Directive' }}</h3>
                <form action="{{ route('admin.bot.directives.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'معرف المنشور على فيسبوك (Post ID):' : 'Facebook Post ID:' }}</label>
                        <input type="text" name="post_id" placeholder="مثال: 828942590302317_123456789" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'عنوان تعريفي للمنشور:' : 'Post Title / Topic:' }}</label>
                        <input type="text" name="title" placeholder="مثال: منشور أسعار افتتاح موسم الزيتون" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'رابط المنشور (للمعاينة):' : 'Post URL:' }}</label>
                        <input type="url" name="post_url" placeholder="https://facebook.com/..." class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الرابط المطلوب تضمينه بالردود:' : 'Target Action Link:' }}</label>
                        <input type="url" name="target_action_link" placeholder="https://zintoop.com/ar/prices" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الهدف التسويقي السري من المنشور (Hook Goal):' : 'Marketing Goal / Hook:' }}</label>
                        <textarea name="hook_goal" rows="2" placeholder="مثال: هذا المنشور استفزازي لمعرفة ردة فعل الفلاحين، مهمتك توجيههم لجدول الأسعار الحية وتشجيعهم على التسجيل." required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B] text-sm"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'تعليمات خاصة للزيتوني عند الرد على هذا المنشور:' : 'Custom Prompt for this Post:' }}</label>
                        <textarea name="custom_prompt" rows="2" placeholder="مثال: لا تذكر السعر مباشرة، بل شجع المتابع على فتح الرابط ليتحقق من أسعار معاصر ولايته." class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B] text-sm"></textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-sm text-gray-700">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-[#6A8F3B] rounded">
                            {{ app()->getLocale() === 'ar' ? 'تفعيل التوجيه فوراً' : 'Activate Directive Immediately' }}
                        </label>
                        <button type="submit" class="px-5 py-2.5 bg-[#6A8F3B] hover:bg-[#587731] text-white font-bold rounded-xl shadow-md transition">
                            <span>➕</span> {{ app()->getLocale() === 'ar' ? 'إضافة التوجيه' : 'Add Directive' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Directives Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-900">
                    {{ app()->getLocale() === 'ar' ? 'قائمة توجيهات المنشورات المسجلة' : 'Active Post Directives' }}
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-start">
                        <thead class="bg-gray-50 text-gray-600 font-bold text-xs uppercase">
                            <tr>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'المنشور' : 'Post' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الهدف التسويقي (Hook)' : 'Hook Goal' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الرابط المرفق' : 'Target Link' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($directives as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $d->title }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $d->post_id }}</div>
                                </td>
                                <td class="p-4 text-gray-700 max-w-xs truncate">{{ $d->hook_goal }}</td>
                                <td class="p-4 font-mono text-xs text-blue-600 truncate max-w-[150px]">
                                    @if($d->target_action_link)
                                        <a href="{{ $d->target_action_link }}" target="_blank" class="hover:underline">{{ $d->target_action_link }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $d->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $d->is_active ? 'مفعل' : 'معطل' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <form action="{{ route('admin.bot.directives.destroy', $d->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التوجيه؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs">🗑️ حذف</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-medium">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد توجيهات خاصة مضافة حالياً. يعمل البوت بالتوجيه العام لجميع المنشورات.' : 'No active directives.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: Live Chats & Human Takeover -->
        <div x-show="activeTab === 'conversations'" class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'سجل المحادثات والعملاء المحتملين (Live Leads)' : 'Live Chats & Lead Inbox' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'يمكنك تحويل أي محادثة للتدخل البشري بضغطة زر واحدة لإيقاف الرد الآلي ومتابعتها شخصياً.' : 'Toggle human takeover mode per client.' }}</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-start">
                        <thead class="bg-gray-50 text-gray-600 font-bold text-xs uppercase">
                            <tr>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'القناة / العميل' : 'Channel / Client' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'آخر رسالة من العميل' : 'Last User Message' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'آخر رد من الزيتوني' : 'Last Bot Reply' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'النية المستخرجة' : 'Intent' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'وضع التحكم' : 'Status & Mode' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($conversations as $c)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">
                                            @if($c->channel === 'whatsapp') 🟢 @elseif($c->channel === 'facebook_comment') 💬 @else 🌐 @endif
                                        </span>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $c->user_name ?? ($c->phone_number ?? $c->external_id) }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $c->channel }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-gray-800 max-w-xs truncate">{{ $c->last_user_message ?? '-' }}</td>
                                <td class="p-4 text-gray-500 max-w-xs truncate">{{ $c->last_bot_reply ?? '-' }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ $c->intent ?? 'عام' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <form action="{{ route('admin.bot.conversations.toggle', $c->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $c->status === 'human_takeover' ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}">
                                            @if($c->status === 'human_takeover')
                                                <span>👤</span> {{ app()->getLocale() === 'ar' ? 'تحت التدخل البشري' : 'Human Takeover' }}
                                            @else
                                                <span>🤖</span> {{ app()->getLocale() === 'ar' ? 'رد آلي مفعل' : 'Automated' }}
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-medium">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد محادثات مسجلة حتى الآن. ستظهر المحادثات هنا فور بدء تفاعل المتابعين.' : 'No conversations recorded yet.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: Custom Keyword Rules -->
        <div x-show="activeTab === 'rules'" class="space-y-6">
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-gray-100 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-4">{{ app()->getLocale() === 'ar' ? 'إضافة كلمة مفتاحية ورد فوري' : 'Add Custom Keyword Trigger' }}</h3>
                <form action="{{ route('admin.bot.rules.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الكلمة المفتاحية:' : 'Keyword:' }}</label>
                        <input type="text" name="keyword" placeholder="مثال: تحليل مخبري أو ONH" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع المطابقة:' : 'Match Type:' }}</label>
                        <select name="match_type" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                            <option value="contains">{{ app()->getLocale() === 'ar' ? 'تحتوي على الكلمة' : 'Contains' }}</option>
                            <option value="exact">{{ app()->getLocale() === 'ar' ? 'تطابق تام' : 'Exact Match' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع الإجراء:' : 'Action Type:' }}</label>
                        <select name="action_type" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B]">
                            <option value="reply_text">{{ app()->getLocale() === 'ar' ? 'رد بنص أو رابط فوري' : 'Reply with Text/Link' }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">{{ app()->getLocale() === 'ar' ? 'الرد الفوري المعتمد:' : 'Action Response Payload:' }}</label>
                        <textarea name="action_payload" rows="2" placeholder="مثال: مرحباً بك! يمكنك حجز موعد تحليل عينات زيت الزيتون في المخابر المعتمدة عبر الرابط التالي: https://zintoop.com/ar/quality-lab" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-[#6A8F3B] text-sm"></textarea>
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-[#6A8F3B] hover:bg-[#587731] text-white font-bold rounded-xl shadow-md transition">
                            <span>➕</span> {{ app()->getLocale() === 'ar' ? 'حفظ القاعدة' : 'Save Rule' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Rules Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-900">
                    {{ app()->getLocale() === 'ar' ? 'القواعد والكلمات المفتاحية النشطة' : 'Active Keyword Triggers' }}
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-start">
                        <thead class="bg-gray-50 text-gray-600 font-bold text-xs uppercase">
                            <tr>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الكلمة المفتاحية' : 'Keyword' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'نوع المطابقة' : 'Match Type' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الرد الفوري' : 'Reply Payload' }}</th>
                                <th class="p-4">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($rules as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 font-bold text-gray-900">{{ $r->keyword }}</td>
                                <td class="p-4 text-gray-500 font-mono text-xs">{{ $r->match_type }}</td>
                                <td class="p-4 text-gray-700 max-w-md truncate">{{ $r->action_payload }}</td>
                                <td class="p-4">
                                    <form action="{{ route('admin.bot.rules.destroy', $r->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه القاعدة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs">🗑️ حذف</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400 font-medium">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد قواعد مخصصة مضافة حالياً.' : 'No custom rules yet.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection
