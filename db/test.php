<!DOCTYPE html>
<html>
<head>
    <title>Faculty Feedback Report</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        @media all {
            .page-break { display: none; }
        }
        @page {
            margin: 2cm; /* Standard margin for ALL pages */
        }
        @media print {
            .page-break { display: block; page-break-before: always; }
            body { 
                background-color: #fff; 
                margin: 0;
                padding: 0;
            }
            .report-card { background-color: #fff !important; border: 1px solid #162252 !important; break-inside: avoid; }
            .heading-section { break-inside: avoid; }
            hr { border-top: 1px solid #ccc !important; }
        }
        body { font-family: 'Roboto', sans-serif; padding: 20px; }
        .report-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
        }
        .report-card {
            flex: 0 1 calc(50% - 10px);
            max-width: calc(50% - 10px);
            min-width: 300px;
            border: 0.5px solid #162252;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .question-text {
            font-weight: bold;
            text-align: left;
            margin-bottom: 20px;
            color: #162252;
            width: 100%;
            align-self: flex-start;
        }
        .percentage-text {
            margin-top: 15px;
            color: #162252;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        .heading-average {
            margin-top: 40px;
            margin-bottom: 40px;
            text-align: center;
            width: 100%;
        }
        h4 {
            margin-top: 30px !important;
            margin-bottom: 20px !important;
            page-break-after: avoid; /* Keep heading with charts */
        }
        .heading-group {
            break-inside: auto; /* Allow break between rows, but title binds to row 1 */
        }
        canvas {
            max-width: 100%;
            height: auto !important;
        }
    </style>
    <script>
    function wrapText(context, text, x, y, maxWidth, lineHeight) {
        var words = text.split(' ');
        var line = '';
        for(var n = 0; n < words.length; n++) {
            var testLine = line + words[n] + ' ';
            var metrics = context.measureText(testLine);
            var testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';
                y += lineHeight;
            } else {
                line = testLine;
            }
        }
        context.fillText(line, x, y);
    }
    </script>
</head>
<body>

<?php
session_start();
include ('../config/db_config.php');

$f_id = isset($_GET["f_id"]) ? $_GET["f_id"] : (isset($_POST["f_id"]) ? $_POST["f_id"] : null);
$dept_id = isset($_SESSION['dept_id']) ? $_SESSION['dept_id'] : (isset($_GET['dept_id']) ? $_GET['dept_id'] : null);

if (!$f_id || !$dept_id) {
    echo "<h3>Error: Missing Faculty ID or Department ID.</h3>";
    exit;
}

// Get Department Name
$sql = "SELECT dept_name from department where dept_id='$dept_id'";
$res = $conn->query($sql);
$dept_name = "Department";
if($r = $res->fetch_assoc()){
    $dept_name = $r['dept_name'];
    $dept_map = [
        "COMPS" => "Computer Engineering",
        "IT" => "Information Technology",
        "ETRX" => "Electronics Engineering",
        "EXTC" => "Electronics and Telecommunication Engineering",
        "MECH" => "Mechanical Engineering",
        "S&H" => "Science and Humanities",
        "CSBS" => "CSBS", "VLSI" => "VLSI", "EXCP" => "EXCP", "RAI" => "RAI"
    ];
    if(isset($dept_map[$dept_name])) $dept_name = $dept_map[$dept_name];
}

// Get Current State
$sql = "SELECT `status`,acad_year,sem_type from current_state where dept_id='$dept_id'";
$res = $conn->query($sql);
$status = 0; $acad_year = ""; $sem_type = "";
if($r = $res->fetch_assoc()){
    $status = (int)$r['status'];
    $acad_year = $r['acad_year'];
    $sem_type = $r['sem_type'];
}

// Get Faculty Name
$fname = ""; $lname = "";
$s = "SELECT fname,lname from faculty where f_id='$f_id'";
$r = $conn->query($s);
if($res_f = $r->fetch_assoc()){
    $fname = $res_f['fname'];
    $lname = $res_f['lname'];
}

