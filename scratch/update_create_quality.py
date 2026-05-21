import re

with open('resources/views/listings/create.blade.php', 'r') as f:
    content = f.read()

old_block = """              <label for="quality_grade" class="block text-[#C8A356] font-semibold mb-1">الجودة</label>
              <select id="quality_grade" name="quality_grade" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="extra_virgin">بكر ممتاز (Extra Virgin)</option>
                <option value="virgin">بكر (Virgin)</option>
                <option value="lampante">صناعي (Lampante)</option>
              </select>"""

new_block = """              <label for="quality" class="block text-[#C8A356] font-semibold mb-1">الجودة</label>
              <select id="quality" name="quality" class="w-full rounded-xl border border-[#C7D1C7] px-3 py-3 bg-white focus:ring-2 focus:ring-[#C8A356]">
                <option value="">— اختر —</option>
                <option value="bio">بيولوجي (Bio)</option>
                <option value="evoo">بكر ممتاز (EVOO)</option>
                <option value="virgin">بكر (Virgin)</option>
                <option value="raffinee">مكرر (Raffinée)</option>
                <option value="pomace">زيت فيتورة (Pomace)</option>
              </select>"""

content = content.replace(old_block, new_block)

with open('resources/views/listings/create.blade.php', 'w') as f:
    f.write(content)

