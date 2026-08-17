<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

$articles = Article::all();
$toTranslate = [];

foreach ($articles as $article) {
    $title = is_string($article->title) ? json_decode($article->title, true) : $article->title;
    $content = is_string($article->content) ? json_decode($article->content, true) : $article->content;
    $category = is_string($article->category) ? json_decode($article->category, true) : $article->category;

    if (empty($title['en']) || empty($title['fr']) || empty($title['ar']) ||
        empty($content['en']) || empty($content['fr']) || empty($content['ar']) ||
        empty($category['en']) || empty($category['fr']) || empty($category['ar'])) {
        $toTranslate[] = [
            'id' => $article->id,
            'title' => $title,
            'content' => $content,
            'category' => $category,
        ];
    }
}
echo json_encode($toTranslate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