// Header Section
?>
<div style="text-align: center;">
    <p style="font-size: 24px;"><strong>K.J. SOMAIYA SCHOOL OF ENGINEERING, MUMBAI</strong></p>
    <p style="font-size: 18px;"><strong>(A Constituent College of Somaiya Vidyavihar University)</strong></p>
    <hr>
    <p style="font-size: 18px;"><strong>Department of <?php echo $dept_name; ?></strong></p>
    <hr>
    <p style="font-size: 24px;"><strong>FACULTY FEEDBACK REPORT (<?php echo $acad_year; ?>)</strong></p>
    <p style="font-size: 20px; margin: 2px 0;"><strong><?php echo $sem_type; ?> Semester (<?php echo $status == 0 ? "Mid Term" : "End Term"; ?>)</strong></p>
    <hr style="margin: 5px 0;">
</div>
<b style='font-size: 16px;'>Faculty Name: </b><strong style='color:#162252; font-size: 16px; '><?php echo $fname." ".$lname; ?></strong><hr style="margin: 5px 0;">

<?php
// Regular Courses
$sem_filter = ($sem_type == 'Odd') ? "(1,3,5,7)" : "(2,4,6,8)";
$s2 = "SELECT course_code,class,sem,section_or_batch from courses_faculty where f_id='$f_id' and sem in $sem_filter and acad_year='$acad_year' and dept_id='$dept_id'";
$r2 = $conn->query($s2);

