import os
from PIL import Image, ImageDraw, ImageFilter

def process_card(bg_path, logo_path, out_path, target_size=(1200, 630), logo_diameter=150, logo_top=22):
    # Open and resize background
    bg = Image.open(bg_path).convert("RGBA")
    bg = bg.resize(target_size, Image.Resampling.LANCZOS)
    
    # Open real logo
    logo = Image.open(logo_path).convert("RGBA")
    
    # Resize logo to circle
    logo = logo.resize((logo_diameter, logo_diameter), Image.Resampling.LANCZOS)
    
    # Create circular mask for logo
    mask = Image.new("L", (logo_diameter, logo_diameter), 0)
    draw_mask = ImageDraw.Draw(mask)
    draw_mask.ellipse((0, 0, logo_diameter, logo_diameter), fill=255)
    
    # Calculate position (center horizontally)
    pos_x = (target_size[0] - logo_diameter) // 2
    pos_y = logo_top
    
    # Create shadow
    shadow_size = (logo_diameter + 20, logo_diameter + 20)
    shadow = Image.new("RGBA", shadow_size, (0, 0, 0, 0))
    draw_shadow = ImageDraw.Draw(shadow)
    draw_shadow.ellipse((5, 5, logo_diameter + 15, logo_diameter + 15), fill=(0, 0, 0, 180))
    shadow = shadow.filter(ImageFilter.GaussianBlur(8))
    
    # Paste shadow
    bg.paste(shadow, (pos_x - 10, pos_y - 10), shadow)
    
    # Create golden outer rim
    rim_img = Image.new("RGBA", (logo_diameter + 8, logo_diameter + 8), (0, 0, 0, 0))
    draw_rim = ImageDraw.Draw(rim_img)
    draw_rim.ellipse((0, 0, logo_diameter + 7, logo_diameter + 7), fill=(212, 175, 55, 255), outline=(255, 223, 128, 255), width=2)
    bg.paste(rim_img, (pos_x - 4, pos_y - 4), rim_img)
    
    # Paste real logo
    bg.paste(logo, (pos_x, pos_y), mask)
    
    # Convert to RGB and save JPEG
    final_rgb = bg.convert("RGB")
    final_rgb.save(out_path, "JPEG", quality=95, optimize=True)
    print(f"Saved: {out_path} ({os.path.getsize(out_path)} bytes)")

if __name__ == "__main__":
    brain_dir = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a"
    logo = "/Users/zinehamdi/Sites/localhost/tuni-olive-hub/public/images/zintoop-logo.png"
    
    ar_bg = os.path.join(brain_dir, "og_register_ar_v2_1789560055996.jpg")
    fr_bg = os.path.join(brain_dir, "og_register_fr_v2_1789560009101.jpg")
    en_bg = os.path.join(brain_dir, "og_register_en_v2_1789559959499.jpg")
    
    # Process for AR, FR, EN
    process_card(ar_bg, logo, "public/images/zintoop-register-card-ar.jpg", logo_diameter=135, logo_top=24)
    process_card(fr_bg, logo, "public/images/zintoop-register-card-fr.jpg", logo_diameter=135, logo_top=24)
    process_card(en_bg, logo, "public/images/zintoop-register-card-en.jpg", logo_diameter=135, logo_top=24)
    
    # Also save to artifacts for preview
    process_card(ar_bg, logo, os.path.join(brain_dir, "zintoop_register_preview_ar.jpg"), logo_diameter=135, logo_top=24)
    process_card(fr_bg, logo, os.path.join(brain_dir, "zintoop_register_preview_fr.jpg"), logo_diameter=135, logo_top=24)
    process_card(en_bg, logo, os.path.join(brain_dir, "zintoop_register_preview_en.jpg"), logo_diameter=135, logo_top=24)
