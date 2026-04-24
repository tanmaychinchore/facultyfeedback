<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
<hr>
<script type="text/javascript">
		$(document).ready(function(){
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		});
    
	</script>
<style>
    body { font-family: 'Open Sans', 'Roboto', sans-serif; color: #333; line-height: 1.6; }
    .report-header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #162252; padding-bottom: 10px; }
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
    @media (max-width: 768px) {
        .report-card {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
    .question-text {
        font-weight: 600;
        text-align: left;
        margin-bottom: 15px;
        color: #162252;
        width: 100%;
        font-size: 14px;
        min-height: 40px;
    }
    .percentage-text {
        margin-top: 15px;
        color: #d9534f;
        font-weight: bold;
        text-align: center;
        width: 100%;
        font-size: 15px;
    }
    .heading-average {
        margin-top: 30px;
        margin-bottom: 30px;
        text-align: center;
        width: 100%;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    h4 {
        color: #162252;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-left: 5px solid #162252;
        padding-left: 10px;
        margin-top: 40px !important;
        page-break-after: avoid;
    }
    canvas {
        width: 100% !important;
        max-width: 400px;
        height: auto !important;
    }
    .course-info-block {
        background-color: #fcfcfc;
        border: 1px solid #162252;
        border-left: 10px solid #162252;
        border-radius: 0;
        padding: 15px 20px;
        margin-bottom: 25px;
        margin-top: 10px;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    }
    .report-container {
        margin-top: 10px;
    }
    .overall-stats {
        background-color: #fff;
        border: 2px solid #162252;
        border-radius: 0;
        padding: 25px;
        margin: 40px 0;
        text-align: center;
        position: relative;
    }
    .overall-stats::before {
        content: "SUMMARY STATISTICS";
        position: absolute;
        top: -12px;
        left: 20px;
        background: white;
        padding: 0 10px;
        font-weight: bold;
        color: #162252;
        font-size: 14px;
    }
    .overall-stats p {
        margin: 10px 0;
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
        }
        else {
            line = testLine;
        }
    }
    context.fillText(line, x, y);
}
</script>
<?php
session_start();
include ('../config/db_config.php');
if(isset($_SESSION['dept_id'])){
  $dept_id=$_SESSION['dept_id'];
}else{
  $dept_id=$_POST['dept_id'];
}

$sql = "SELECT dept_name from department where dept_id='$dept_id'";
$res = $conn->query($sql);                     
while($r=$res->fetch_assoc()){ 
  $dept_name = $r['dept_name'];
  if($dept_name == "COMPS")
    $dept_name = "Computer Engineering";
  if($dept_name == "IT")
    $dept_name = "Information Technology";
  if($dept_name == "ETRX")
    $dept_name = "Electronics Engineering";
  if($dept_name == "EXTC")
    $dept_name = "Electronics and Telecommunication Engineering";
  if($dept_name == "MECH")
    $dept_name = "Mechanical Engineering";
  if($dept_name == "S&H")
    $dept_name = "Science and Humanities";
  if($dept_name == "CSBS")
    $dept_name = "CSBS";
  if($dept_name == "VLSI")
    $dept_name = "VLSI";
  if($dept_name == "EXCP")
    $dept_name = "EXCP";
  if($dept_name == "RAI")
    $dept_name = "RAI";
}?>

<div class="report-header" style="text-align: center; margin-bottom: 30px; border-bottom: 3px double #162252; padding-bottom: 10px;">
    <h2 style="margin:0; color: #162252; font-weight: 800; text-align:center;">K.J. SOMAIYA SCHOOL OF ENGINEERING, MUMBAI</h2>
    <!-- <p style="margin:5px 0; font-style: italic; text-align:center;">(A Constituent College of Somaiya Vidyavihar University)</p> -->
    <h4 style="margin:15px 0; border:none; padding:0; text-align:center; color: #162252; border-left:none;">Department of <?= htmlspecialchars($dept_name) ?></h4>
</div>

<?php
$f_id; $cname; $c_id; $fname; $lname;                
$f_id=$_POST["f_id"];


$sql = "SELECT `status`,acad_year,sem_type from current_state where dept_id='$dept_id'";
$res = $conn->query($sql);                     
while($r=$res->fetch_assoc()){ 
  $status=(int)$r['status'];
  $acad_year=$r['acad_year'];
  $sem_type=$r['sem_type'];
}

$s = "SELECT fname,lname from faculty where f_id='$f_id'";
$r= $conn->query($s);
while($res=$r->fetch_assoc()){
  $fname=$res['fname'];
  $lname=$res['lname'];
}
?>

<div class="faculty-info" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; gap: 20px;">
        <table class="table table-bordered" style="width: 48%; margin-bottom: 0;">
            <tr>
                <th style="width: 40%; background: #f4f4f4; text-align:left; color: #333;">Faculty Name</th>
                <td><strong style="color: #162252; font-size: 14px;"><?= $fname." ".$lname ?></strong></td>
            </tr>
        </table>
        <table class="table table-bordered" style="width: 48%; margin-bottom: 0;">
            <tr>
                <th style="width: 40%; background: #f4f4f4; text-align:left; color: #333;">Academic Year</th>
                <td><strong style="color: #162252; font-size: 14px;"><?= $acad_year ?></strong></td>
            </tr>
        </table>
    </div>
</div>

<h3 style="text-align: center; text-decoration: underline; margin-bottom: 30px; font-weight:bold; color: #162252;">FACULTY FEEDBACK REPORT</h3>

<?php
if($sem_type=='Odd')
  $s2 = "SELECT course_code,class,sem,section_or_batch from courses_faculty where f_id='$f_id' and sem in (1,3,5,7) and acad_year='$acad_year' and dept_id='$dept_id'";
else
  $s2 = "SELECT course_code,class,sem,section_or_batch from courses_faculty where f_id='$f_id' and sem in (2,4,6,8) and acad_year='$acad_year' and dept_id='$dept_id'";
$r2= $conn->query($s2);

while($res2=$r2->fetch_assoc()):
  $c_id=$res2['course_code'];
  $class=$res2["class"];
  $sem=$res2["sem"];
  $section_or_batch=$res2["section_or_batch"];
  $a="SELECT roll_no from student where class='$class' and sem='$sem' and (batch='$section_or_batch' or section='$section_or_batch') and dept_id='$dept_id' and acad_year='$acad_year'";
  $b=$conn->query($a);

  $roll_no=array();
  while($s=$b->fetch_assoc()){
    $roll_no[]=$s["roll_no"];
  }
  $roll_no_list = count($roll_no) > 0 ? implode(',', $roll_no) : "'0'";
  $total_calculated_students = count($roll_no);
  ?>

  <?php
  $s3 = "SELECT c_name from subject where course_code='$c_id' and class='$class' and acad_year='$acad_year' and sem='$sem'";
  $r3= $conn->query($s3);
  $cname = '';
  while($res3=$r3->fetch_assoc()){
    $cname=$res3['c_name'];
  }
  $c = 'TH'; // default
  $code_upper = strtoupper($c_id);
  if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
      $c = 'LAB';
  } elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false) {
      $c = 'TU';
  } elseif ($code_upper[0] == 'T' || strpos($code_upper, 'TH') !== false) {
      $c = 'TH';
  }

  $resp_table = ($status == 0) ? 'response_midsem' : 'response_endsem';
  $resp_sql = "SELECT count(DISTINCT roll_no) as total_resp FROM $resp_table WHERE course_code='$c_id' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
  $resp_res = $conn->query($resp_sql);
  $resp_row = ($resp_res) ? $resp_res->fetch_assoc() : null;
  $total_responded = ($resp_row) ? $resp_row['total_resp'] : 0;

  $avg=0;
  $global_q_counter = 1;
