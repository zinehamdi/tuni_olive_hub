#!/bin/bash

# Image Optimization Script for Tunisian Olive Hub
# تحسين الصور لمنصة الزيتون التونسية

echo "🖼️  Tunisian Olive Hub - Image Optimizer"
echo "========================================"
echo ""

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo "❌ ImageMagick not installed!"
    echo ""
    echo "To install on macOS:"
    echo "  brew install imagemagick"
    echo ""
    echo "Alternatively, use online tools:"
    echo "  https://tinypng.com"
    echo "  https://squoosh.app"
    exit 1
fi

# Navigate to images directory
cd public/images || exit 1

echo "📊 Current image sizes:"
echo "----------------------"
du -h zitounchamal.jpg zitzitoun.png dealbackground.png zitounroadbg.jpg mill-activity.jpg HighTidebg.jpeg 2>/dev/null
echo ""

# Calculate total size before
BEFORE=$(du -ch zitounchamal.jpg zitzitoun.png dealbackground.png zitounroadbg.jpg mill-activity.jpg HighTidebg.jpeg 2>/dev/null | grep total | cut -f1)

echo "🔄 Optimizing images..."
echo "----------------------"

# Optimize each image
echo "Processing zitounchamal.jpg (16MB)..."
convert zitounchamal.jpg -strip -quality 85 -resize 1920x1080\> zitounchamal_optimized.jpg

echo "Processing zitzitoun.png (3MB)..."
convert zitzitoun.png -strip -quality 85 -resize 1920x1080\> zitzitoun_optimized.png

echo "Processing dealbackground.png (2.4MB)..."
convert dealbackground.png -strip -quality 85 -resize 1920x1080\> dealbackground_optimized.png

echo "Processing zitounroadbg.jpg (2.1MB)..."
convert zitounroadbg.jpg -strip -quality 85 -resize 1920x1080\> zitounroadbg_optimized.jpg

echo "Processing mill-activity.jpg (1.7MB)..."
convert mill-activity.jpg -strip -quality 85 -resize 1920x1080\> mill-activity_optimized.jpg

echo "Processing HighTidebg.jpeg (488KB)..."
convert HighTidebg.jpeg -strip -quality 85 -resize 1920x1080\> HighTidebg_optimized.jpeg

echo ""
echo "✅ Optimization complete!"
echo ""

echo "📊 Optimized image sizes:"
echo "------------------------"
du -h *_optimized.* 2>/dev/null
echo ""

# Calculate total size after
AFTER=$(du -ch *_optimized.* 2>/dev/null | grep total | cut -f1)

echo "📈 Size comparison:"
echo "------------------"
echo "Before: $BEFORE"
echo "After:  $AFTER"
echo ""

echo "⚠️  Review the optimized images before replacing originals!"
echo "Open them in an image viewer to check quality."
echo ""
echo "If satisfied with quality, run:"
echo "  cd public/images"
echo "  mv zitounchamal_optimized.jpg zitounchamal.jpg"
echo "  mv zitzitoun_optimized.png zitzitoun.png"
echo "  mv dealbackground_optimized.png dealbackground.png"
echo "  mv zitounroadbg_optimized.jpg zitounroadbg.jpg"
echo "  mv mill-activity_optimized.jpg mill-activity.jpg"
echo "  mv HighTidebg_optimized.jpeg HighTidebg.jpeg"
echo ""
echo "Or run this script with --replace flag:"
echo "  ./optimize-images.sh --replace"
echo ""

# If --replace flag is provided, replace originals
if [ "$1" == "--replace" ]; then
    echo "🔄 Replacing original images..."
    mv zitounchamal_optimized.jpg zitounchamal.jpg
    mv zitzitoun_optimized.png zitzitoun.png
    mv dealbackground_optimized.png dealbackground.png
    mv zitounroadbg_optimized.jpg zitounroadbg.jpg
    mv mill-activity_optimized.jpg mill-activity.jpg
    mv HighTidebg_optimized.jpeg HighTidebg.jpeg
    echo "✅ Original images replaced!"
    echo ""
    echo "📊 Final image sizes:"
    du -h zitounchamal.jpg zitzitoun.png dealbackground.png zitounroadbg.jpg mill-activity.jpg HighTidebg.jpeg
    echo ""
    echo "🚀 Page load speed should now be significantly faster!"
fi
