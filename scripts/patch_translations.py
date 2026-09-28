import json
import os

base_path = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
lang_dir = os.path.join(base_path, 'resources', 'lang')

patches = {
    'es': {
        'chemlali': 'chemlali',
        'arbaquina': 'arbequina',
        'المنستير': 'Monastir',
        'Images (Optional)': 'Imágenes (Opcional)',
        'Add Company': 'Añadir Empresa',
        'Product Packaging': 'Embalaje de Producto',
        'Transport Tasks': 'Tareas de Transporte',
        'Role': 'Rol',
        'Complete the details to join the platform': 'Complete los detalles para unirse a la plataforma',
        'My Batches': 'Mis Lotes',
        'Zarzis': 'Zarzis',
        'Add Mill Name': 'Añadir Nombre del Molino',
        'lampante': 'lampante',
        'جرجيس': 'Zarzis',
        'وقاد (Lampante)': 'Lampante',
        'Register as Buyer / Importer': 'Registrarse como Comprador / Importador',
        'Services': 'Servicios',
        'WhatsApp': 'WhatsApp',
        'Monastir': 'Monastir'
    },
    'zh': {
        'chemlali': 'chemlali',
        'arbaquina': 'arbequina',
        'المنستير': '莫纳斯提尔',
        'Images (Optional)': '图片（可选）',
        'Add Company': '添加公司',
        'Product Packaging': '产品包装',
        'Transport Tasks': '运输任务',
        'Role': '角色',
        'Complete the details to join the platform': '填写详细信息以加入平台',
        'My Batches': '我的批次',
        'Zarzis': '扎尔吉斯',
        'Add Mill Name': '添加磨坊名称',
        'lampante': 'lampante',
        'جرجيس': '扎尔吉斯',
        'وقاد (Lampante)': 'Lampante',
        'Register as Buyer / Importer': '注册为买家/进口商',
        'Services': '服务',
        'WhatsApp': 'WhatsApp',
        'Monastir': '莫纳斯提尔'
    },
    'ja': {
        'chemlali': 'chemlali',
        'arbaquina': 'arbequina',
        'المنستير': 'モナスティル',
        'Images (Optional)': '画像（オプション）',
        'Add Company': '会社を追加',
        'Product Packaging': '製品パッケージ',
        'Transport Tasks': '輸送タスク',
        'Role': '役割',
        'Complete the details to join the platform': 'プラットフォームに参加するための詳細を記入してください',
        'My Batches': '私のバッチ',
        'Zarzis': 'ザルジス',
        'Add Mill Name': '製粉所の名前を追加',
        'lampante': 'lampante',
        'جرجيس': 'ザルジス',
        'وقاد (Lampante)': 'Lampante',
        'Register as Buyer / Importer': 'バイヤー/輸入者として登録する',
        'Services': 'サービス',
        'WhatsApp': 'WhatsApp',
        'Monastir': 'モナスティル'
    }
}

for lang in ['es', 'zh', 'ja']:
    file_path = os.path.join(lang_dir, f"{lang}.json")
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    for key, val in patches[lang].items():
        data[key] = val
        
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)

print("Translations patched successfully!")
