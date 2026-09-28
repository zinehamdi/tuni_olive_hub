import sys
from PIL import Image, ImageOps, ImageFilter, ImageDraw

src_path = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/.user_uploaded/media_1789555092043.png"
out_path = "/Users/zinehamdi/.gemini/antigravity-ide/brain/ad3e926f-b568-4719-947d-8def1ead108a/og_register_card_user_final.jpg"
out_public = "public/images/zintoop-register-card-user.jpg"

img = Image.open(src_path).convert("RGBA")
w, h = img.size

# Target OG standard dimensions
canvas_w, canvas_h = 1200, 630

# We want the form and logo to look sharp and prominent
# The content is vertically centered in the 1024x938 image
# Let's crop slightly from top and bottom empty margins to maximize the form card size!
# Bounding box of content in the screenshot:
# Logo starts at y ~ 20
# Form ends at y ~ 880
# Total content height is ~870px out of 938px

# Let's scale so it fills the 630 height gracefully
target_h = 630
scale = target_h / h
target_w = int(w * scale)

resized = img.resize((target_w, target_h), Image.Resampling.LANCZOS)

# Create canvas with matching top/bottom background
# The background in the screenshot has a subtle light gradient
canvas = Image.new("RGB", (canvas_w, canvas_h), (248, 250, 252))
draw = ImageDraw.Draw(canvas)

# Sample the background color from the left column of the resized screenshot
for y in range(canvas_h):
    pixel_color = resized.getpixel((5, y))[:3]
    draw.line([(0, y), (canvas_w, y)], fill=pixel_color)

# Paste the screenshot in the exact center
offset_x = (canvas_w - target_w) // 2
offset_y = 0

canvas.paste(resized, (offset_x, offset_y), resized)

# Save high quality JPEG
canvas.save(out_path, "JPEG", quality=96)
canvas.save(out_public, "JPEG", quality=96)
print("Saved perfect seamless OG card!")
