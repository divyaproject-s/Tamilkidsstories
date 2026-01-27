<?php
include "includes/db.php";

$story_id = (int)($_POST['story_id'] ?? 0);
$user_ip = $_SERVER['REMOTE_ADDR'];

if (!$story_id) {
  echo json_encode(['status' => 'error']);
  exit;
}

// Check already favourited
$check = $conn->prepare("SELECT id FROM favourites WHERE story_id=? AND user_ip=?");
$check->bind_param("is", $story_id, $user_ip);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
  // Remove favourite
  $del = $conn->prepare("DELETE FROM favourites WHERE story_id=? AND user_ip=?");
  $del->bind_param("is", $story_id, $user_ip);
  $del->execute();

  echo json_encode(['status' => 'removed']);
} else {
  // Add favourite
  $add = $conn->prepare("INSERT INTO favourites (story_id, user_ip) VALUES (?, ?)");
  $add->bind_param("is", $story_id, $user_ip);
  $add->execute();

  echo json_encode(['status' => 'added']);
}
