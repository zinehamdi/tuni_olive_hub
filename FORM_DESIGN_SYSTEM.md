# Form Design System
**Tunisian Olive Oil Platform - Consistent Form Styling**

## Color Palette
```css
Primary Green: #6A8F3B
Dark Green: #5a7a2f
Gold: #C8A356
Dark Gold: #b08a3c
Gray Text: #6B7280
White: #FFFFFF
```

## Form Container
```html
<div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100">
    <!-- Form content -->
</div>
```

## Input Fields

### Text Input (Standard)
```html
<input 
    type="text"
    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-900 
           focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 focus:bg-white
           transition-all duration-200"
    placeholder="Enter text..."
/>
```

### Select Dropdown
```html
<select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-900 
               focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 focus:bg-white
               transition-all duration-200">
    <option>Option 1</option>
</select>
```

### Textarea
```html
<textarea 
    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-900 
           focus:border-[#6A8F3B] focus:ring-4 focus:ring-[#6A8F3B]/20 focus:bg-white
           transition-all duration-200 min-h-[120px]"
    placeholder="Enter description..."></textarea>
```

### Checkbox
```html
<input 
    type="checkbox" 
    class="rounded border-gray-300 text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5"
/>
```

### Radio Button
```html
<input 
    type="radio" 
    class="text-[#6A8F3B] focus:ring-[#6A8F3B] w-5 h-5"
/>
```

## Labels

### Standard Label
```html
<label class="block text-gray-900 font-bold mb-2">
    Label Text
</label>
```

### Label with Icon
```html
<label class="text-gray-900 font-bold mb-2 flex items-center gap-2">
    <svg class="w-5 h-5 text-[#6A8F3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- SVG path -->
    </svg>
    Label Text
</label>
```

## Buttons

### Primary Button (Green Gradient)
```html
<button class="w-full py-4 px-6 bg-gradient-to-r from-[#6A8F3B] to-[#5a7a2f] 
               text-white font-bold rounded-xl shadow-lg 
               hover:shadow-xl hover:scale-[1.02] 
               transition-all duration-200 
               flex items-center justify-center gap-2">
    <span>Submit</span>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <!-- Icon -->
    </svg>
</button>
```

### Secondary Button (Gold)
```html
<button class="w-full py-3 px-6 bg-[#C8A356] text-white font-bold rounded-xl 
               hover:bg-[#b08a3c] transition-all shadow-md hover:shadow-lg">
    Secondary Action
</button>
```

### Outline Button
```html
<button class="w-full py-3 px-6 border-2 border-[#6A8F3B] text-[#6A8F3B] font-bold rounded-xl 
               hover:bg-[#6A8F3B] hover:text-white transition-all">
    Outline Button
</button>
```

### Reset/Cancel Button (Gray)
```html
<button class="w-full py-3 px-6 bg-gradient-to-r from-gray-600 to-gray-700 
               text-white font-bold rounded-xl shadow-md
               hover:from-gray-700 hover:to-gray-800 transition-all">
    Reset
</button>
```

## Form Groups

### Standard Form Group
```html
<div class="space-y-6">
    <!-- Each field -->
    <div>
        <label class="block text-gray-900 font-bold mb-2">Field Label</label>
        <input type="text" class="..." />
        <!-- Error message if needed -->
        <p class="mt-1 text-sm text-red-600">Error message</p>
    </div>
</div>
```

### Highlighted Form Section
```html
<div class="p-4 bg-gradient-to-r from-[#6A8F3B]/10 to-[#C8A356]/10 rounded-xl mb-6">
    <!-- Important form fields -->
</div>
```

## Form Layout

### Single Column
```html
<form class="space-y-6">
    <!-- Fields -->
</form>
```

### Two Column Grid
```html
<form class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div><!-- Field 1 --></div>
        <div><!-- Field 2 --></div>
    </div>
</form>
```

## Error Messages
```html
<p class="mt-1 text-sm text-red-600 flex items-center gap-1">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
    </svg>
    Error message text
</p>
```

## Success Messages
```html
<div class="p-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-800 flex items-center gap-2">
    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    Success message text
</div>
```

## File Upload
```html
<div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center 
            hover:border-[#6A8F3B] hover:bg-[#6A8F3B]/5 transition-all cursor-pointer">
    <input type="file" class="hidden" id="file-upload" />
    <label for="file-upload" class="cursor-pointer">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <span class="text-sm text-gray-600">Click to upload or drag and drop</span>
    </label>
</div>
```

## Loading States
```html
<button disabled class="w-full py-4 px-6 bg-gray-400 text-white font-bold rounded-xl 
                        cursor-not-allowed flex items-center justify-center gap-2">
    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
    </svg>
    Processing...
</button>
```

## Common Icons (SVG Paths)

### Email Icon
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
</svg>
```

### Password/Lock Icon
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
```

### User Icon
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
</svg>
```

### Phone Icon
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
</svg>
```

### Location Icon
```html
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
</svg>
```

## Responsive Design
- Use `md:` prefix for tablet/desktop styles
- Stack inputs vertically on mobile, use grid on larger screens
- Adjust padding: `p-6 md:p-8`
- Adjust font sizes: `text-sm md:text-base`

---
*Last Updated: October 15, 2025*