?>
  <div class="course-info-block">
    <b>Course Name: </b><strong style='color:#162252;'><?= htmlspecialchars($cname) ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Class: </b><strong style='color:#162252;'><?= ($class=='FY_A' || $class=='FY_B') ? 'FY' : htmlspecialchars($class) ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Sem: </b><strong style='color:#162252;'><?= $sem ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;
    <b>Section/ Batch: </b><strong style='color:#162252;'><?= htmlspecialchars($section_or_batch) ?></strong>
  </div>
  <div class="report-container">
    <?php
  $sem_parity = ($sem % 2 == 0) ? 2 : 1;
  $sql = "SELECT q.id as q_id, q.question_text as question, h.heading, q.is_text_input
          FROM question_set qs
          JOIN question_heading h ON qs.id = h.question_set_id
          JOIN feedback_question q ON h.id = q.heading_id
          WHERE qs.code = '$c' AND qs.acad_year = '$acad_year' AND qs.semester = '$sem_parity'
          ORDER BY h.heading_order ASC, q.question_order ASC";
  $result = $conn->query($sql);   
  $questions_by_heading = [];
  while($row=$result->fetch_assoc()){
      $questions_by_heading[$row['heading']][] = $row;
  }

  $overall_achieved_score = 0;
  $overall_max_possible_score = 0;
  $overall_num_questions = 0;

  foreach ($questions_by_heading as $heading => $questions) {
      $has_numerical = false;
      foreach($questions as $q) { if(!$q['is_text_input']) { $has_numerical = true; break; } }
      if (!$has_numerical) continue;

      echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 20px;'>" . htmlspecialchars($heading) . "</h4>";
      
      $heading_achieved_score = 0;
      $heading_max_possible_score = 0;
      $heading_num_questions = 0;
?>
<div class="report-grid">

<?php
      foreach ($questions as $q) {
          $q_id = $q['q_id'];
          $question = $q['question'];
          $is_text_input = $q['is_text_input'];

          if ($is_text_input || stripos($question, 'comment') !== false) continue;

          $s2 = "SELECT option_number, option_text FROM feedback_option WHERE question_id='$q_id' ORDER BY option_number ASC";
          $res2 = $conn->query($s2);   
          $noOfOptions=$res2->num_rows;
          $options=array();
          $optionName=array();
          $optionValues=array();
          while($row2=$res2->fetch_assoc()){
            $options[]=0;
            $optionName[]=$row2["option_text"];
            $optionValues[]=(int)$row2["option_number"];
          }
          
          $max_opt_val = empty($optionValues) ? 0 : max($optionValues);
          $noOfStudents = 0;

          if($status==0){
            $m = "SELECT distinct(roll_no) FROM response_midsem where q_id='$q_id' and course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
            $n = $conn->query($m); 
            if($n!== false && $n->num_rows>0) {          
                $noOfStudents=$n->num_rows;
                $check = "SELECT response,roll_no FROM response_midsem where q_id='$q_id' and course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
                $res = $conn->query($check);   
                while($response=$res->fetch_assoc()){
                  $resp_val = (int)$response["response"];
                  $idx = array_search($resp_val, $optionValues);
                  if ($idx !== false) $options[$idx]++;
                }
            }
          }
          else{
           $m = "SELECT distinct(roll_no) FROM response_endsem where q_id='$q_id' and course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
           $n = $conn->query($m); 
           if($n!== false && $n->num_rows>0) {
             $noOfStudents=$n->num_rows;
             $check = "SELECT response,roll_no FROM response_endsem where q_id='$q_id' and course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
             $res = $conn->query($check);   
             while($response=$res->fetch_assoc()){
               $resp_val = (int)$response["response"];
               $idx = array_search($resp_val, $optionValues);
               if ($idx !== false) $options[$idx]++;
             }
           }
          }

          $q_achieved = 0;
          for($g=0; $g<count($options); $g++){
             $q_achieved += $optionValues[$g] * $options[$g];
          }
          $heading_achieved_score += $q_achieved;
          $heading_max_possible_score += ($max_opt_val * $noOfStudents);
          
          $overall_achieved_score += $q_achieved;
          $overall_max_possible_score += ($max_opt_val * $noOfStudents);

          $heading_num_questions++;
          $overall_num_questions++;

          $q_weighted_avg = ($noOfStudents > 0) ? ($q_achieved / $noOfStudents) : 0;
?>
      <div class="report-card">
        <div class="question-text"><?= $global_q_counter . ". " . htmlspecialchars($question) ?></div>
        <canvas id='<?php echo $c.$cname. $section_or_batch.$q_id ?>' width="400" height="220" ></canvas> 
        <div class="percentage-text">Weighted Average: <?= number_format($q_weighted_avg, 2) ?></div>
      </div>
      <script>
        (function() {
          var my_canvas=document.getElementById(<?php echo json_encode($c.$cname. $section_or_batch.$q_id)?>);
          if(my_canvas) {
            var gctx=my_canvas.getContext("2d");

            var noOfOptions=<?php echo json_encode($noOfOptions)?>;
            var options=<?php echo json_encode($options)?>;
            var optionName=<?php echo json_encode($optionName)?>;
            var optionValues=<?php echo json_encode($optionValues)?>;
            var noOfStudents=<?php echo json_encode($noOfStudents)?>;
            
            var data=[];
            for(var m=0;m<noOfOptions;m++){
              // Label: Number - Text
              var label = optionValues[m] + " - " + optionName[m];
              data[m]=[label, noOfStudents > 0 ? (options[m]/noOfStudents)*100 : 0];
            }
          
            var bar_width=30;
            var y_gap=60; // Increased for multi-line labels
            var bar_gap=80;
            var x= 20; 

            var y = my_canvas.height - y_gap;
            my_canvas.width = data.length * bar_gap + x + 20;

            gctx.moveTo(x-5,y);
            gctx.lineTo(my_canvas.width,y); 
            gctx.stroke();

            for (var i=0;i<data.length;i++){
              gctx.font = '12px Arial'; 
              gctx.textAlign='center';
              gctx.textBaseline='top';
              gctx.fillStyle= '#162252';
              
              var centerX = x + (bar_width / 2);
              
              // Wrap text to avoid overlap, centered under bar
              wrapText(gctx, data[i][0], centerX, y + 5, bar_gap - 10, 14);

              gctx.beginPath();
              var y1 = y - (data[i][1] * 1.2); // Scale a bit for visibility
              var x1 = x;    
              
              if (noOfStudents > 0) {
                  gctx.fillStyle= '#000000';
                  // Percentage text centered above bar
                  gctx.fillText(data[i][1].toFixed(1)+"%", centerX, y1-20); 
              }

              gctx.fillStyle= '#2E5090'; 
              gctx.fillRect(x1, y1, bar_width, (data[i][1] * 1.2));

              x=x+bar_gap;
            }
          }
        })();
      </script>
<?php 
          $global_q_counter++;
      } // Question loop
?>
</div>

<?php
      $h_weighted_avg = ($heading_num_questions > 0 && $total_responded > 0) ? ($heading_achieved_score / ($heading_num_questions * $total_responded)) : 0;
      if ($heading_num_questions > 0) {
          echo "<p class='heading-average'><b>Weighted Average for ".htmlspecialchars($heading).": <span style='color:#162252;'>".number_format($h_weighted_avg, 2)."</span></b></p>";
      }
  } // End Numerical headings loop
