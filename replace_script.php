<?php
$content = file_get_contents('/Users/zinehamdi/Sites/localhost/tuni-olive-hub/ChatbotController.php');

$start = strpos($content, 'private function handleFallbackIntent($message)');
$end = strpos($content, '}', strrpos($content, '}')); // Find the very last brace or something
// Wait, better to replace from "private function handleFallbackIntent($message)" to the end of the class.
$end_class = strrpos($content, '}'); // The closing brace of the class

$new_logic = file_get_contents('/Users/zinehamdi/Sites/localhost/tuni-olive-hub/chatbot_new_logic.php');
// Strip the <?php and class ChatbotController { at the beginning, and } at the end.
$new_logic = preg_replace('/^<\?php\s*class\s+ChatbotController\s*\{/s', '', $new_logic);
$new_logic = preg_replace('/\}\s*$/s', '', $new_logic);

$new_content = substr($content, 0, $start) . trim($new_logic) . "\n}\n";

file_put_contents('/Users/zinehamdi/Sites/localhost/tuni-olive-hub/ChatbotController.php', $new_content);
