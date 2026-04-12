<?php 
session_start();
include ('../config/db_config.php');
if(isset($_SESSION['dept_id'])){
  $dept_id=$_SESSION['dept_id'];
}else{
  $dept_id=$_POST['dept_id'];
}

include ('../config/db_config.php');
$year=$_POST['year'];
$sem=$_POST['sem'];

$sql = "SELECT `status`,acad_year,sem_type from current_state where dept_id='$dept_id'";
$res = $conn->query($sql);                     
while($r=$res->fetch_assoc()){ 
  $status=$r['status'];
  $acad_year=$r['acad_year'];
  $sem_type=$r['sem_type'];
}
?>

<table id="5" class="table table-bordered">
  <thead>
    <tr>
       
      <!-- <th style="text-align: center;">Course Name</th> -->
      <th style="text-align: center;">Faculty ID</th>
      <th style="text-align: center;">Faculty Name</th>
      <th style="text-align: center;">Course Name</th>
      <th style="text-align: center;">Average</th>
      <!-- <th style="text-align: center;">Add More Faculty</th> -->

    </tr>
<?php

$sql1 = "SELECT DISTINCT f_id , course_code FROM courses_faculty where acad_year='$year' and sem='$sem' and dept_id='$dept_id' and f_id<>'0'";
$res1= $conn->query($sql1);
while($row1=$res1->fetch_assoc()):
  $f_id=$row1['f_id'];
  $course_code=$row1['course_code'];
  $s = "SELECT fname,lname from faculty where f_id='$f_id'";
  $r= $conn->query($s);
while($res=$r->fetch_assoc()){
  $fname=$res['fname'];
  $lname=$res['lname'];
}
$s3 = "SELECT c_name from subject where course_code='$course_code'";
  $r3= $conn->query($s3);
  while($res3=$r3->fetch_assoc()){
    $cname=$res3['c_name'];
}

$c = 'TH'; // default
$code_upper = strtoupper($course_code);
if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
    $c = 'LAB';
} elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false) {
    $c = 'TU';
} elseif ($code_upper[0] == 'TH' || strpos($code_upper, 'TH') !== false) {
    $c = 'TH';
}

$sem_parity = ($sem % 2 == 0) ? 2 : 1;
$overall_achieved = 0;
$overall_max = 0;
$sql = "SELECT q.id as q_id, q.question_text as question, q.is_text_input
        FROM question_set qs
        JOIN question_heading h ON qs.id = h.question_set_id
        JOIN feedback_question q ON h.id = q.heading_id
        WHERE qs.code = '$c' AND qs.acad_year = '$year' AND qs.semester = '$sem_parity'";
$result = $conn->query($sql);   
while($row=$result->fetch_assoc()):
    $q_id = $row['q_id'];
    if ($row['is_text_input'] || stripos($row['question'], 'comment') !== false) continue;

    $s2 = "SELECT option_number FROM feedback_option WHERE question_id='$q_id'";
    $res2 = $conn->query($s2);   
    $optionValues=array();
    $options=array();
    while($row2=$res2->fetch_assoc()){
        $optionValues[]=(int)$row2["option_number"];
        $options[]=0;
    }
    
    $max_opt_val = empty($optionValues) ? 0 : max($optionValues);
    $noOfStudents = 0;

    if($status==0){
        $m = "SELECT distinct(roll_no) FROM response_midsem where q_id='$q_id' and course_code='$course_code' and f_id='$f_id' and acad_year='$year' and sem_type='$sem_type'";
        $n = $conn->query($m); 
        $noOfStudents=$n->num_rows;

        $check = "SELECT response FROM response_midsem where q_id='$q_id' and course_code='$course_code' and f_id='$f_id' and acad_year='$year' and sem_type='$sem_type'";
        $res = $conn->query($check);   
        while($response=$res->fetch_assoc()){
            $resp_val = (int)$response["response"];
            $idx = array_search($resp_val, $optionValues);
            if ($idx !== false) $options[$idx]++;
        }
    }
    else{
        $m = "SELECT distinct(roll_no) FROM response_endsem where q_id='$q_id' and course_code='$course_code' and f_id='$f_id' and acad_year='$year' and sem_type='$sem_type'";
        $n = $conn->query($m); 
        $noOfStudents=$n->num_rows;

        $check = "SELECT response FROM response_endsem where q_id='$q_id' and course_code='$course_code' and f_id='$f_id' and acad_year='$year' and sem_type='$sem_type'";
        $res = $conn->query($check);   
        while($response=$res->fetch_assoc()){
            $resp_val = (int)$response["response"];
            $idx = array_search($resp_val, $optionValues);
            if ($idx !== false) $options[$idx]++;
        }
    }
    
    for($g=0; $g<count($options); $g++){
        $overall_achieved += $optionValues[$g] * $options[$g];
    }
    $overall_max += ($max_opt_val * $noOfStudents);
endwhile;

$avg_display = $overall_max > 0 ? number_format(($overall_achieved / $overall_max) * 100, 2) . "%" : "0%";
?>

<tr id="hdsj">
    <td id="f_id"><?= $f_id ?></td>
    <td id="fname"><?= $fname.' '.$lname ?></td>
    <td id="cname"><?= $cname ?></td>
    <td id="avg"><?= $avg_display ?></td>
</tr>
<?php 
     
endwhile;
 ?>
    </thead>

    <tbody>


    </tbody>
  </table>
  <button id="button_print"  class="btn btn-success" onclick="printDataSummary()">Print</button>
<script>
  function printDataSummary()
  {
   var divToPrint=document.getElementById("5");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.document.write("<head><style>@media print{ table{border-collapse: collapse;} table, td, th{ border:1px solid #000; padding: 10px 40px 10px 40px; } </style></head>")
   newWin.print();
   newWin.close();
 }

 $('#button_print').on('click',function(){
  printDataSummary();
});


</script>