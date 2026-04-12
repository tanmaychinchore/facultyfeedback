<?php
include ('config/db_config.php');
$res = $conn->query("SELECT * FROM question_set");
while($r = $res->fetch_assoc()) { 
    echo "SET: id={$r['id']}, code={$r['code']}, acad_year={$r['acad_year']}, semester={$r['semester']}\n"; 
}
$res = $conn->query("SELECT * FROM current_state");
while($r = $res->fetch_assoc()) { 
    echo "CURRENT STATE: acad_year={$r['acad_year']}, sem_type={$r['sem_type']}, status={$r['status']}\n"; 
}
?>
