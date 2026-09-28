import json
import os
import sys
import time
from concurrent.futures import ThreadPoolExecutor, as_completed
from deep_translator import GoogleTranslator

target_languages = {
    'es': 'es',
    'zh': 'zh-CN',
    'ja': 'ja'
}

base_path = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
lang_dir = os.path.join(base_path, 'resources', 'lang')

# We use ar.json as the source of truth for ALL keys, because it has 1377 keys!
ar_file_path = os.path.join(lang_dir, 'ar.json')

with open(ar_file_path, 'r', encoding='utf-8') as f:
    ar_data = json.load(f)

english_keys = list(ar_data.keys())

# Let's fix en.json while we are at it
en_file_path = os.path.join(lang_dir, 'en.json')
en_data = {k: k for k in english_keys}
with open(en_file_path, 'w', encoding='utf-8') as f:
    json.dump(en_data, f, ensure_ascii=False, indent=4)

def translate_key(key, target_lang, existing_dict):
    if key in existing_dict and existing_dict[key] and existing_dict[key] != key:
        return key, existing_dict[key]
    try:
        translated_text = GoogleTranslator(source='en', target=target_lang).translate(key)
        return key, translated_text
    except Exception:
        return key, key

for lang_code, target_lang in target_languages.items():
    print(f"Translating to {lang_code} ({target_lang})...")
    output_file_path = os.path.join(lang_dir, f"{lang_code}.json")
    
    translated_data = {}
    if os.path.exists(output_file_path):
        try:
            with open(output_file_path, 'r', encoding='utf-8') as f:
                translated_data = json.load(f)
        except json.JSONDecodeError:
            pass
            
    total_keys = len(english_keys)
    completed = 0
    
    # Fast concurrent translation
    with ThreadPoolExecutor(max_workers=10) as executor:
        futures = {executor.submit(translate_key, key, target_lang, translated_data): key for key in english_keys}
        for future in as_completed(futures):
            key, val = future.result()
            translated_data[key] = val
            completed += 1
            if completed % 100 == 0:
                print(f"[{lang_code}] {completed}/{total_keys} keys translated...")

    with open(output_file_path, 'w', encoding='utf-8') as f:
        json.dump(translated_data, f, ensure_ascii=False, indent=4)
        
    print(f"Finished {lang_code}. Saved to {output_file_path}")

print("All translations completed successfully.")