?>

<div class="comments-section">
  <?php
  // 2. Open-Ended Heading + Comments
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
  ?>
</div>
<?php
    $o_weighted_avg = ($overall_num_questions > 0 && $total_responded > 0) ? ($overall_achieved_score / ($overall_num_questions * $total_responded)) : 0;
    $o_pct = ($overall_max_possible_score > 0) ? ($overall_achieved_score / $overall_max_possible_score) * 100 : 0;
?>
    <div class="overall-stats">
      <p><b style='font-size: 18px;'>Faculty Evaluation (Overall Weighted Average): </b><strong style='color:#162252; font-size: 20px;'><?= number_format($o_weighted_avg, 2) ?> out of 5</strong></p>
      <p><b style='font-size: 18px;'>Faculty Evaluation (Overall Weighted Percentage): </b><strong style='color:#162252; font-size: 20px;'><?= number_format($o_pct, 2) ?>%</strong></p>
      <p style='margin-top: 15px;'><b style='font-size: 16px;'>Number of Students Submitted Feedback = </b><strong style='color:#162252; font-size: 16px;'><?= $total_responded ?> out of <?= $total_calculated_students ?></strong></p>
    </div>
<?php
    echo '<div style="text-align: center; margin-top: 10px;"><hr><footer>************ This is a System Generated Report ************</footer><hr></div>';

    echo "</div>"; // end report-container
    echo "<hr><br>";
