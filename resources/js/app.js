import './bootstrap';

import Alpine from 'alpinejs';

// Import wizard component registration function
import registerWizardComponent from './components/wizard.js';

// Set Alpine on window
window.Alpine = Alpine;

// Register wizard component BEFORE starting Alpine
registerWizardComponent(Alpine);

// Global Toast Store
Alpine.store('toast', {
    show: false,
    message: '',
    type: 'success',
    showToast(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.show = true;
        setTimeout(() => { this.show = false; }, 4000);
    }
});

// Global Chat Store for state sharing between modals
Alpine.store('chat', {
    assigning: false,
    selectedOrder: null
});

// Start Alpine
Alpine.start();

console.log('Alpine started with stores:', Object.keys(Alpine.store('toast') || {}));

console.log('Alpine started');

// Background slideshow: reads slide URLs from <meta name="bg-slides"> (JSON array) and optional <meta name="bg-interval">
document.addEventListener('DOMContentLoaded', () => {
	try {
		const slidesMeta = document.querySelector('meta[name="bg-slides"]');
		let slides = [];
		if (slidesMeta && slidesMeta.content) {
			try { slides = JSON.parse(slidesMeta.content); } catch (_) { slides = []; }
		}
		if ((!slides || slides.length === 0) && Array.isArray(window.__bgSlides)) {
			slides = window.__bgSlides;
		}
		if (!Array.isArray(slides) || slides.length === 0) return;

		const intervalMeta = document.querySelector('meta[name="bg-interval"]');
		let interval = 8000;
		if (intervalMeta && intervalMeta.content) {
			const parsed = parseInt(intervalMeta.content, 10);
			if (!Number.isNaN(parsed)) interval = parsed;
		}

		let i = 0;
		const apply = () => {
			const url = String(slides[i] || '').trim();
			if (!url) return;
			document.documentElement.style.backgroundImage = `url('${url}')`;
			document.body.style.backgroundImage = `url('${url}')`;
			i = (i + 1) % slides.length;
		};
		apply();
		if (slides.length > 1) setInterval(apply, Math.max(2000, interval));
	} catch (e) {
		// fail silently
	}
});

window.alpineRegisterForm = function() {
    return {
        role: '',
        clearFields() {
            if (this.role !== 'farmer') {
                this.$refs.farm_location.value = '';
                this.$refs.tree_number.value = '';
            }
            if (this.role !== 'carrier') {
                this.$refs.camion_capacity.value = '';
            }
            if (this.role !== 'mill') {
                this.$refs.mill_name.value = '';
            }
            if (!['farmer','mill','packer'].includes(this.role)) {
                this.$refs.olive_type.value = '';
            }
        }
    }
}

// PWA install prompt and service worker registration
if ('serviceWorker' in navigator) {
	window.addEventListener('load', () => {
		navigator.serviceWorker.register('/service-worker.js').catch(() => {});
	});
}

(() => {
	let deferredPrompt;
	const installButton = document.getElementById('pwa-install');
	if (!installButton) return;

	// Check if already installed (running as standalone PWA)
	const isInstalled = window.matchMedia('(display-mode: standalone)').matches ||
	                    window.navigator.standalone === true ||
	                    localStorage.getItem('pwa-installed') === 'true';
	
	if (isInstalled) {
		installButton.classList.add('hidden');
		return;
	}

	const disabledClasses = ['opacity-50', 'cursor-not-allowed'];

	window.addEventListener('beforeinstallprompt', (event) => {
		event.preventDefault();
		deferredPrompt = event;
		installButton.classList.remove('hidden');
		installButton.classList.remove(...disabledClasses);
	});

	installButton.addEventListener('click', async () => {
		if (!deferredPrompt) return;
		deferredPrompt.prompt();
		try {
			const { outcome } = await deferredPrompt.userChoice;
			if (outcome === 'accepted') {
				localStorage.setItem('pwa-installed', 'true');
				installButton.classList.add('hidden');
			}
		} finally {
			deferredPrompt = null;
		}
	});

	window.addEventListener('appinstalled', () => {
		deferredPrompt = null;
		localStorage.setItem('pwa-installed', 'true');
		installButton.classList.add('hidden');
	});
})();

// Web Push subscription flow
(() => {
	const pushButton = document.getElementById('enable-notifications');
	const vapidMeta = document.querySelector('meta[name="vapid-public-key"]');
	const vapidKey = vapidMeta?.content || '';
	const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

	const disabledClasses = ['opacity-50', 'cursor-not-allowed'];

	const isIos = () => /iphone|ipad|ipod/i.test(window.navigator.userAgent);

	if (!pushButton) return;

	const supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window && vapidKey;
	if (!supported || isIos()) {
		// Hide on unsupported platforms (notably iOS browsers)
		pushButton.classList.add('hidden');
		return;
	}

	pushButton.classList.remove('hidden');
	pushButton.classList.add(...disabledClasses);

	const urlBase64ToUint8Array = (base64String) => {
		const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
		const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
		const rawData = window.atob(base64);
		const outputArray = new Uint8Array(rawData.length);
		for (let i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	};

	const sendSubscription = async (subscription) => {
		await fetch('/push/subscribe', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrf || '',
				'Accept': 'application/json'
			},
			body: JSON.stringify(subscription)
		});
	};

	const enableButton = () => pushButton.classList.remove(...disabledClasses);
	const disableButton = () => pushButton.classList.add(...disabledClasses);

	window.addEventListener('beforeinstallprompt', () => {
		// no-op, just ensuring SW registration exists; push logic independent
	});

	navigator.serviceWorker.ready.then(async (registration) => {
		const existing = await registration.pushManager.getSubscription();
		if (existing) {
			disableButton();
			return;
		}
		enableButton();
	}).catch(() => {});

	pushButton.addEventListener('click', async () => {
		if (pushButton.classList.contains('opacity-50')) return;
		try {
			const permission = await Notification.requestPermission();
			if (permission !== 'granted') {
				disableButton();
				return;
			}

			const registration = await navigator.serviceWorker.ready;
			const subscription = await registration.pushManager.subscribe({
				userVisibleOnly: true,
				applicationServerKey: urlBase64ToUint8Array(vapidKey),
			});

			await sendSubscription(subscription);
			disableButton();
		} catch (e) {
			// If anything fails, keep button enabled for retry
			enableButton();
		}
	});
})();
