<?php
session_start();
include "includes/db.php";

/* ===== LOGIN CHECK ===== */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error', 'message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$story_id = isset($_POST['story_id']) ? intval($_POST['story_id']) : 0;

if (!$story_id) {
    echo json_encode(['status'=>'error', 'message'=>'Story ID missing']);
    exit;
}

/* ===== CHECK IF ALREADY FAVORITED ===== */
$stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id=? AND story_id=?");
$stmt->bind_param("ii", $user_id, $story_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Already favorited → remove
    $stmt2 = $conn->prepare("DELETE FROM favorites WHERE user_id=? AND story_id=?");
    $stmt2->bind_param("ii", $user_id, $story_id);
    $stmt2->execute();
    $status = 'removed';
} else {
    // Not favorited → add
    $stmt3 = $conn->prepare("INSERT INTO favorites (user_id, story_id, created_at) VALUES (?, ?, NOW())");
    $stmt3->bind_param("ii", $user_id, $story_id);
    $stmt3->execute();
    $status = 'added';
}

/* ===== GET TOTAL FAVORITES COUNT ===== */
$stmt4 = $conn->prepare("SELECT COUNT(*) AS total FROM favorites WHERE story_id=?");
$stmt4->bind_param("i", $story_id);
$stmt4->execute();
$totalFav = $stmt4->get_result()->fetch_assoc()['total'];

echo json_encode(['status'=>$status, 'total'=>$totalFav]);
?>
