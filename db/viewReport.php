<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
<hr>
<script type="text/javascript">
		$(document).ready(function(){
			document.body.scrollTop = 0;
			document.documentElement.scrollTop = 0;
		});
    
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

<p style="text-align: center; color: black; text-align: center; font-size: 24px;  "><strong>K.J. SOMAIYA SCHOOL OF ENGINEERING, MUMBAI</strong></p>
<p style="text-align: center; color: black; text-align: center; font-size: 18px; "><strong>(A Constituent College of Somaiya Vidyavihar University)</strong></p>
<hr>
<p style="text-align: center; color: black; text-align: center; font-size: 18px; "><strong>Department of <?php echo $dept_name; ?></strong></p>
<hr>
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



echo '<p style="text-align: center; font-size: 24px;"><strong>FACULTY FEEDBACK REPORT ('.$acad_year.')</strong></p>';
if($sem_type=="Odd"){
  if($status==0)
    echo '<p style="text-align: center; font-size: 20px;"><strong>Odd Semester (Mid Term) </strong></p>';
  else
    echo '<p style="text-align: center; font-size: 18px;"><strong>Odd Semester (End Term) </strong></p>';
}else{
  if($status==0)
    echo '<p style="text-align: center; font-size: 20px;"><strong>Even Semester (Mid Term) </strong></p>';
  else
    echo '<p style="text-align: center; font-size: 18px;"><strong>Even Semester (End Term) </strong></p>';
}
echo "<hr>";

