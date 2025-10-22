<?php
$path = 'resources/views/prices/index.blade.php';
$code = file_get_contents($path);

/**
 * 1) OLIVE CARD: collapse duplicate governorate/souk rows to a single row.
 *    Keep one line that shows governorate (left) and souk (right).
 */
$dupPattern = '~(<div class="flex items-center justify-between mb-2">\s*<div class="text-sm font-semibold text-gray-800">\s*\{\{[^}]+\}\}\s*</div>\s*<div class="text-sm text-gray-500">\s*\{\{[^}]+\}\}\s*</div>\s*</div>\s*){2}~u';
$singleRow  = '<div class="flex items-center justify-between mb-2">
  <div class="text-sm font-semibold text-gray-800">{{ $row->governorate ?? ($row->gov ?? "") }}</div>
  <div class="text-sm text-gray-500">{{ $row->souk_name ?? ($row->souk ?? "") }}</div>
</div>';

$code = preg_replace($dupPattern, $singleRow, $code, 1);

/**
 * 2) OLIVE-OIL CARD: after the header (🫗 زيت الزيتون …chip),
 *    inject a small line with governorate and variety if present.
 *    We insert it only if that line is not already present.
 */
if (strpos($code, 'data-olive-oil-extra-line') === false) {
  $oilHeaderPattern =
    '~(<!-- OIL CARD.*?\n\s*<div class="p-6">\s*<div class="flex items-center justify-between mb-4">.*?</div>\s*)~su';
  $oilExtra =
    "$1" .
    "<div class=\"text-xs text-gray-500 mb-3\" data-olive-oil-extra-line>" .
    "{{ \$row->governorate ?? (\$row->gov ?? '') }}" .
    "@if(!empty(\$row->variety)) — {{ \$row->variety }} @endif" .
    "</div>\n";
  $code = preg_replace($oilHeaderPattern, $oilExtra, $code, 1);
}

file_put_contents($path, $code);
echo \"✓ Patch applied to $path\\n\";
