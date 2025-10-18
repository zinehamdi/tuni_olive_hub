# Role Card Design System
**Beautiful Card-Based Selection Pattern**

## Overview
This design system is based on the role selection pattern from `register.blade.php`. It features interactive cards with icons, descriptions, hover effects, and active states.

## Key Design Elements

### 1. **Card Container**
```html
<label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
              cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 
              transition-all"
       :class="selected === 'value' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
    <!-- Card content -->
</label>
```

**Features:**
- `border-2 border-gray-200` - Prominent border
- `rounded-xl` - Smooth rounded corners
- `p-4` - Generous padding
- `hover:border-[#6A8F3B]` - Green border on hover
- `hover:bg-[#6A8F3B]/5` - Subtle green background on hover
- `transition-all` - Smooth transitions
- Alpine.js `:class` for selected state

### 2. **Icon Box with Gradient**
```html
<div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] 
            flex items-center justify-center text-white">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- SVG path -->
    </svg>
</div>
```

**Color Gradients by Category:**
- **Primary/Main**: `from-[#6A8F3B] to-[#5a7a2f]` (Green)
- **Secondary**: `from-[#C8A356] to-[#b08a3c]` (Gold)
- **Tertiary**: `from-[#8B4513] to-[#6B3410]` (Brown)
- **Accent 1**: `from-[#9333EA] to-[#7E22CE]` (Purple)
- **Accent 2**: `from-[#3B82F6] to-[#2563EB]` (Blue)
- **Accent 3**: `from-[#EF4444] to-[#DC2626]` (Red)
- **Accent 4**: `from-[#10B981] to-[#059669]` (Emerald)

### 3. **Content Layout**
```html
<input type="radio" name="option" value="value" 
       x-model="selected" 
       class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
<div class="flex items-center gap-3 flex-1">
    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] 
                flex items-center justify-center text-white">
        <!-- Icon -->
    </div>
    <div>
        <div class="font-bold text-gray-900">Title</div>
        <div class="text-sm text-gray-500">Description</div>
    </div>
</div>
```

## Application Examples

### Example 1: Payment Method Selection
```html
<div x-data="{ payment: '' }">
    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="payment === 'cash' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="payment" value="cash" x-model="payment" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Cash Payment') }}</div>
                <div class="text-sm text-gray-500">{{ __('Pay upon delivery') }}</div>
            </div>
        </div>
    </label>

    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="payment === 'card' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="payment" value="card" x-model="payment" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#3B82F6] to-[#2563EB] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Card Payment') }}</div>
                <div class="text-sm text-gray-500">{{ __('Pay with credit/debit card') }}</div>
            </div>
        </div>
    </label>
</div>
```

### Example 2: Delivery Options
```html
<div x-data="{ delivery: '' }">
    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="delivery === 'standard' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="delivery" value="standard" x-model="delivery" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="font-bold text-gray-900">{{ __('Standard Delivery') }}</div>
                <div class="text-sm text-gray-500">{{ __('3-5 business days') }}</div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">{{ __('Free') }}</div>
            </div>
        </div>
    </label>

    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="delivery === 'express' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="delivery" value="express" x-model="delivery" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#EF4444] to-[#DC2626] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="font-bold text-gray-900">{{ __('Express Delivery') }}</div>
                <div class="text-sm text-gray-500">{{ __('1-2 business days') }}</div>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-gray-900">10 {{ __('TND') }}</div>
            </div>
        </div>
    </label>
</div>
```

### Example 3: Product Type Selection
```html
<div x-data="{ productType: '' }">
    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="productType === 'oil' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="product_type" value="oil" x-model="productType" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] 
                        flex items-center justify-center text-white">
                <span class="text-2xl">🫗</span>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Olive Oil') }}</div>
                <div class="text-sm text-gray-500">{{ __('Processed and packaged oil') }}</div>
            </div>
        </div>
    </label>

    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="productType === 'olive' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="product_type" value="olive" x-model="productType" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] 
                        flex items-center justify-center text-white">
                <span class="text-2xl">🫒</span>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Olives') }}</div>
                <div class="text-sm text-gray-500">{{ __('Fresh olives from farm') }}</div>
            </div>
        </div>
    </label>
</div>
```

### Example 4: Quality Selection
```html
<div x-data="{ quality: '' }">
    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="quality === 'premium' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="quality" value="premium" x-model="quality" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#C8A356] to-[#b08a3c] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Premium') }}</div>
                <div class="text-sm text-gray-500">{{ __('Highest quality, extra virgin') }}</div>
            </div>
        </div>
    </label>

    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="quality === 'standard' ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="radio" name="quality" value="standard" x-model="quality" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#6A8F3B] to-[#5a7a2f] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Standard') }}</div>
                <div class="text-sm text-gray-500">{{ __('Good quality, everyday use') }}</div>
            </div>
        </div>
    </label>
</div>
```

## Checkbox Variant

For multiple selections, use checkboxes instead of radio buttons:

```html
<div x-data="{ features: [] }">
    <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl 
                  cursor-pointer hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all"
           :class="features.includes('organic') ? 'border-[#6A8F3B] bg-[#6A8F3B]/10' : ''">
        <input type="checkbox" name="features[]" value="organic" x-model="features" 
               class="text-[#6A8F3B] focus:ring-[#6A8F3B] rounded w-5 h-5">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[#10B981] to-[#059669] 
                        flex items-center justify-center text-white">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="font-bold text-gray-900">{{ __('Organic') }}</div>
                <div class="text-sm text-gray-500">{{ __('Certified organic product') }}</div>
            </div>
        </div>
    </label>
</div>
```

## When to Use This Pattern

✅ **Use for:**
- Role/account type selection
- Payment method selection
- Delivery options
- Product type/category selection
- Plan/subscription selection
- Important multi-option choices
- Feature selections with descriptions

❌ **Don't use for:**
- Simple yes/no choices
- Long lists (>8 options)
- Minor settings
- Text-heavy options
- Forms with many fields (too much visual weight)

## Accessibility Considerations

1. **Always include proper labels** - The entire card should be clickable
2. **Use semantic HTML** - Keep input elements inside label for proper association
3. **Keyboard navigation** - Radio/checkbox inputs are focusable
4. **Screen readers** - Use descriptive text
5. **Color contrast** - Ensure text meets WCAG standards
6. **Focus states** - Built-in focus rings on inputs

## Best Practices

1. **Keep descriptions short** - 1-2 lines max
2. **Use relevant icons** - Icon should clearly represent the option
3. **Consistent gradient colors** - Use the defined color palette
4. **Logical grouping** - Group related options together
5. **Clear visual hierarchy** - Title bold, description lighter
6. **Responsive design** - Stack cards on mobile
7. **Loading states** - Disable cards during submission

## Responsive Layout

```html
<!-- Stack on mobile, grid on larger screens -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <!-- Cards -->
</div>

<!-- Always stack vertically -->
<div class="space-y-3">
    <!-- Cards -->
</div>
```

---
*Based on: auth/register.blade.php role selection design*
*Last updated: October 15, 2025*
