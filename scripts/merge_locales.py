import json
import os

lang_dir = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'resources', 'lang')

# Load the full 1377-key en.json
with open(os.path.join(lang_dir, 'en.json'), 'r', encoding='utf-8') as f:
    full_en = json.load(f)

for lang in ['es', 'zh', 'ja']:
    file_path = os.path.join(lang_dir, f"{lang}.json")
    
    # Load existing (partially translated) file
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            existing = json.load(f)
    except FileNotFoundError:
        existing = {}
        
    # Merge
    merged = full_en.copy()
    for k, v in existing.items():
        if v and v != k: # if it was actually translated
            merged[k] = v
            
    # Save back
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(merged, f, ensure_ascii=False, indent=4)

print("Merged successfully. Files now contain 1377 keys each.")