while($res2 = $r2->fetch_assoc()):
    $c_id = $res2['course_code'];
    $class = $res2["class"];
    $sem = $res2["sem"];
    $section_or_batch = $res2["section_or_batch"];

    $a = "SELECT roll_no from student where class='$class' and sem='$sem' and (batch='$section_or_batch' or section='$section_or_batch') and acad_year='$acad_year'";
    $b = $conn->query($a);
    $roll_no = [];
    while($s = $b->fetch_assoc()) $roll_no[] = $s["roll_no"];
    $roll_no_list = count($roll_no) > 0 ? implode(',', $roll_no) : "'0'";

    $s3 = "SELECT c_name from subject where course_code='$c_id' and class='$class' and acad_year='$acad_year' and sem='$sem'";
    $r3 = $conn->query($s3);
    $cname = ($res3 = $r3->fetch_assoc()) ? $res3['c_name'] : "Unknown Course";

    echo "<b style='font-size: 18px;'>Course Name: </b><strong style='color:#162252;font-size: 16px;'>".$cname."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>".(($class=='FY_A' || $class=='FY_B')?"FY":$class)."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<b style='font-size: 18px;'>Sem: </b><strong style='color:#162252;font-size: 16px;'>".$sem."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<b style='font-size: 18px;'>Section/ Batch: </b><strong style='color:#162252;font-size: 16px;'>".$section_or_batch."</strong><br>";

    $resp_table = ($status == 0) ? 'response_midsem' : 'response_endsem';
    $resp_sql = "SELECT count(DISTINCT roll_no) as total_resp FROM $resp_table WHERE course_code='$c_id' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
    $resp_res = $conn->query($resp_sql);
    $total_responded = ($row = $resp_res->fetch_assoc()) ? $row['total_resp'] : 0;
    echo "<br><b style='font-size: 18px;'>Total Students Responded: </b><strong style='color:#162252;font-size: 16px;'>".$total_responded."</strong><br><hr>&nbsp;&nbsp;&nbsp;&nbsp;";

    $c_type = 'TH'; // default
    $code_upper = strtoupper($c_id);
    if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) $c_type = 'LAB';
    elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false) $c_type = 'TU';

    $sem_parity = ($sem % 2 == 0) ? 2 : 1;
    $sql_q = "SELECT q.id as q_id, q.question_text as question, h.heading, q.is_text_input
              FROM question_set qs
              JOIN question_heading h ON qs.id = h.question_set_id
              JOIN feedback_question q ON h.id = q.heading_id
              WHERE qs.code = '$c_type' AND qs.acad_year = '$acad_year' AND qs.semester = '$sem_parity'
              ORDER BY h.heading_order ASC, q.question_order ASC";
    $res_q = $conn->query($sql_q);
    $questions_by_heading = [];
    while($row = $res_q->fetch_assoc()) $questions_by_heading[$row['heading']][] = $row;

    $overall_achieved = 0; $overall_max = 0;

    foreach ($questions_by_heading as $heading => $questions) {
        $has_numerical = false;
        foreach($questions as $q) { if(!$q['is_text_input']) { $has_numerical = true; break; } }
        if (!$has_numerical) continue;

        echo "<div class='heading-group'>";
        echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px;'>" . htmlspecialchars($heading) . "</h4>";
        $h_achieved = 0; $h_max = 0;
        echo '<div class="report-grid">';
        foreach ($questions as $q) {
            $q_id = $q['q_id'];
            $q_text = $q['question'];
            if ($q['is_text_input']) continue; // Skip text questions in grid

            $s_opt = "SELECT option_number, option_text FROM feedback_option WHERE question_id='$q_id' ORDER BY option_number ASC";
            $res_opt = $conn->query($s_opt);
            $names = []; $vals = []; $counts = array_fill(0, $res_opt->num_rows, 0);
            $i = 0;
            while($ro = $res_opt->fetch_assoc()) { $names[] = $ro['option_text']; $vals[] = (int)$ro['option_number']; $i++; }
            $max_v = empty($vals) ? 0 : max($vals);

            $check_sql = "SELECT response FROM $resp_table WHERE q_id='$q_id' AND course_code='$c_id' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
            $res_resp = $conn->query($check_sql);
            $q_stu_count = 0;
            if ($res_resp) {
                while($rr = $res_resp->fetch_assoc()) {
                    $val = (int)$rr['response'];
                    $idx = array_search($val, $vals);
                    if ($idx !== false) $counts[$idx]++;
                    $q_stu_count++;
                }
            }
            $q_ach = 0;
            for($k=0; $k<count($counts); $k++) $q_ach += $vals[$k] * $counts[$k];
            $h_achieved += $q_ach; $h_max += ($max_v * $q_stu_count);
            $overall_achieved += $q_ach; $overall_max += ($max_v * $q_stu_count);
            $q_pct = ($max_v > 0 && $q_stu_count > 0) ? ($q_ach / ($max_v * $q_stu_count)) * 100 : 0;
            ?>
            <div class="report-card">
                <div class="question-text"><?= htmlspecialchars($q_text) ?></div>
                <canvas id='c_<?= $c_id.$q_id ?>' width="400" height="220"></canvas>
                <?php if($q_stu_count > 0): ?>
                    <div class="percentage-text">Percentage: <?= number_format($q_pct, 2) ?>%</div>
                <?php endif; ?>
            </div>
            <script>
            (function() {
                var ctx = document.getElementById('c_<?= $c_id.$q_id ?>').getContext('2d');
                var names = <?= json_encode($names) ?>;
                var vals = <?= json_encode($vals) ?>;
                var counts = <?= json_encode($counts) ?>;
                var total = <?= $q_stu_count ?>;
                var data = [];
                for(var m=0; m<names.length; m++) data.push([vals[m] + " - " + names[m], total > 0 ? (counts[m]/total)*100 : 0]);
                
                var bar_w = 30; var y_g = 60; var bar_g = 80; var startX = 20;
                var canv = ctx.canvas;
                canv.width = data.length * bar_g + startX + 20;
                var base_y = canv.height - y_g;

                ctx.moveTo(startX-5, base_y); ctx.lineTo(canv.width, base_y); ctx.stroke();
                ctx.textAlign = 'center'; ctx.textBaseline = 'top'; ctx.font = '12px Arial';
                
                for(var i=0; i<data.length; i++) {
                    var cx = startX + (bar_w/2);
                    ctx.fillStyle = '#162252';
                    wrapText(ctx, data[i][0], cx, base_y + 5, bar_g - 10, 14);
                    var bar_h = (data[i][1] * 1.2);
                    var ty = base_y - bar_h;
                    if(total > 0) {
                        ctx.fillStyle = '#000';
                        ctx.fillText(data[i][1].toFixed(1)+"%", cx, ty - 20);
                    }
                    ctx.fillStyle = '#2E5090';
                    ctx.fillRect(startX, ty, bar_w, bar_h);
                    startX += bar_g;
                }
            })();
            </script>
            <?php
        }
        echo '</div>';
        if ($h_max > 0) {
            $h_pct = ($h_achieved / $h_max) * 100;
            echo "<p class='heading-average'><b>Average percentage for ".htmlspecialchars($heading).": <span style='color:#162252;'>".number_format($h_pct, 2)."%</span></b></p>";
        }
        echo "</div>"; // Close heading-section
    } // End numerical heading loop
    
    // 2. Find and Print Open-Ended Heading + Comments
    foreach ($questions_by_heading as $heading => $questions) {
        $has_text = false;
        foreach($questions as $q) { if($q['is_text_input']) { $has_text = true; break; } }
        
        if($has_text) {
            echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 30px;'>" . htmlspecialchars($heading) . "</h4>";
            
            $comm_table = ($status == 0) ? 'comment_midsem' : 'comment_endsem';
            $c_sql = "SELECT comment FROM $comm_table WHERE course_code='$c_id' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
            $c_res = $conn->query($c_sql);
            if($c_res && $c_res->num_rows > 0) {
                while($cr = $c_res->fetch_assoc()) {
                    $txt = trim($cr['comment']);
                    if(!$txt || in_array(strtolower($txt), ['-','--','na','none','nil','.','..'])) continue;
                    echo "<b>- ".htmlspecialchars($txt)."</b><br>";
                }
            } else {
                echo "<i>No comments recorded.</i><br>";
            }
        }
    }

    // 3. Overall Percentage
    $o_pct = ($overall_max > 0) ? ($overall_achieved / $overall_max) * 100 : 0;
    echo "<br><p style='text-align:center;'><b style='font-size: 18px;'>Faculty Evaluation (Overall): </b><strong style='color:#162252; font-size: 20px;'>".number_format($o_pct, 2)."%</strong></p>";
    
    // 4. Footer
    echo '<div style="text-align: center; margin-top: 10px;"><hr><footer>************ This is a System Generated Report ************</footer><hr></div>';
    
    echo "<div class='page-break'></div><hr><br>";
