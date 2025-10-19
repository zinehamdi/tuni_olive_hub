// Wizard Form Component
// Using window.Alpine to ensure we use the same Alpine instance as app.js
export default function registerWizardComponent(Alpine) {
    Alpine.data('wizardForm', () => ({
        currentStep: 1,
    totalSteps: 9,
    isSubmitting: false,
    errorMessage: '',
    debug: true,
    formData: {
        category: '',
        variety: '',
        quality: '',
        quantity: '',
        unit: 'kg',
        price: '',
        currency: 'TND',
        min_order: '',
        payment_methods: [],
        delivery_options: [],
        location_text: '',
        latitude: '',
        longitude: '',
        governorate: '',
        delegation: '',
        images: [],
        imagePreview: []
    },
    locationError: '',
    locationSuccess: false,

    init() {
        if (this.debug) {
            console.log('[wizard] component initialized - variety selection mode');
        }
    },

    get stepTitle() {
        const titles = {
            1: 'الخطوة 1: نوع المنتج',
            2: 'الخطوة 2: اختيار الصنف',
            3: 'الخطوة 3: الكمية',
            4: 'الخطوة 4: التسعير',
            5: 'الخطوة 5: طرق الدفع',
            6: 'الخطوة 6: التسليم',
            7: 'الخطوة 7: الموقع',
            8: 'الخطوة 8: صور المنتج',
            9: 'الخطوة 9: المراجعة النهائية'
        };
        return titles[this.currentStep] || '';
    },

    selectCategory(category) {
        this.formData.category = category;
        this.formData.variety = '';
    },

    togglePaymentMethod(method) {
        const i = this.formData.payment_methods.indexOf(method);
        if (i > -1) this.formData.payment_methods.splice(i, 1); else this.formData.payment_methods.push(method);
    },

    toggleDeliveryOption(option) {
        const i = this.formData.delivery_options.indexOf(option);
        if (i > -1) this.formData.delivery_options.splice(i, 1); else this.formData.delivery_options.push(option);
    },

    getSelectedVarietyName() {
        // Return the selected variety name
        return this.formData.variety || '';
    },

    nextStep() {
        console.log('[wizard] nextStep called');
        console.log('[wizard] currentStep:', this.currentStep, 'type:', typeof this.currentStep);
        console.log('[wizard] totalSteps:', this.totalSteps, 'type:', typeof this.totalSteps);
        console.log('[wizard] comparison (currentStep < totalSteps):', this.currentStep < this.totalSteps);
        const isValid = this.validateStep();
        console.log('[wizard] validateStep returned:', isValid);
        if (isValid && this.currentStep < this.totalSteps) {
            this.currentStep++;
            console.log('[wizard] moved to step:', this.currentStep);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            console.log('[wizard] nextStep blocked - isValid:', isValid, 'canAdvance:', this.currentStep < this.totalSteps);
        }
    },

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    validateStep() {
        console.log('[wizard] validateStep called for step:', this.currentStep);
        switch (this.currentStep) {
            case 1:
                if (!this.formData.category) { alert('الرجاء اختيار نوع المنتج'); return false; }
                break;
            case 2:
                if (!this.formData.variety) { alert('الرجاء اختيار الصنف'); return false; }
                break;
            case 3:
                if (!this.formData.quantity || this.formData.quantity <= 0) { alert('الرجاء إدخال الكمية'); return false; }
                if (!this.formData.unit) { alert('الرجاء اختيار الوحدة'); return false; }
                break;
            case 4:
                if (!this.formData.price || this.formData.price <= 0) { alert('الرجاء إدخال السعر'); return false; }
                break;
            case 5:
                if (this.formData.payment_methods.length === 0) { alert('الرجاء اختيار طريقة دفع واحدة على الأقل'); return false; }
                break;
            case 6:
                if (this.formData.delivery_options.length === 0) { alert('الرجاء اختيار خيار تسليم واحد على الأقل'); return false; }
                break;
            case 7:
                if (!this.formData.governorate && !this.formData.location_text) { alert('الرجاء إدخال الموقع أو اختيار الولاية'); return false; }
                break;
            case 8:
                // Images are optional, no validation needed
                return true;
            case 9:
                return true;
        }
        return true;
    },

    handleImageSelect(event) {
        console.log('[wizard] handleImageSelect called');
        console.log('[wizard] event:', event);
        console.log('[wizard] event.target:', event.target);
        console.log('[wizard] files:', event.target.files);
        
        const files = Array.from(event.target.files);
        console.log('[wizard] files array:', files, 'length:', files.length);
        
        if (files.length > 5) {
            alert('يمكنك رفع 5 صور كحد أقصى');
            return;
        }
        
        // Validate file sizes
        for (const file of files) {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('حجم الصورة يجب أن يكون أقل من 2MB');
                return;
            }
        }
        
        this.formData.images = files;
        this.formData.imagePreview = [];
        console.log('[wizard] creating previews...');
        
        // Create preview URLs
        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                console.log('[wizard] preview loaded for image', idx);
                this.formData.imagePreview.push(e.target.result);
            };
            reader.readAsDataURL(file);
        });
        
        console.log('[wizard] handleImageSelect complete');
    },

    removeImage(index) {
        this.formData.imagePreview.splice(index, 1);
        const dt = new DataTransfer();
        const files = Array.from(this.formData.images);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        document.getElementById('images').files = dt.files;
        this.formData.images = Array.from(dt.files);
    },

    getCurrentLocation(event) {
        this.locationError = '';
        this.locationSuccess = false;
        if (!navigator.geolocation) { this.locationError = 'المتصفح لا يدعم تحديد الموقع'; return; }
        const button = event?.target;
        if (button) {
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4zm2 5.29A7.96 7.96 0 014 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65z"/></svg>';
        }
        navigator.geolocation.getCurrentPosition(pos => {
            this.formData.latitude = pos.coords.latitude.toFixed(6);
            this.formData.longitude = pos.coords.longitude.toFixed(6);
            this.locationSuccess = true;
            if (button) {
                button.disabled = false;
                button.innerHTML = '✓ تم تحديد الموقع';
                setTimeout(() => { button.innerHTML = 'حدد موقعي الحالي'; }, 2000);
            }
        }, err => {
            if (button) { button.disabled = false; button.innerHTML = 'حدد موقعي الحالي'; }
            this.locationError = 'تعذر تحديد الموقع (' + err.code + ')';
        });
    },

    handleSubmit(ev) {
        if (this.debug) console.log('[wizard] submit intercepted');
        ev.preventDefault();
        this.errorMessage = '';
        if (!this.validateStep()) return;
        if (!this.formData.product_id) { this.errorMessage = 'الرجاء اختيار المنتج'; return; }
        if (!this.formData.price) { this.errorMessage = 'الرجاء إدخال السعر'; return; }
        this.isSubmitting = true;
        if (this.debug) {
            console.log('[wizard] submitting FormData to server');
        }
        // Let the native form submit after a tick to allow UI update
        setTimeout(() => {
            ev.target.submit();
        }, 10);
    }
    }));
}

if (window) {
    window.__wizardInjected = true;
    console.log('[wizard] module loaded');
}