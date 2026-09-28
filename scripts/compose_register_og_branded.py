import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

src_path = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/.user_uploaded/media_1789555092043.png"
out_path = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/og_register_card_branded_1200x630.jpg"
out_public = "public/images/zintoop-register-card-v8.jpg"

img = Image.open(src_path).convert("RGBA")
w, h = img.size

canvas_w, canvas_h = 1200, 630
canvas = Image.new("RGBA", (canvas_w, canvas_h), (243, 245, 248, 255))
draw = ImageDraw.Draw(canvas)

# Create a rich modern background gradient
for y in range(canvas_h):
    # Smooth subtle elegant olive-tinted off-white gradient
    ratio = y / canvas_h
    r = int(248 - ratio * 12)
    g = int(250 - ratio * 8)
    b = int(246 - ratio * 15)
    draw.line([(0, y), (canvas_w, y)], fill=(r, g, b, 255))

# Scale screenshot to fit nicely in the center (target height 610)
target_h = 615
scale = target_h / h
target_w = int(w * scale)
resized = img.resize((target_w, target_h), Image.Resampling.LANCZOS)

offset_x = (canvas_w - target_w) // 2
offset_y = (canvas_h - target_h) // 2

# Add soft decorative cards or badges on left and right for high-converting social preview
# Left Card: 100% Free
# Right Card: 0% Commission

# Left Pill Badge (x: 50, y: 220, w: 220, h: 180)
# Right Pill Badge (x: 930, y: 220, w: 220, h: 180)

def draw_badge(draw_ctx, x, y, w_box, h_box, title_top, sub_text, icon_color, border_color):
    # Rounded glassmorphism card
    draw_ctx.rounded_rectangle([x, y, x + w_box, y + h_box], radius=24, fill=(255, 255, 255, 240), outline=border_color, width=2)
    # Subtle inner accent
    draw_ctx.rounded_rectangle([x+10, y+10, x + w_box - 10, y + 50], radius=14, fill=icon_color)

# Draw left and right aesthetic highlight badges
draw_badge(draw, 45, 200, 225, 210, "100%", "مجاني 100%", (106, 143, 59, 30), (106, 143, 59, 100))
draw_badge(draw, 930, 200, 225, 210, "0%", "0% عمولة", (217, 119, 6, 30), (217, 119, 6, 100))

# Try loading Arabic/system font
font_title = None
font_sub = None
for font_path in [
    "/System/Library/Fonts/Supplemental/Arial Bold.ttf",
    "/System/Library/Fonts/Supplemental/Arial.ttf",
    "/Library/Fonts/Arial.ttf"
]:
    if os.path.exists(font_path):
        try:
            font_title = ImageFont.truetype(font_path, 34)
            font_sub = ImageFont.truetype(font_path, 20)
            font_pill = ImageFont.truetype(font_path, 16)
            break
        except:
            pass

if font_title:
    # Left texts
    draw.text((157, 228), "100%", fill=(106, 143, 59), font=font_title, anchor="mm")
    draw.text((157, 285), "FREE", fill=(30, 41, 59), font=font_sub, anchor="mm")
    draw.text((157, 320), "Registration", fill=(71, 85, 105), font=font_pill, anchor="mm")
    draw.text((157, 365), "تسجيل مجاني", fill=(106, 143, 59), font=font_sub, anchor="mm")

    # Right texts
    draw.text((1042, 228), "0%", fill=(217, 119, 6), font=font_title, anchor="mm")
    draw.text((1042, 285), "COMMISSION", fill=(30, 41, 59), font=font_sub, anchor="mm")
    draw.text((1042, 320), "Direct Trade", fill=(71, 85, 105), font=font_pill, anchor="mm")
    draw.text((1042, 365), "تواصل مباشر", fill=(217, 119, 6), font=font_sub, anchor="mm")

# Paste center screenshot
canvas.paste(resized, (offset_x, offset_y), resized)

# Convert to RGB and save
final_rgb = canvas.convert("RGB")
final_rgb.save(out_path, "JPEG", quality=95)
final_rgb.save(out_public, "JPEG", quality=95)
print("Branded OG image generated successfully!")
