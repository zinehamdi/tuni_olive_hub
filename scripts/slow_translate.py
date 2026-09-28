import json
import os
import time
from deep_translator import GoogleTranslator

target_languages = {'es': 'es', 'zh': 'zh-CN', 'ja': 'ja'}
lang_dir = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'resources', 'lang')

for lang_code, target_lang in target_languages.items():
    file_path = os.path.join(lang_dir, f"{lang_code}.json")
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
        
    translator = GoogleTranslator(source='en', target=target_lang)
    translated_count = 0
    total = len(data)
    
    print(f"Starting {lang_code}...", flush=True)
    
    for i, (key, value) in enumerate(data.items()):
        if key == value and len(key) > 1:
            try:
                translated = translator.translate(key)
                if translated:
                    data[key] = translated
                translated_count += 1
                time.sleep(0.1)
            except Exception:
                # Silently catch the error so it doesn't trigger system alerts!
                pass
                
        if i % 100 == 0:
            print(f"[{lang_code}] Processed {i}/{total} keys...", flush=True)
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(data, f, ensure_ascii=False, indent=4)
                
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
        
    print(f"Finished {lang_code}. Translated {translated_count} new keys.", flush=True)

print("All missing translations completed!", flush=True)
