<div id="leadCaptureModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300 opacity-0 pointer-events-none">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0" id="leadCaptureContent">
        <!-- Header Image / Banner -->
        <div class="h-32 bg-green-700 rounded-t-xl flex items-center justify-center relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-green-600 rounded-full opacity-50"></div>
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-green-800 rounded-full opacity-50"></div>
            
            <button id="closeLeadModal" class="absolute top-3 right-3 text-white/80 hover:text-white transition bg-black/20 hover:bg-black/40 rounded-full w-8 h-8 flex items-center justify-center z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="text-white text-2xl font-bold z-10 text-center px-4">{{ __('Join 14,000+ Professionals') }}</h2>
        </div>

        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('Get the Free Zintoop Guide & Weekly Market Updates!') }}</h3>
            <p class="text-gray-600 text-sm mb-6">{{ __('Learn how to track transporters, negotiate deals, and get the latest olive prices directly to your inbox or phone.') }}</p>

            <form id="leadCaptureForm" class="space-y-4">
                @csrf
                
                <!-- Toggle -->
                <div class="text-sm font-bold text-gray-700 mb-2">{{ __('Choose your preferred method:') }}</div>
                <div class="flex items-center justify-between bg-gray-100 p-1 rounded-lg relative">
                    <button type="button" id="tabWhatsapp" class="flex-1 py-2 text-sm font-bold rounded-md bg-white shadow text-green-600 hover:text-green-700 hover:bg-green-50 transition z-10">{{ __('WhatsApp') }}</button>
                    
                    <!-- OR Badge -->
                    <div class="absolute left-1/2 -translate-x-1/2 z-20 bg-gray-200 text-gray-500 rounded-full px-2 py-0.5 text-xs font-bold border-2 border-white pointer-events-none">
                        {{ __('or') }}
                    </div>

                    <button type="button" id="tabEmail" class="flex-1 py-2 text-sm font-bold rounded-md text-gray-500 hover:text-green-700 hover:bg-green-50 transition z-10">{{ __('Email') }}</button>
                </div>

                <input type="hidden" name="type" id="contactType" value="whatsapp">

                <!-- Input Group -->
                <div id="inputWrapper">
                    <label for="contactValue" class="block text-sm font-medium text-gray-700 mb-1" id="contactLabel">{{ __('WhatsApp Number') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" id="inputIcon">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.128.552 4.135 1.523 5.918L.004 24l6.236-1.636a11.967 11.967 0 005.791 1.492h.004c6.645 0 12.03-5.385 12.03-12.031C24.065 5.385 18.68 0 12.031 0zm0 21.84c-1.802 0-3.564-.485-5.111-1.401l-.366-.217-3.799.996 1.015-3.704-.239-.38C2.569 15.485 2.016 13.785 2.016 12.03 2.016 6.505 6.51 2.01 12.035 2.01c2.677 0 5.193 1.043 7.085 2.936A9.972 9.972 0 0122.052 12.03c0 5.524-4.494 10.02-10.02 10.02v-.01zm5.496-7.502c-.301-.151-1.782-.879-2.058-.979-.276-.1-.478-.151-.678.151-.201.301-.778.979-.953 1.18-.175.201-.35.226-.652.075-.301-.151-1.272-.469-2.423-1.496-.895-.8-1.501-1.787-1.677-2.088-.175-.301-.019-.464.131-.614.136-.136.301-.351.452-.526.151-.176.201-.301.301-.502.101-.201.05-.376-.025-.526-.075-.151-.678-1.634-.928-2.237-.243-.591-.49-.51-.678-.519-.175-.01-.377-.01-.578-.01-.201 0-.527.075-.803.376-.276.301-1.054 1.03-1.054 2.508 0 1.479 1.079 2.909 1.23 3.109.15.201 2.118 3.232 5.13 4.531 2.457 1.06 2.96 1.058 3.565.98.665-.084 1.782-.729 2.032-1.433.251-.703.251-1.306.175-1.433-.075-.126-.276-.201-.578-.352z"></path></svg>
                        </div>
                        <input type="tel" name="contact_value" id="contactValue" class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 py-2 border" placeholder="+216 20 123 456" required>
                    </div>
                </div>
                
                <div id="formError" class="text-red-500 text-sm hidden"></div>
                <div id="formSuccess" class="text-green-600 text-sm font-medium hidden">{{ __('Success! Check your messages soon.') }}</div>

                <button type="submit" id="submitLead" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200 flex justify-center items-center">
                    <span>{{ __('Send Me The Guide') }}</span>
                </button>

            </form>

            <div class="mt-6 text-center">
                <span class="text-gray-500 text-sm">{{ __('Already ready to trade?') }}</span>
                <a href="{{ route('register') }}" class="text-green-600 font-medium hover:text-green-800 text-sm ml-1 transition">{{ __('Create a Full Account') }}</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('leadCaptureModal');
    const content = document.getElementById('leadCaptureContent');
    const closeBtn = document.getElementById('closeLeadModal');
    
    // Tabs
    const tabEmail = document.getElementById('tabEmail');
    const tabWhatsapp = document.getElementById('tabWhatsapp');
    const contactType = document.getElementById('contactType');
    const contactLabel = document.getElementById('contactLabel');
    const contactValue = document.getElementById('contactValue');
    const inputIcon = document.getElementById('inputIcon');

    // Form
    const form = document.getElementById('leadCaptureForm');
    const submitBtn = document.getElementById('submitLead');
    const formError = document.getElementById('formError');
    const formSuccess = document.getElementById('formSuccess');

    // Check if user already saw or subscribed
    const hasSeenLeadCapture = localStorage.getItem('zintoop_lead_capture_seen');

    if (!hasSeenLeadCapture && !window.location.pathname.includes('/register') && !window.location.pathname.includes('/login')) {
        // Trigger logic
        let modalTriggered = false;

        const showModal = () => {
            if (modalTriggered) return;
            modalTriggered = true;
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-95', 'opacity-0');
            
            // Mark as seen so we don't annoy them for 30 days
            const thirtyDays = new Date().getTime() + (30 * 24 * 60 * 60 * 1000);
            localStorage.setItem('zintoop_lead_capture_seen', thirtyDays);
        };

        // 1. Time-based trigger (15 seconds)
        const timeTrigger = setTimeout(showModal, 15000);

        // 2. Scroll-based trigger (50% of page)
        window.addEventListener('scroll', () => {
            if (modalTriggered) return;
            const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            if (scrollPercent > 50) {
                showModal();
                clearTimeout(timeTrigger);
            }
        });
    }

    const hideModal = () => {
        content.classList.add('scale-95', 'opacity-0');
        modal.classList.add('opacity-0', 'pointer-events-none');
    };

    closeBtn.addEventListener('click', hideModal);

    // Tab Switching Logic
    tabEmail.addEventListener('click', () => {
        contactType.value = 'email';
        tabEmail.classList.add('bg-white', 'shadow', 'text-green-600');
        tabEmail.classList.remove('text-gray-500');
        tabWhatsapp.classList.remove('bg-white', 'shadow', 'text-green-600');
        tabWhatsapp.classList.add('text-gray-500');
        
        contactLabel.innerText = '{{ __('Email Address') }}';
        contactValue.placeholder = 'you@example.com';
        contactValue.type = 'email';
        inputIcon.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
    });

    tabWhatsapp.addEventListener('click', () => {
        contactType.value = 'whatsapp';
        tabWhatsapp.classList.add('bg-white', 'shadow', 'text-green-600');
        tabWhatsapp.classList.remove('text-gray-500');
        tabEmail.classList.remove('bg-white', 'shadow', 'text-green-600');
        tabEmail.classList.add('text-gray-500');
        
        contactLabel.innerText = '{{ __('WhatsApp Number') }}';
        contactValue.placeholder = '+216 20 123 456';
        contactValue.type = 'tel';
        inputIcon.innerHTML = '<svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.128.552 4.135 1.523 5.918L.004 24l6.236-1.636a11.967 11.967 0 005.791 1.492h.004c6.645 0 12.03-5.385 12.03-12.031C24.065 5.385 18.68 0 12.031 0zm0 21.84c-1.802 0-3.564-.485-5.111-1.401l-.366-.217-3.799.996 1.015-3.704-.239-.38C2.569 15.485 2.016 13.785 2.016 12.03 2.016 6.505 6.51 2.01 12.035 2.01c2.677 0 5.193 1.043 7.085 2.936A9.972 9.972 0 0122.052 12.03c0 5.524-4.494 10.02-10.02 10.02v-.01zm5.496-7.502c-.301-.151-1.782-.879-2.058-.979-.276-.1-.478-.151-.678.151-.201.301-.778.979-.953 1.18-.175.201-.35.226-.652.075-.301-.151-1.272-.469-2.423-1.496-.895-.8-1.501-1.787-1.677-2.088-.175-.301-.019-.464.131-.614.136-.136.301-.351.452-.526.151-.176.201-.301.301-.502.101-.201.05-.376-.025-.526-.075-.151-.678-1.634-.928-2.237-.243-.591-.49-.51-.678-.519-.175-.01-.377-.01-.578-.01-.201 0-.527.075-.803.376-.276.301-1.054 1.03-1.054 2.508 0 1.479 1.079 2.909 1.23 3.109.15.201 2.118 3.232 5.13 4.531 2.457 1.06 2.96 1.058 3.565.98.665-.084 1.782-.729 2.032-1.433.251-.703.251-1.306.175-1.433-.075-.126-.276-.201-.578-.352z"></path></svg>';
    });

    // Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('Sending...') }}';
        formError.classList.add('hidden');
        formSuccess.classList.add('hidden');

        try {
            const response = await fetch('/api/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    type: contactType.value,
                    contact_value: contactValue.value
                })
            });

            const data = await response.json();

            if (!response.ok) {
                formError.innerText = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'An error occurred');
                formError.classList.remove('hidden');
            } else {
                formSuccess.classList.remove('hidden');
                form.reset();
                setTimeout(() => {
                    hideModal();
                }, 2000);
            }
        } catch (error) {
            formError.innerText = '{{ __('Network error. Please try again.') }}';
            formError.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>{{ __('Send Me The Guide') }}</span>';
        }
    });
});
</script>
