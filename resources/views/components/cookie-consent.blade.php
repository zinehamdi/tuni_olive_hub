<div id="cookieConsentBanner" class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 shadow-lg z-[10000] transform translate-y-full transition-transform duration-500 ease-in-out flex flex-col md:flex-row justify-between items-center" style="display: none; z-index: 100000;">
    <div class="text-sm text-gray-300 mb-4 md:mb-0 max-w-4xl">
        <strong>{{ __('We value your privacy.') }}</strong> {{ __('We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.') }}
    </div>
    <div class="flex space-x-4 shrink-0 rtl:space-x-reverse">
        <button id="declineCookies" class="px-4 py-2 border border-gray-600 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 transition">{{ __('Decline') }}</button>
        <button id="acceptCookies" class="px-4 py-2 bg-green-600 rounded-md text-sm font-medium text-white hover:bg-green-700 transition">{{ __('Accept All') }}</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const consentBanner = document.getElementById('cookieConsentBanner');
        const acceptBtn = document.getElementById('acceptCookies');
        const declineBtn = document.getElementById('declineCookies');

        // Check if cookie exists
        if (!localStorage.getItem('zintoop_cookie_consent')) {
            // Show banner after short delay
            setTimeout(() => {
                consentBanner.style.display = 'flex';
                // Trigger reflow to apply transition
                consentBanner.offsetHeight;
                consentBanner.classList.remove('translate-y-full');
            }, 1000);
        }

        function hideBanner() {
            consentBanner.classList.add('translate-y-full');
            setTimeout(() => {
                consentBanner.style.display = 'none';
            }, 500);
        }

        acceptBtn.addEventListener('click', function() {
            localStorage.setItem('zintoop_cookie_consent', 'accepted');
            hideBanner();
        });

        declineBtn.addEventListener('click', function() {
            localStorage.setItem('zintoop_cookie_consent', 'declined');
            hideBanner();
        });
    });
</script>