endwhile;
?>


<!--Electives Report-->
<?php
if($sem_type=='Odd')
  $s2 = "SELECT electiveID,electiveName,sem from electives where f_id='$f_id' and sem%2=1 and acad_year='$acad_year' and dept_id='$dept_id'";
else
  $s2 = "SELECT electiveID,electiveName,sem from electives where f_id='$f_id' and sem%2=0 and acad_year='$acad_year' and dept_id='$dept_id'";
$r2= $conn->query($s2);
while($res2=$r2->fetch_assoc()):
  $electiveID=$res2['electiveID'];

  $sem=$res2["sem"];
  $electiveName=$res2["electiveName"];
  $a="SELECT roll_no from student where sem='$sem' and dept_id='$dept_id' and (elective_or_IDC_ID='$electiveID' or elective_or_IDC_BatchID='$electiveID' or elective_or_IDC_ID1='$electiveID' or elective_or_IDC_ID2='$electiveID' or elective_or_IDC_ID3='$electiveID' or elective_or_IDC_ID4='$electiveID' or elective_or_IDC_ID5='$electiveID' or elective_or_IDC_BatchID1='$electiveID'or elective_or_IDC_BatchID2='$electiveID' or elective_or_IDC_BatchID3='$electiveID' or elective_or_IDC_BatchID4='$electiveID' or elective_or_IDC_BatchID5='$electiveID' )  and acad_year='$acad_year'";
  $b=$conn->query($a);

  $roll_no=array();
  while($s=$b->fetch_assoc()){
    $roll_no[]=$s["roll_no"];
  }
  $roll_no_list = count($roll_no) > 0 ? implode(',', $roll_no) : "'0'";
  $total_calculated_students_el = count($roll_no);
  ?>

  <?php

  echo "<b style='font-size: 18px;'>Course Name: </b><strong style='color:#162252;font-size: 16px;'>".$electiveName."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  
  $c = 'TH'; // default
  $code_upper = strtoupper($electiveID);
  if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
      $c = 'LAB';
  } elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false) {
      $c = 'TU';
  } elseif ($code_upper[0] == 'T' || strpos($code_upper, 'TH') !== false) {
      $c = 'TH';
  }
  

    if($sem==1 or $sem==2)
    {
      if($dept_id==6)
       echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>FY</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
       else
       echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>MTech</strong>&nbsp;&nbsp;&nbsp;&nbsp;";

    }
    else if($sem==3 or $sem==4)
    echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>SY</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
    else if($sem==5 or $sem==6)
   echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>TY</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
