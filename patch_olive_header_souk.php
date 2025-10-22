<?php
$path = 'resources/views/prices/index.blade.php';
$code = file_get_contents($path);

/*
 * After the olive card header (the green header div),
 * insert a line for the souk name (in gray, subtle style),
 * but only for olive cards (not olive oil).
 */
$pattern = '~(<!-- OLIVE CARD \(simple min/avg/max\) -->\s*<div class="p-5 space-y-3">)~u';
$insert  = "$1\n  <div class=\"text-sm text-gray-500\" data-souk-name-line>{{ \$row->souk_name ?? (\$row->souk ?? '') }}</div>\n";
$code = preg_replace($pattern, $insert, $code, 1);

file_put_contents($path, $code);
echo \"✓ Added souk name under olive card header in $path\\n\";
