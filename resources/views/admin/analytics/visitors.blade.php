@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden" x-transition></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : (document.documentElement.dir === 'rtl' ? 'translate-x-full' : '-translate-x-full')" 
           class="fixed md:sticky top-0 md:top-[72px] h-[calc(100vh)] md:h-[calc(100vh-72px)] w-72 bg-white shadow-2xl md:shadow-lg z-50 md:z-10 flex flex-col transition-transform duration-300 md:translate-x-0">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-[#6A8F3B]">🛡️</span> {{ __('Admin Panel') }}
            </h2>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto scrollbar-hide">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📊</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.analytics.visitors') }}" class="flex items-center gap-3 px-4 py-3 bg-[#6A8F3B]/10 text-[#6A8F3B] rounded-xl font-bold transition">
                <span class="text-xl">🌍</span> {{ __('Visitor Analytics') }}
            </a>
            <a href="{{ route('admin.analytics.marketing') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">📈</span> {{ app()->getLocale() === 'ar' ? 'تحليلات التسويق' : __('Marketing Analytics') }}
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#6A8F3B] rounded-xl font-bold transition group">
                <span class="text-xl group-hover:scale-110 transition">👥</span> {{ __('Manage Users') }}
            </a>
            <!-- other links can be added here or kept simple -->
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 w-full p-4 sm:p-8 min-w-0">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-[#6A8F3B] hover:text-[#5a7a2f] font-bold text-sm flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ __('Back to Dashboard') }}
                </a>
                <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-2">{{ __('Visitor Analytics') }}</h1>
                <p class="text-gray-600 font-medium">{{ __('Monitor platform traffic and device usage') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('admin.analytics.visitors') }}" method="GET">
                    <select name="period" onchange="this.form.submit()" class="bg-white border-gray-200 rounded-xl font-bold text-gray-700 shadow-sm focus:border-[#6A8F3B] focus:ring focus:ring-[#6A8F3B]/20">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>{{ __('Last 30 Days') }}</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>{{ __('Last 90 Days') }}</option>
                    </select>
                </form>
                <button @click="sidebarOpen = true" class="md:hidden p-3 bg-white rounded-xl shadow-md text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Top Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ __('Total Period Visitors') }}</div>
                    <div class="text-3xl font-black text-gray-900">{{ number_format($totalPeriod) }}</div>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">🌍</div>
            </div>
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ __('Today\'s Visitors') }}</div>
                    <div class="text-3xl font-black text-gray-900">{{ number_format($today) }}</div>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">📅</div>
            </div>
            <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="text-sm font-bold text-gray-500 mb-1">{{ __('Growth (vs Yesterday)') }}</div>
                    <div class="text-3xl font-black {{ $growth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                    </div>
                </div>
                <div class="w-12 h-12 {{ $growth >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-2xl flex items-center justify-center text-2xl">
                    {!! $growth >= 0 ? '📈' : '📉' !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Line Chart Area -->
            <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">📊</span> {{ __('Visitor Trend') }}
                </h2>
                @php 
                    // Fill missing days with 0 for the chart
                    $dates = [];
                    $counts = [];
                    for($i = $period - 1; $i >= 0; $i--) {
                        $d = \Carbon\Carbon::today()->subDays($i)->format('M d'); // e.g. May 19
                        $dates[] = $d;
                        $counts[] = $chartData->get(\Carbon\Carbon::today()->subDays($i)->toDateString(), 0);
                    }
                @endphp
                <div id="visitorsChart" class="mt-6 w-full min-h-[300px]"></div>

                <!-- Premium ApexCharts Integration -->
                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var options = {
                            series: [{
                                name: "{{ app()->getLocale() === 'ar' ? 'الزوار' : __('Visitors') }}",
                                data: @json($counts)
                            }],
                            chart: {
                                type: 'area',
                                height: 320,
                                fontFamily: 'inherit',
                                toolbar: { show: false },
                                zoom: { enabled: false },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 150
                                    },
                                    dynamicAnimation: {
                                        enabled: true,
                                        speed: 350
                                    }
                                }
                            },
                            colors: ['#6A8F3B'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.5,
                                    opacityTo: 0.05,
                                    stops: [0, 100]
                                }
                            },
                            dataLabels: { enabled: false },
                            stroke: {
                                curve: 'smooth',
                                width: 4
                            },
                            xaxis: {
                                categories: @json($dates),
                                tooltip: { enabled: false },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                                labels: {
                                    style: { colors: '#9CA3AF', fontWeight: 600 }
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: { colors: '#9CA3AF', fontWeight: 600 },
                                    formatter: function (val) { return Math.round(val); }
                                }
                            },
                            grid: {
                                borderColor: '#F3F4F6',
                                strokeDashArray: 5,
                                yaxis: { lines: { show: true } },
                                xaxis: { lines: { show: false } },
                            },
                            markers: {
                                size: 0,
                                colors: ['#fff'],
                                strokeColors: '#6A8F3B',
                                strokeWidth: 3,
                                hover: { size: 6 }
                            },
                            theme: { mode: 'light' }
                        };

                        var chart = new ApexCharts(document.querySelector("#visitorsChart"), options);
                        chart.render();
                    });
                </script>
            </div>

            <!-- Side Stats Area -->
            <div class="flex flex-col gap-8">
                <!-- Device Stats Area -->
                <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="text-2xl">📱</span> {{ __('Devices') }}
                    </h2>
                    <div class="space-y-4">
                        @php $totalDevices = $deviceStats->sum() ?: 1; @endphp
                        @foreach($deviceStats as $device => $count)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between text-sm font-bold">
                                <span class="text-gray-700">{{ $device ?: __('Unknown') }}</span>
                                <span class="text-gray-900">{{ number_format(($count / $totalDevices) * 100, 1) }}% ({{ $count }})</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ ($count / $totalDevices) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Country Stats Area -->
                <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-6 sm:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="text-2xl">🌍</span> {{ __('Countries') }}
                    </h2>
                    <div class="space-y-4">
                        @php $totalCountries = $countryStats->sum() ?: 1; @endphp
                        @foreach($countryStats as $country => $count)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between text-sm font-bold">
                                <span class="text-gray-700">{{ $country ?: __('Unknown') }}</span>
                                <span class="text-gray-900">{{ number_format(($count / $totalCountries) * 100, 1) }}% ({{ $count }})</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full" style="width: {{ ($count / $totalCountries) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs Table -->
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">👀</span> {{ __('Recent Visitors') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold">{{ __('IP Address') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('Location') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('Device') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('Hits') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('Last Visit') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('User Agent') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium divide-y divide-gray-100">
                        @forelse($recentVisitors as $v)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-gray-900 font-mono">{{ $v->ip_address ?? 'Hidden' }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                @if($v->country || $v->city)
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-bold text-gray-800">{{ $v->city ?? 'Unknown' }}, {{ $v->country ?? 'Unknown' }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $v->device }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-[#6A8F3B]/10 text-[#6A8F3B] px-2 py-1 rounded-lg font-bold">{{ $v->hits }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $v->updated_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-gray-400 text-xs truncate max-w-xs" title="{{ $v->user_agent }}">{{ $v->user_agent }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 font-bold">
                                {{ __('No visitors logged yet.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
@endsection