else if($sem==7 or $sem==8)
   echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>LY</strong>&nbsp;&nbsp;&nbsp;&nbsp;";

  echo "<b style='font-size: 18px;'>Sem: </b><strong style='color:#162252;font-size: 16px;'>".$sem."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";

  $resp_table = ($status == 0) ? 'response_midsem' : 'response_endsem';
  $resp_sql = "SELECT count(DISTINCT roll_no) as total_resp FROM $resp_table WHERE course_code='$electiveID' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
  $resp_res = $conn->query($resp_sql);
  $resp_row = ($resp_res) ? $resp_res->fetch_assoc() : null;
  $total_responded = ($resp_row) ? $resp_row['total_resp'] : 0;

  $avg=0;
  $global_q_counter = 1;
?>
  <div class="report-container">
  <?php
  $sem_parity = ($sem % 2 == 0) ? 2 : 1;
  $sql = "SELECT q.id as q_id, q.question_text as question, h.heading, q.is_text_input
          FROM question_set qs
          JOIN question_heading h ON qs.id = h.question_set_id
          JOIN feedback_question q ON h.id = q.heading_id
          WHERE qs.code = '$c' AND qs.acad_year = '$acad_year' AND qs.semester = '$sem_parity'
          ORDER BY h.heading_order ASC, q.question_order ASC";
  $result = $conn->query($sql);   
  $questions_by_heading = [];
  while($row=$result->fetch_assoc()){
      $questions_by_heading[$row['heading']][] = $row;
  }

  $overall_achieved_score = 0;
  $overall_max_possible_score = 0;
  $overall_num_questions = 0;

  foreach ($questions_by_heading as $heading => $questions) {
      $has_num_e = false;
      foreach($questions as $qe) { if(!$qe['is_text_input']) { $has_num_e = true; break; } }
      if (!$has_num_e) continue;

      echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 20px;'>" . htmlspecialchars($heading) . "</h4>";
      
      $heading_achieved_score = 0;
      $heading_max_possible_score = 0;
      $heading_num_questions = 0;
?>
  <div class="report-grid">
<?php
      foreach ($questions as $q) {
          $q_id = $q['q_id'];
          $question = $q['question'];
          $is_text_input = $q['is_text_input'];

          if ($is_text_input || stripos($question, 'comment') !== false) continue;

          $s2 = "SELECT option_number, option_text FROM feedback_option WHERE question_id='$q_id' ORDER BY option_number ASC";
          $res2 = $conn->query($s2);   
          $noOfOptions=$res2->num_rows;
          $options=array();
          $optionName=array();
          $optionValues=array();
          while($row2=$res2->fetch_assoc()){
            $options[]=0;
            $optionName[]=$row2["option_text"];
            $optionValues[]=(int)$row2["option_number"];
          }
          
          $max_opt_val = empty($optionValues) ? 0 : max($optionValues);
          $noOfStudents = 0;

          if($status==0){
            $m = "SELECT distinct(roll_no) FROM response_midsem where q_id='$q_id' and course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
            $n = $conn->query($m); 
            if($n!== false && $n->num_rows>0) {          
                $noOfStudents=$n->num_rows;
                $check = "SELECT response,roll_no FROM response_midsem where q_id='$q_id' and course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
                $res = $conn->query($check);   
                while($response=$res->fetch_assoc()){
                  $resp_val = (int)$response["response"];
                  $idx = array_search($resp_val, $optionValues);
                  if ($idx !== false) $options[$idx]++;
                }
            }
          }
          else{
           $m = "SELECT distinct(roll_no) FROM response_endsem where q_id='$q_id' and course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
           $n = $conn->query($m); 
           if($n!== false && $n->num_rows>0) {
             $noOfStudents=$n->num_rows;
             $check = "SELECT response,roll_no FROM response_endsem where q_id='$q_id' and course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
             $res = $conn->query($check);   
             while($response=$res->fetch_assoc()){
               $resp_val = (int)$response["response"];
               $idx = array_search($resp_val, $optionValues);
               if ($idx !== false) $options[$idx]++;
             }
           }
          }

          $q_achieved = 0;
          for($g=0; $g<count($options); $g++){
             $q_achieved += $optionValues[$g] * $options[$g];
          }
          $heading_achieved_score += $q_achieved;
          $heading_max_possible_score += ($max_opt_val * $noOfStudents);
          
          $overall_achieved_score += $q_achieved;
          $overall_max_possible_score += ($max_opt_val * $noOfStudents);

          $heading_num_questions++;
          $overall_num_questions++;

          $q_weighted_avg = ($noOfStudents > 0) ? ($q_achieved / $noOfStudents) : 0;
?>
      <div class="report-card">
        <div class="question-text"><?= $global_q_counter . ". " . htmlspecialchars($question) ?></div>
        <canvas id='<?php echo $electiveName.$electiveID.$q_id ?>' width="400" height="220" ></canvas> 
        <div class="percentage-text">Weighted Average: <?= number_format($q_weighted_avg, 2) ?></div>
      </div>
      <script>
        (function() {
          var my_canvas=document.getElementById(<?php echo json_encode($electiveName.$electiveID.$q_id)?>);
          if(my_canvas) {
            var gctx=my_canvas.getContext("2d");

            var noOfOptions=<?php echo json_encode($noOfOptions)?>;
            var options=<?php echo json_encode($options)?>;
            var optionName=<?php echo json_encode($optionName)?>;
            var optionValues=<?php echo json_encode($optionValues)?>;
            var noOfStudents=<?php echo json_encode($noOfStudents)?>;
            
            var data=[];
            for(var m=0;m<noOfOptions;m++){
              var label = optionValues[m] + " - " + optionName[m];
              data[m]=[label, noOfStudents > 0 ? (options[m]/noOfStudents)*100 : 0];
            }
          
            var bar_width=30;
            var y_gap=60;
            var bar_gap=80;
            var x= 20; 

            var y = my_canvas.height - y_gap;
            my_canvas.width = data.length * bar_gap + x + 20;

            gctx.moveTo(x-5,y);
            gctx.lineTo(my_canvas.width,y); 
            gctx.stroke();

            for (var i=0;i<data.length;i++){
              gctx.font = '12px Arial'; 
              gctx.textAlign='center';
              gctx.textBaseline='top';
              gctx.fillStyle= '#162252';

              var centerX = x + (bar_width / 2);

              wrapText(gctx, data[i][0], centerX, y + 5, bar_gap - 10, 14);

              gctx.beginPath();
              var y1 = y - (data[i][1] * 1.2);
              var x1 = x;    
              if (noOfStudents > 0) {
                  gctx.fillStyle= '#000000';
                  gctx.fillText(data[i][1].toFixed(1)+"%", centerX, y1-20); 
              }
              gctx.fillStyle= '#2E5090'; 
              gctx.fillRect(x1, y1, bar_width, (data[i][1] * 1.2));
              x=x+bar_gap;
            }
          }
        })();
      </script>
<?php 
          $global_q_counter++;
      } // Question loop
?>
</div>

<?php
      $h_weighted_avg = ($heading_num_questions > 0 && $total_responded > 0) ? ($heading_achieved_score / ($heading_num_questions * $total_responded)) : 0;
      if ($heading_num_questions > 0) {
          echo "<p class='heading-average'><b>Weighted Average for ".htmlspecialchars($heading).": <span style='color:#162252;'>".number_format($h_weighted_avg, 2)."</span></b></p>";
      }
  } // End Numerical headings loop