endwhile;

// Electives
$s_el = "SELECT electiveID, electiveName, sem from electives where f_id='$f_id' and acad_year='$acad_year' and dept_id='$dept_id' and sem in $sem_filter";
$r_el = $conn->query($s_el);
while($re = $r_el->fetch_assoc()):
    $eid = $re['electiveID']; $ename = $re['electiveName']; $esem = $re['sem'];
    $a_el = "SELECT roll_no from student where sem='$esem' and (elective_or_IDC_ID='$eid' or elective_or_IDC_BatchID='$eid' or elective_or_IDC_ID1='$eid' or elective_or_IDC_ID2='$eid' or elective_or_IDC_ID3='$eid' or elective_or_IDC_ID4='$eid' or elective_or_IDC_ID5='$eid' or elective_or_IDC_BatchID1='$eid' or elective_or_IDC_BatchID2='$eid' or elective_or_IDC_BatchID3='$eid' or elective_or_IDC_BatchID4='$eid' or elective_or_IDC_BatchID5='$eid') and acad_year='$acad_year'";
    $b_el = $conn->query($a_el);
    $rnos = []; while($sr = $b_el->fetch_assoc()) $rnos[] = $sr['roll_no'];
    $r_list = count($rnos) > 0 ? implode(',', $rnos) : "'0'";

    echo "<b style='font-size: 18px;'>Course Name: </b><strong style='color:#162252;font-size: 16px;'>".$ename." (Elective/IDC)</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<b style='font-size: 18px;'>Sem: </b><strong style='color:#162252;font-size: 16px;'>".$esem."</strong><br>";
    
    $resp_sql_el = "SELECT count(DISTINCT roll_no) as total_resp FROM $resp_table WHERE course_code='$eid' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($r_list)";
    $resp_res_el = $conn->query($resp_sql_el);
    $total_resp_el = ($row_el = $resp_res_el->fetch_assoc()) ? $row_el['total_resp'] : 0;
    echo "<br><b style='font-size: 18px;'>Total Students Responded: </b><strong style='color:#162252;font-size: 16px;'>".$total_resp_el."</strong><br><hr>&nbsp;&nbsp;&nbsp;&nbsp;";

    $e_type = 'TH';
    $e_upper = strtoupper($eid);
    if($e_upper[0] == 'L' || strpos($e_upper, 'LAB') !== false) $e_type = 'LAB';

    $sem_par = ($esem % 2 == 0) ? 2 : 1;
    $sql_qe = "SELECT q.id as q_id, q.question_text as question, h.heading, q.is_text_input
               FROM question_set qs JOIN question_heading h ON qs.id = h.question_set_id
               JOIN feedback_question q ON h.id = q.heading_id
               WHERE qs.code = '$e_type' AND qs.acad_year = '$acad_year' AND qs.semester = '$sem_par'
               ORDER BY h.heading_order ASC, q.question_order ASC";
    $res_qe = $conn->query($sql_qe);
    $q_be = []; while($rqe = $res_qe->fetch_assoc()) $q_be[$rqe['heading']][] = $rqe;

    $o_ach_e = 0; $o_max_e = 0;
    foreach ($q_be as $h_e => $ques_e) {
        $has_num_e = false;
        foreach($ques_e as $qe) { if(!$qe['is_text_input']) { $has_num_e = true; break; } }
        if (!$has_num_e) continue;

        echo "<div class='heading-group'>";
        echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px;'>" . htmlspecialchars($h_e) . "</h4>";
        $he_ach = 0; $he_max = 0;
        echo '<div class="report-grid">';
        foreach ($ques_e as $qe) {
            $qid_e = $qe['q_id']; $qtx_e = $qe['question'];
            if($qe['is_text_input']) continue;
            
            $s_opte = "SELECT option_number, option_text FROM feedback_option WHERE question_id='$qid_e' ORDER BY option_number ASC";
            $res_opte = $conn->query($s_opte);
            $nese = []; $vse = []; $cse = array_fill(0, $res_opte->num_rows, 0);
            while($roe = $res_opte->fetch_assoc()) { $nese[] = $roe['option_text']; $vse[] = (int)$roe['option_number']; }
            $max_ve = empty($vse) ? 0 : max($vse);

            $csql_e = "SELECT response FROM $resp_table WHERE q_id='$qid_e' AND course_code='$eid' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($r_list)";
            $rres_e = $conn->query($csql_e);
            $q_stu_e = 0;
            if($rres_e) {
                while($rr_e = $rres_e->fetch_assoc()) {
                    $v_e = (int)$rr_e['response']; $idx_e = array_search($v_e, $vse);
                    if($idx_e !== false) $cse[$idx_e]++;
                    $q_stu_e++;
                }
            }
            $qae = 0; for($ke=0; $ke<count($cse); $ke++) $qae += $vse[$ke] * $cse[$ke];
            $he_ach += $qae; $he_max += ($max_ve * $q_stu_e);
            $o_ach_e += $qae; $o_max_e += ($max_ve * $q_stu_e);
            $qpe = ($max_ve > 0 && $q_stu_e > 0) ? ($qae / ($max_ve * $q_stu_e)) * 100 : 0;
            ?>
            <div class="report-card">
                <div class="question-text"><?= htmlspecialchars($qtx_e) ?></div>
                <canvas id='e_<?= $eid.$qid_e ?>' width="400" height="220"></canvas>
                <?php if($q_stu_e > 0): ?>
                    <div class="percentage-text">Percentage: <?= number_format($qpe, 2) ?>%</div>
                <?php endif; ?>
            </div>
            <script>
            (function() {
                var ctx = document.getElementById('e_<?= $eid.$qid_e ?>').getContext('2d');
                var names = <?= json_encode($nese) ?>;
                var vals = <?= json_encode($vse) ?>;
                var counts = <?= json_encode($cse) ?>;
                var total = <?= $q_stu_e ?>;
                var data = [];
                for(var m=0; m<names.length; m++) data.push([vals[m] + " - " + names[m], total > 0 ? (counts[m]/total)*100 : 0]);
                var bar_w = 30; var y_g = 60; var bar_g = 80; var startX = 20;
                var canv = ctx.canvas; canv.width = data.length * bar_g + startX + 20;
                var base_y = canv.height - y_g;
                ctx.moveTo(startX-5, base_y); ctx.lineTo(canv.width, base_y); ctx.stroke();
                ctx.textAlign = 'center'; ctx.font = '12px Arial'; ctx.textBaseline = 'top';
                for(var i=0; i<data.length; i++) {
                    var cx = startX + (bar_w/2);
                    ctx.fillStyle = '#162252'; wrapText(ctx, data[i][0], cx, base_y + 5, bar_g - 10, 14);
                    var bh = (data[i][1] * 1.2); var ty = base_y - bh;
                    if(total > 0) { ctx.fillStyle = '#000'; ctx.fillText(data[i][1].toFixed(1)+"%", cx, ty - 20); }
                    ctx.fillStyle = '#2E5090'; ctx.fillRect(startX, ty, bar_w, bh);
                    startX += bar_g;
                }
            })();
            </script>
            <?php
        }
        echo '</div>';
        if($he_max > 0) {
            $he_pct = ($he_ach / $he_max) * 100;
            echo "<p class='heading-average'><b>Average percentage for ".htmlspecialchars($h_e).": <span style='color:#162252;'>".number_format($he_pct, 2)."%</span></b></p>";
        }
        echo "</div>"; // Close heading-section
    } // End Numerical loop
    
    // 2. Open-Ended Heading + Comments
    foreach ($q_be as $h_e => $ques_e) {
        $has_text = false;
        foreach($ques_e as $qe) { if($qe['is_text_input']) { $has_text = true; break; } }
        
        if($has_text) {
            echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 30px;'>" . htmlspecialchars($h_e) . "</h4>";
            
            $ce_sql = "SELECT comment FROM $comm_table WHERE course_code='$eid' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($r_list)";
            $ce_res = $conn->query($ce_sql);
            if($ce_res && $ce_res->num_rows > 0) {
                while($cer = $ce_res->fetch_assoc()) {
                    $txt = trim($cer['comment']);
                    if(!$txt || in_array(strtolower($txt), ['-','--','na','none','nil','.','..'])) continue;
                    echo "<b>- ".htmlspecialchars($txt)."</b><br>";
                }
            } else {
                echo "<i>No comments recorded.</i><br>";
            }
        }
    }

    // 3. Overall Percentage
    $oe_pct = ($o_max_e > 0) ? ($o_ach_e / $o_max_e) * 100 : 0;
    echo "<p style='text-align:center;'><b style='font-size: 18px;'>Faculty Evaluation (Overall): </b><strong style='color:#162252; font-size: 20px;'>".number_format($oe_pct, 2)."%</strong></p>";
    
    // 4. Footer
    echo '<div style="text-align: center; margin-top: 10px;"><hr><footer>************ This is a System Generated Report ************</footer><hr></div>';
    echo "<div class='page-break'></div><hr><br>";
endwhile;
?>


<script>
window.onload = function() {
    // Small delay to ensure all canvas drawings are finished before print dialog
    setTimeout(function() {
        // Only trigger print if not in a frame (optional check)
        // window.print();
    }, 500);
}
</script>
</body>
</html>