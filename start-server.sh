#!/bin/bash

# Start Laravel server with custom PHP configuration
# This allows large file uploads (up to 100MB)

echo "🚀 Starting Tuni Olive Hub Server..."
echo "📁 Allowing large file uploads (up to 100MB)"
echo ""

php -c php-custom.ini artisan serve --host=0.0.0.0 --port=8000