?>

<div class="comments-section">
  <?php
  // 2. Open-Ended Heading + Comments
  foreach ($questions_by_heading as $h_e => $questions_e) {
      $has_text = false;
      foreach($questions_e as $qe) { if($qe['is_text_input']) { $has_text = true; break; } }
      
      if($has_text) {
          echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 30px;'>" . htmlspecialchars($h_e) . "</h4>";
          
          $ce_sql = "SELECT comment FROM $comm_table WHERE course_code='$electiveID' AND f_id='$f_id' AND acad_year='$acad_year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
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
  ?>
</div>
<?php
    $o_weighted_avg = ($overall_num_questions > 0 && $total_responded > 0) ? ($overall_achieved_score / ($overall_num_questions * $total_responded)) : 0;
    $o_pct = ($overall_max_possible_score > 0) ? ($overall_achieved_score / $overall_max_possible_score) * 100 : 0;
?>
    <div class="overall-stats">
      <p><b style='font-size: 18px;'>Faculty Evaluation (Overall Weighted Average): </b><strong style='color:#162252; font-size: 20px;'><?= number_format($o_weighted_avg, 2) ?> out of 5</strong></p>
      <p><b style='font-size: 18px;'>Faculty Evaluation (Overall Weighted Percentage): </b><strong style='color:#162252; font-size: 20px;'><?= number_format($o_pct, 2) ?>%</strong></p>
      <p style='margin-top: 15px;'><b style='font-size: 16px;'>Number of Students Submitted Feedback = </b><strong style='color:#162252; font-size: 16px;'><?= $total_responded ?> out of <?= $total_calculated_students_el ?></strong></p>
    </div>
<?php
    echo '<div style="text-align: center; margin-top: 10px;"><hr><footer>System Generated Report Segment</footer><hr></div>';

    echo "</div>"; // end report-container
    echo "<hr><br>";
endwhile;
?>



<div style="text-align: center; margin-top: 60px; color: #777; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px;">
    <footer>This is a System Generated Report - Generated on <?= date('d-M-Y H:i') ?></footer>
</div>


