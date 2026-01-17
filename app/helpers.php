<?php
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }

function redirect(string $path): void {
  header("Location: {$path}");
  exit;
}

function slugify(string $text): string {
  $text = strtolower(trim($text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

function notify_user(int $user_id, string $title, string $message = null, string $link = null): bool {
  try {
    $stmt = db()->prepare("INSERT INTO notifications (user_id,title,message,link) VALUES (:uid,:title,:msg,:link)");
    return (bool)$stmt->execute([':uid'=>$user_id,':title'=>$title,':msg'=>$message,':link'=>$link]);
  } catch (Exception $e) {
    return false;
  }
}
