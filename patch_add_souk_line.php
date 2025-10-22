<?php
$path = 'resources/views/prices/index.blade.php';
$code = file_get_contents($path);

/*
 * For OLIVE cards only, add a line showing the souk name right after:
 * <!-- OLIVE CARD (simple min/avg/max) --> + <div class="p-5 space-y-3">
 * It won't duplicate if already inserted (uses a data marker).
 */
if (strpos($code, 'data-olive-souk-line') === false) {
  $pattern = '~(<!-- OLIVE CARD \(simple min/avg/max\) -->\s*<div class="p-5 space-y-3">)~u';
  $insert  = "$1\n  <div class=\"text-sm text-gray-500\" data-olive-souk-line>{{ \$row->souk_name ?? (\$row->souk ?? '') }}</div>\n";
  $code = preg_replace($pattern, $insert, $code, 1);
}

file_put_contents($path, $code);
echo \"✓ Souk line added for olive cards in $path\n\";
