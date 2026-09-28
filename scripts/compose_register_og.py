import sys
from PIL import Image, ImageOps, ImageFilter, ImageDraw

src_path = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/.user_uploaded/media_1789555092043.png"
out_path_1 = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/og_register_user_clean_1200x630.jpg"
out_path_public = "public/images/zintoop-register-card-v8.jpg"

img = Image.open(src_path).convert("RGBA")
w, h = img.size

# Let's create a 1200x630 canvas
canvas_w, canvas_h = 1200, 630

# Crop excess empty margin from top/bottom if any, but keep logo and full card
# In the original 1024x938, logo is at top, card is in middle, copyright is at bottom
# Let's scale img so that it fits height 610px
target_h = 610
scale = target_h / h
target_w = int(w * scale)

resized = img.resize((target_w, target_h), Image.Resampling.LANCZOS)

# Create canvas with subtle luxury background matching the app
# Top color from original screenshot (around y=10)
bg_color = img.getpixel((10, 10))[:3]

canvas = Image.new("RGB", (canvas_w, canvas_h), (248, 250, 252))
draw = ImageDraw.Draw(canvas)

# Create a smooth vertical gradient for background
for y in range(canvas_h):
    r = int(248 - (y / canvas_h) * 12)
    g = int(250 - (y / canvas_h) * 12)
    b = int(252 - (y / canvas_h) * 14)
    draw.line([(0, y), (canvas_w, y)], fill=(r, g, b))

# Paste resized screenshot centered
offset_x = (canvas_w - target_w) // 2
offset_y = (canvas_h - target_h) // 2

# Create soft drop shadow behind the card if needed, or composite directly
canvas.paste(resized, (offset_x, offset_y), resized)

# Save both high quality JPG and PNG
canvas.save(out_path_1, "JPEG", quality=95)
canvas.save(out_path_public, "JPEG", quality=95)
print("Generated successfully!")
