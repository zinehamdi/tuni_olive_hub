import os
import glob
import json
import time
import requests
import mysql.connector
from PIL import Image
from datetime import datetime

# ==========================================
# ZINTOOP AUTOMATION & SEO SCRIPT
# ==========================================

# Database Configuration (Update with your Laravel DB credentials)
DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'tuni_olive_hub',
}

# Facebook / Instagram Graph API Configuration
PAGE_ID = 'YOUR_FACEBOOK_PAGE_ID'
IG_ACCOUNT_ID = 'YOUR_IG_ACCOUNT_ID'
ACCESS_TOKEN = 'YOUR_LONG_LIVED_ACCESS_TOKEN'

# Paths
PUBLIC_STORAGE_PATH = './public/storage/media'

# ------------------------------------------
# 1. Image Optimization (Auto-Convert to WebP)
# ------------------------------------------
def optimize_images_to_webp(directory):
    print("🚀 Starting Image Optimization to WebP...")
    extensions = ('*.jpg', '*.jpeg', '*.png')
    files_to_convert = []
    
    for ext in extensions:
        files_to_convert.extend(glob.glob(os.path.join(directory, '**', ext), recursive=True))
        
    for file_path in files_to_convert:
        try:
            filename = os.path.splitext(file_path)[0]
            webp_path = f"{filename}.webp"
            
            # Check if webp already exists
            if not os.path.exists(webp_path):
                img = Image.open(file_path)
                img.save(webp_path, 'webp', quality=80)
                print(f"✅ Converted: {file_path} -> {webp_path}")
                # Optional: os.remove(file_path) to delete original
        except Exception as e:
            print(f"❌ Error converting {file_path}: {e}")

# ------------------------------------------
# 2. SEO Titles Automation
# ------------------------------------------
def optimize_seo_titles():
    print("\n🚀 Starting SEO Title Optimization...")
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        
        # Fetch products without optimized SEO titles (Assuming you add an 'seo_title' column or we update generic titles)
        cursor.execute("SELECT id, type, variety, quality, is_organic FROM products")
        products = cursor.fetchall()
        
        for product in products:
            p_type = 'Olive Oil' if product['type'] == 'oil' else 'Olives'
            variety = product['variety'].capitalize() if product['variety'] else 'Tunisian'
            quality = f"- {product['quality'].capitalize()}" if product['quality'] else ''
            organic = "Organic " if product['is_organic'] else ""
            
            # Example: "Organic Olive Oil Chmlali - Extra Virgin - Sfax"
            seo_title = f"{organic}{p_type} {variety} {quality} - Premium Quality"
            
            # You would update your database here. For now, we simulate:
            print(f"✅ Generated SEO Title for Product ID {product['id']}: {seo_title}")
            
        cursor.close()
        conn.close()
    except Exception as e:
        print(f"❌ Database connection failed: {e}")

# ------------------------------------------
# 3. Auto-Posting to Social Media (Sfax Fair)
# ------------------------------------------
def auto_post_to_social_media():
    print("\n🚀 Preparing Auto-Post for Sfax Agriculture Fair...")
    
    # Post Schedule & Content
    posts = [
        {
            "time": "08:30",
            "message": "يا جماعة الخير، الفلاحة في تونس تبدلت! 🚜\nاليوم Zintoop يقدملك الحل اللي يخليك تتبع خدمتك وإنتاجك من تليفونك وأنت في معرض صفاقس.\nزورنا واكتشف الثورة الفلاحية! #Sfax #Agriculture #Zintoop #SIA2026",
        },
        {
            "time": "12:30",
            "message": "شنوة أكثر حاجة عجبتكم في معرض الفلاحة بصفاقس اليوم؟ 🌾\nشاركونا رأيكم واكتشفوا كيفاش Zintoop يسهل عليكم البيع والشراء. #AgriTech #Tunisie",
        },
        {
            "time": "19:30",
            "message": "نهارك متعب في المعرض؟ ارتاح وخلي Zintoop يخدم في بلاصتك. 📱\nبيع زيتك وزيتونك بأحسن الأسعار من غير ما تتحرك من بلاصتك. اكتشف المنصة توة! #Sfax #SIA2026",
        }
    ]
    
    current_time = datetime.now().strftime("%H:%M")
    
    for post in posts:
        # In a real scenario, you'd check if current_time >= post['time']
        print(f"🕒 Scheduled for {post['time']} | Content: {post['message'][:50]}...")
        
        # Facebook Graph API Call Example:
        """
        url = f"https://graph.facebook.com/{PAGE_ID}/feed"
        payload = {
            'message': post['message'],
            'access_token': ACCESS_TOKEN
        }
        response = requests.post(url, data=payload)
        """
        print("✅ Post scheduled successfully.")

# ------------------------------------------
# 4. Auto-Reply Bot for Comments
# ------------------------------------------
def auto_reply_to_comments():
    print("\n🚀 Starting Auto-Reply Bot...")
    # This function would typically run on a webhook or a polling loop.
    # Simulating fetching comments from Facebook Page
    
    simulated_comments = [
        {"id": "1", "message": "بقدّاش الزيت سيدي؟", "user": "Ahmed"},
        {"id": "2", "message": "مهتم بالتطبيق، كيفاش نصبو؟", "user": "Saleh"},
        {"id": "3", "message": "وين المعرض بالضبط؟", "user": "Ali"}
    ]
    
    keywords = ["قداش", "بقدّاش", "سوم", "مهتم", "كيفاش", "تفاصيل", "معلومات"]
    
    for comment in simulated_comments:
        if any(keyword in comment['message'] for keyword in keywords):
            reply_message = f"أهلاً بيك يا {comment['user']}! 🌾 Zintoop هو شريكك في النجاح. تفضل زور موقعنا https://zintoop.com بش تشوف الأسعار والتفاصيل كاملة، وإذا تستحق مساعدة خلي لنا رقمك في الخاص!"
            print(f"🤖 Replying to {comment['user']}: {reply_message}")
            
            # API Call Example to reply to a comment:
            """
            url = f"https://graph.facebook.com/{comment['id']}/comments"
            payload = {
                'message': reply_message,
                'access_token': ACCESS_TOKEN
            }
            requests.post(url, data=payload)
            """

if __name__ == "__main__":
    print("========================================")
    print(" ZINTOOP AUTOMATION SYSTEM INITIALIZING")
    print("========================================")
    
    optimize_images_to_webp(PUBLIC_STORAGE_PATH)
    optimize_seo_titles()
    auto_post_to_social_media()
    auto_reply_to_comments()
    
    print("\n✅ All Automation Tasks Completed.")
