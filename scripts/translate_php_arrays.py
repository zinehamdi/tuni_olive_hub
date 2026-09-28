import os
import json
import subprocess
from deep_translator import GoogleTranslator

base_path = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
lang_dir = os.path.join(base_path, 'resources', 'lang')
en_dir = os.path.join(lang_dir, 'en')

targets = ['es', 'zh-CN', 'ja']
folders = ['es', 'zh', 'ja']

def php_to_dict(filepath):
    php_code = f"echo json_encode(require '{filepath}');"
    result = subprocess.run(['php', '-r', php_code], capture_output=True, text=True)
    if result.returncode == 0 and result.stdout:
        return json.loads(result.stdout)
    return {}

def dict_to_php(data):
    php_str = "<?php\n\nreturn [\n"
    for k, v in data.items():
        val = str(v).replace("'", "\\'")
        php_str += f"    '{k}' => '{val}',\n"
    php_str += "];\n"
    return php_str

for filename in os.listdir(en_dir):
    if not filename.endswith('.php'):
        continue
        
    filepath = os.path.join(en_dir, filename)
    en_data = php_to_dict(filepath)
    if not en_data:
        continue
        
    for target_lang, folder_name in zip(targets, folders):
        target_dir = os.path.join(lang_dir, folder_name)
        os.makedirs(target_dir, exist_ok=True)
        
        target_file = os.path.join(target_dir, filename)
        
        # Load existing if available
        existing_data = {}
        if os.path.exists(target_file):
            existing_data = php_to_dict(target_file)
            
        new_data = {}
        for k, v in en_data.items():
            if k in existing_data and existing_data[k]:
                new_data[k] = existing_data[k]
                continue
                
            if isinstance(v, str) and v.strip():
                try:
                    translated = GoogleTranslator(source='en', target=target_lang).translate(v)
                    new_data[k] = translated
                except Exception as e:
                    print(f"Error translating {k}: {e}")
                    new_data[k] = v
            else:
                new_data[k] = v
                
        with open(target_file, 'w', encoding='utf-8') as f:
            f.write(dict_to_php(new_data))
            
        print(f"Translated {filename} to {folder_name}")
