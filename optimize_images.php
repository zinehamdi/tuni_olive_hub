<?php
$dir = __DIR__ . '/../domains/toop.kairouanhub.com/public_html/images';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) continue;

    $src = ($ext === 'png') ? imagecreatefrompng($path) : imagecreatefromjpeg($path);
    if (!$src) continue;

    $w = imagesx($src);
    $h = imagesy($src);
    $max = 1920;
    if ($w > $max) {
        $newh = intval($h * ($max / $w));
        $dst = imagecreatetruecolor($max, $newh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $max, $newh, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }
    if ($ext === 'png') {
        imagepng($src, $path, 8); // compress PNG
    } else {
        imagejpeg($src, $path, 82); // compress JPG
    }
    imagedestroy($src);
}
echo "✅ Optimization done.\n";
