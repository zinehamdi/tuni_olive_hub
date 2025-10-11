import './bootstrap';

import Alpine from 'alpinejs';
import csp from '@alpinejs/csp';

window.Alpine = Alpine;

Alpine.plugin(csp);

Alpine.start();

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
