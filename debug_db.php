<?php
include ('config/db_config.php');
echo "Tables:\n";
$res = $conn->query("SHOW TABLES");
while($r = $res->fetch_array()) { echo $r[0] . "\n"; }

echo "\n--- question_set ---\n";
$res = $conn->query("SELECT * FROM question_set");
while($r = $res->fetch_assoc()) { print_r($r); }

echo "\n--- question_heading ---\n";
$res = $conn->query("SELECT * FROM question_heading");
while($r = $res->fetch_assoc()) { print_r($r); }

echo "\n--- current_state ---\n";
$res = $conn->query("SELECT * FROM current_state");
while($r = $res->fetch_assoc()) { print_r($r); }

echo "\n--- feedback_question ---\n";
$res = $conn->query("SELECT * FROM feedback_question");
while($r = $res->fetch_assoc()) { print_r($r); }

echo "\n--- feedback_option ---\n";
$res = $conn->query("SELECT * FROM feedback_option");
while($r = $res->fetch_assoc()) { print_r($r); }

?>