$s = "SELECT fname,lname from faculty where f_id='$f_id'";
$r= $conn->query($s);
while($res=$r->fetch_assoc()){
  $fname=$res['fname'];
  $lname=$res['lname'];
  echo "<b style='font-size: 18px;'>Faculty Name: </b><strong style='color:#162252; font-size: 16px; '>".$fname." ".$lname."</strong><br><hr><br>";

}

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
  $a="SELECT roll_no from student where class='$class' and sem='$sem' and (batch='$section_or_batch' or section='$section_or_batch')  and acad_year='$acad_year'";
  $b=$conn->query($a);

  $roll_no=array();
  while($s=$b->fetch_assoc()){
    $roll_no[]=$s["roll_no"];
  }
  $roll_no_list = count($roll_no) > 0 ? implode(',', $roll_no) : "'0'";
  ?>

  <?php
  $s3 = "SELECT c_name from subject where course_code='$c_id' and class='$class' and acad_year='$acad_year' and sem='$sem'";
  $r3= $conn->query($s3);
  while($res3=$r3->fetch_assoc()){
    $cname=$res3['c_name'];
    echo "<b style='font-size: 18px;'>Course Name: </b><strong style='color:#162252;font-size: 16px;'>".$cname."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  }
  if($c_id[0]=='L')
    $c=$c_id[0];
  elseif ($c_id[0]=='T') 
    $c="TH";
  if($class=='FY_A' || $class=='FY_B')
    echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>"."FY"."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  else
    echo "<b style='font-size: 18px;'>Class: </b><strong style='color:#162252;font-size: 16px;'>".$class."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<b style='font-size: 18px;'>Sem: </b><strong style='color:#162252;font-size: 16px;'>".$sem."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<b style='font-size: 18px;'>Section/ Batch: </b><strong style='color:#162252;font-size: 16px;'>".$section_or_batch."</strong><br><hr>&nbsp;&nbsp;&nbsp;&nbsp;";
  $avg=0;
  ?>
  <table style="border-radius: 5px;"><tr>
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

  foreach ($questions_by_heading as $heading => $questions) {
      echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 20px;'>" . htmlspecialchars($heading) . "</h4>";
      
      $heading_achieved_score = 0;
      $heading_max_possible_score = 0;
      $q_counter = 1;
?>
<table style="border-radius: 5px; width: 100%;"><tr>
<?php
      foreach ($questions as $q) {
          $q_id = $q['q_id'];
          $question = $q['question'];
          $is_text_input = $q['is_text_input'];

          if ($is_text_input) continue;

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
          $q_pct = ($max_opt_val > 0 && $noOfStudents > 0) ? ($q_achieved / ($max_opt_val * $noOfStudents)) * 100 : 0;
?>
      <td style="width: 50%; text-align: center; border:0.5px solid #162252 ;padding: 5px; background-color: #f5f5f5 ">
        <?php echo "<br><b>".$question."</b><br>"; ?>
        <canvas id='<?php echo $c.$cname. $section_or_batch.$q_id ?>' width="50%" height="180" ></canvas> 
        <?php if($noOfStudents > 0): ?>
            <br><b style='color:#162252;'>Percentage: <?= number_format($q_pct, 2) ?>%</b>
        <?php endif; ?>
      </td>
      <script>
        var my_canvas=document.getElementById(<?php echo json_encode($c.$cname. $section_or_batch.$q_id)?>);
        if(my_canvas) {
          var gctx=my_canvas.getContext("2d");

          var noOfOptions=<?php echo json_encode($noOfOptions)?>;
          var options=<?php echo json_encode($options)?>;
          var optionName=<?php echo json_encode($optionName)?>;
          var noOfStudents=<?php echo json_encode($noOfStudents)?>;
          var q_id=<?php echo json_encode($q_id)?>;
          var q_counter=<?php echo json_encode($q_counter)?>;
          var data=[];
          for(var m=0;m<noOfOptions;m++){
            data[m]=[optionName[m], noOfStudents > 0 ? (options[m]/noOfStudents)*100 : 0];
          }
        
          if(q_counter%2==0){
            my_canvas.style.styleFloat="clear";
          }

          var bar_width=30;
          var y_gap=30;
          var bar_gap=100;
          var x= 15; 

          var y = my_canvas.height - y_gap;
          my_canvas.width = data.length * bar_gap + x + 22;

          gctx.moveTo(x-5,y);
          gctx.lineTo(my_canvas.width,y); 
          gctx.stroke();

          gctx.shadowColor = '#000000';
          gctx.shadowOffsetX=3;
          gctx.shadowOffsetY=3;
          gctx.shadowBlur = 3;

          for (var i=0;i<data.length;i++){
            gctx.shadowColor = '#ffffff'; 
            gctx.font = '15px serif'; 
            gctx.textAlign='left';
            gctx.textBaseline='top';
            gctx.fillStyle= '#162252';
            gctx.fillText(data[i][0], x,y+5); 

            gctx.beginPath();
            gctx.lineWidth=2;
            var y1 = y - data[i][1]; 
            var x1 = x;    
            gctx.font = '12px serif'; 
            gctx.fillStyle= '#000000';
            if (noOfStudents > 0) {
                gctx.fillText(data[i][1].toFixed(1)+"%", x1,y1-20); 
            }

            gctx.fillStyle= '#2E5090'; 
            gctx.shadowColor = '#000000'; 
            gctx.fillRect(x1,y1,bar_width,data[i][1]);

            x=x+bar_gap;
          }
        }
      </script>
<?php 
          if($q_counter%2==0) {
            echo "</tr><tr>";
          }
          $q_counter++;
      } // Question loop
?>
</tr></table>
<?php
      $h_pct = ($heading_max_possible_score > 0) ? ($heading_achieved_score / $heading_max_possible_score) * 100 : 0;
      echo "<p style='text-align:center;'><b>Average percentage for ".htmlspecialchars($heading).": <span style='color:#162252;'>".number_format($h_pct, 2)."%</span></b></p>";
  } // Heading loop
?>

<?php
    $o_pct = ($overall_max_possible_score > 0) ? ($overall_achieved_score / $overall_max_possible_score) * 100 : 0;
    echo "<br><p style='text-align:center;'><b style='font-size: 18px;'>Faculty Evaluation (Overall Percentage): </b><strong style='color:#162252; font-size: 20px;'>".number_format($o_pct, 2)."%</strong></p><br>";
?>

<?php
if($status==0){
 $check = "SELECT comment FROM comment_midsem where course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
 $res = $conn->query($check);
 if($res!== false && $res->num_rows>0)
 { 
 echo "<b>Suggestions/ Comments<b><br>";   
while($comment=$res->fetch_assoc()){
    if($comment["comment"]=="" or $comment["comment"] =="." or $comment["comment"] ==".." or $comment["comment"] =="_" or $comment["comment"] =="-" or $comment["comment"] ==" " or $comment["comment"]=="NA" or $comment["comment"]=="na" or $comment["comment"] == "none")
      continue;
    echo "<b>- ".$comment["comment"]."</b><br>";
    
  }
 }
}else{
   $check = "SELECT comment FROM comment_endsem where course_code='$c_id' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
 $res = $conn->query($check);
 if($res!== false && $res->num_rows>0)
 { 
 echo "<b>Suggestions/ Comments<b><br>";   
while($comment=$res->fetch_assoc()){
    if($comment["comment"]=="" or $comment["comment"] =="." or $comment["comment"] ==".." or $comment["comment"] =="_" or $comment["comment"] =="-" or $comment["comment"] ==" " or $comment["comment"]=="NA" or $comment["comment"]=="na" or $comment["comment"] == "none")
      continue;
    echo "<b>- ".$comment["comment"]."</b><br>";
    
  }
 }
}
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
  $a="SELECT roll_no from student where sem='$sem' and (elective_or_IDC_ID='$electiveID' or elective_or_IDC_BatchID='$electiveID' or elective_or_IDC_ID1='$electiveID' or elective_or_IDC_ID2='$electiveID' or elective_or_IDC_ID3='$electiveID' or elective_or_IDC_ID4='$electiveID' or elective_or_IDC_ID5='$electiveID' or elective_or_IDC_BatchID1='$electiveID'or elective_or_IDC_BatchID2='$electiveID' or elective_or_IDC_BatchID3='$electiveID' or elective_or_IDC_BatchID4='$electiveID' or elective_or_IDC_BatchID5='$electiveID' )  and acad_year='$acad_year'";
  $b=$conn->query($a);

  $roll_no=array();
  while($s=$b->fetch_assoc()){
    $roll_no[]=$s["roll_no"];
  }
  $roll_no_list = count($roll_no) > 0 ? implode(',', $roll_no) : "'0'";
  ?>

  <?php

  echo "<b style='font-size: 18px;'>Course Name: </b><strong style='color:#162252;font-size: 16px;'>".$electiveName."</strong>&nbsp;&nbsp;&nbsp;&nbsp;";
  
  if($electiveID[0]=='L')
    $c=$electiveID[0];
  elseif ($electiveID[0]=='T') 
    $c='TH';
  

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

 $avg=0;
 ?>
 <table style="border-radius: 5px;"><tr>
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

  foreach ($questions_by_heading as $heading => $questions) {
      echo "<h4 style='color: #337ab7; border-bottom: 2px solid #337ab7; padding-bottom: 5px; margin-top: 20px;'>" . htmlspecialchars($heading) . "</h4>";
      
      $heading_achieved_score = 0;
      $heading_max_possible_score = 0;
      $q_counter = 1;
?>
<table style="border-radius: 5px; width: 100%;"><tr>
<?php
      foreach ($questions as $q) {
          $q_id = $q['q_id'];
          $question = $q['question'];
          $is_text_input = $q['is_text_input'];

          if ($is_text_input) continue;

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
          $q_pct = ($max_opt_val > 0 && $noOfStudents > 0) ? ($q_achieved / ($max_opt_val * $noOfStudents)) * 100 : 0;
?>
      <td style="width: 50%; text-align: center; border:0.5px solid #162252 ;padding: 5px; background-color: #f5f5f5 ">
        <?php echo "<br><b>".$question."</b><br>"; ?>
        <canvas id='<?php echo $electiveName.$electiveID.$q_id ?>' width="50%" height="180" ></canvas> 
        <?php if($noOfStudents > 0): ?>
            <br><b style='color:#162252;'>Percentage: <?= number_format($q_pct, 2) ?>%</b>
        <?php endif; ?>
      </td>
      <script>
        var my_canvas=document.getElementById(<?php echo json_encode($electiveName.$electiveID.$q_id)?>);
        if(my_canvas) {
          var gctx=my_canvas.getContext("2d");

          var noOfOptions=<?php echo json_encode($noOfOptions)?>;
          var options=<?php echo json_encode($options)?>;
          var optionName=<?php echo json_encode($optionName)?>;
          var noOfStudents=<?php echo json_encode($noOfStudents)?>;
          var q_id=<?php echo json_encode($q_id)?>;
          var q_counter=<?php echo json_encode($q_counter)?>;
          var data=[];
          for(var m=0;m<noOfOptions;m++){
            data[m]=[optionName[m], noOfStudents > 0 ? (options[m]/noOfStudents)*100 : 0];
          }
        
          if(q_counter%2==0){
            my_canvas.style.styleFloat="clear";
          }

          var bar_width=30;
          var y_gap=30;
          var bar_gap=100;
          var x= 15; 

          var y = my_canvas.height - y_gap;
          my_canvas.width = data.length * bar_gap + x + 22;

          gctx.moveTo(x-5,y);
          gctx.lineTo(my_canvas.width,y); 
          gctx.stroke();

          gctx.shadowColor = '#000000';
          gctx.shadowOffsetX=3;
          gctx.shadowOffsetY=3;
          gctx.shadowBlur = 3;

          for (var i=0;i<data.length;i++){
            gctx.shadowColor = '#ffffff'; 
            gctx.font = '15px serif'; 
            gctx.textAlign='left';
            gctx.textBaseline='top';
            gctx.fillStyle= '#162252';
            gctx.fillText(data[i][0], x,y+5); 

            gctx.beginPath();
            gctx.lineWidth=2;
            var y1 = y - data[i][1]; 
            var x1 = x;    
            gctx.font = '12px serif'; 
            gctx.fillStyle= '#000000';
            if (noOfStudents > 0) {
                gctx.fillText(data[i][1].toFixed(1)+"%", x1,y1-20); 
            }

            gctx.fillStyle= '#2E5090'; 
            gctx.shadowColor = '#000000'; 
            gctx.fillRect(x1,y1,bar_width,data[i][1]);

            x=x+bar_gap;
          }
        }
      </script>
<?php 
          if($q_counter%2==0) {
            echo "</tr><tr>";
          }
          $q_counter++;
      } // Question loop
?>
</tr></table>
<?php
      $h_pct = ($heading_max_possible_score > 0) ? ($heading_achieved_score / $heading_max_possible_score) * 100 : 0;
      echo "<p style='text-align:center;'><b>Average percentage for ".htmlspecialchars($heading).": <span style='color:#162252;'>".number_format($h_pct, 2)."%</span></b></p>";
  } // Heading loop
?>

<?php
    $o_pct = ($overall_max_possible_score > 0) ? ($overall_achieved_score / $overall_max_possible_score) * 100 : 0;
    echo "<br><p style='text-align:center;'><b style='font-size: 18px;'>Faculty Evaluation (Overall Percentage): </b><strong style='color:#162252; font-size: 20px;'>".number_format($o_pct, 2)."%</strong></p><br>";
?>

<?php
if($status==0){
 $check = "SELECT comment FROM comment_midsem where course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
 $res = $conn->query($check);
 if($res!== false && $res->num_rows>0)
  {
    echo "<b>Suggestions/ Comments<b><br>"; 
    while($comment=$res->fetch_assoc()){
        if($comment["comment"]=="" or $comment["comment"] ==" " or $comment["comment"]=="NA" or $comment["comment"]=="na" or $comment["comment"] == "none")
          continue;
        echo "<b>- ".$comment["comment"]."</b><br>";
        
      }
  }

}else{
   $check = "SELECT comment FROM comment_endsem where course_code='$electiveID' and f_id='$f_id' and acad_year='$acad_year' and sem_type='$sem_type' and roll_no in(".$roll_no_list.")";
 $res = $conn->query($check);
 if($res!== false && $res->num_rows>0)
  {
      echo "<b>Suggestions/ Comments<b><br>";   
        
      while($comment=$res->fetch_assoc()){
          if($comment["comment"]=="" or $comment["comment"] ==" " or $comment["comment"]=="NA" or $comment["comment"]=="na" or $comment["comment"] == "none")
            continue;
          echo "<b>- ".$comment["comment"]."</b><br>";
          
        }
  }
}

echo "<hr><br>";
endwhile;
?>


