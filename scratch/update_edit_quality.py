import re

with open('resources/views/listings/edit.blade.php', 'r') as f:
    content = f.read()

old_block = """              <label for="quality_grade" class="block text-[#C8A356] font-semibold mb-1">الجودة</label>
              <select id="quality_grade" name="quality_grade" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="extra_virgin" {{ old('quality_grade', $listing->product->quality ?? '') == 'extra_virgin' ? 'selected' : '' }}>بكر ممتاز (Extra Virgin)</option>
                <option value="virgin" {{ old('quality_grade', $listing->product->quality ?? '') == 'virgin' ? 'selected' : '' }}>بكر (Virgin)</option>
                <option value="lampante" {{ old('quality_grade', $listing->product->quality ?? '') == 'lampante' ? 'selected' : '' }}>صناعي (Lampante)</option>
              </select>"""

new_block = """              <label for="quality" class="block text-[#C8A356] font-semibold mb-1">الجودة</label>
              <select id="quality" name="quality" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="bio" {{ old('quality', $listing->product->quality ?? '') == 'bio' ? 'selected' : '' }}>بيولوجي (Bio)</option>
                <option value="evoo" {{ old('quality', $listing->product->quality ?? '') == 'evoo' ? 'selected' : '' }}>بكر ممتاز (EVOO)</option>
                <option value="virgin" {{ old('quality', $listing->product->quality ?? '') == 'virgin' ? 'selected' : '' }}>بكر (Virgin)</option>
                <option value="raffinee" {{ old('quality', $listing->product->quality ?? '') == 'raffinee' ? 'selected' : '' }}>مكرر (Raffinée)</option>
                <option value="pomace" {{ old('quality', $listing->product->quality ?? '') == 'pomace' ? 'selected' : '' }}>زيت فيتورة (Pomace)</option>
              </select>"""

if 'quality_grade' in content:
    print("Found quality_grade in edit.blade.php")
    content = content.replace(old_block, new_block)
    with open('resources/views/listings/edit.blade.php', 'w') as f:
        f.write(content)
else:
    print("No quality_grade found in edit.blade.php")

