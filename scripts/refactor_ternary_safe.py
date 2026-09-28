import os
import re
import json

base_path = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
views_dir = os.path.join(base_path, 'resources', 'views')
lang_dir = os.path.join(base_path, 'resources', 'lang')

def load_json(lang):
    path = os.path.join(lang_dir, f"{lang}.json")
    if os.path.exists(path):
        with open(path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}

def save_json(lang, data):
    path = os.path.join(lang_dir, f"{lang}.json")
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)

ar_data = load_json('ar')
fr_data = load_json('fr')
en_data = load_json('en')

# ONLY match if the English fallback does NOT contain __
pattern1 = re.compile(r"app\(\)->getLocale\(\)\s*===\s*'ar'\s*\?\s*(['\"])(.*?)\1\s*:\s*\(\s*app\(\)->getLocale\(\)\s*===\s*'fr'\s*\?\s*(['\"])(.*?)\3\s*:\s*(['\"])(.*?)\5\s*\)")
pattern2 = re.compile(r"app\(\)->getLocale\(\)\s*===\s*'ar'\s*\?\s*(['\"])(.*?)\1\s*:\s*(['\"])(.*?)\3")

total_replaced = 0

def process_file(filepath):
    global total_replaced
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    def repl1(m):
        en_str = m.group(6)
        # Skip if it's dynamic or already wrapped in __(
        if '__(' in m.group(0) or '$' in en_str:
            return m.group(0)
            
        ar_str = m.group(2)
        fr_str = m.group(4)
        
        ar_data[en_str] = ar_str
        fr_data[en_str] = fr_str
        en_data[en_str] = en_str
        
        return f"__('{en_str}')"

    def repl2(m):
        en_str = m.group(4)
        if '__(' in m.group(0) or '$' in en_str:
            return m.group(0)
            
        ar_str = m.group(2)
        
        ar_data[en_str] = ar_str
        en_data[en_str] = en_str
        
        return f"__('{en_str}')"

    content = pattern1.sub(repl1, content)
    content = pattern2.sub(repl2, content)

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        total_replaced += 1
        print(f"Updated {filepath}")

for root, dirs, files in os.walk(views_dir):
    for file in files:
        if file.endswith('.blade.php'):
            process_file(os.path.join(root, file))

save_json('ar', ar_data)
save_json('fr', fr_data)
save_json('en', en_data)

print(f"Total files safely updated: {total_replaced}")
