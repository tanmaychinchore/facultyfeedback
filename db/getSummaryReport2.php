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

$cs;$el;$tot;
?>

<!-- Print Table -->
<div id="7_">
<div style="margin: 20px;">
<button id="button_print"  class="btn btn-success" onclick="printDataSummary()" align="center">Print</button>
<script>
  function printDataSummary()
  {
   var divToPrint=document.getElementById("7");
   if(divToPrint.style.display!="none"){
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.document.write("<head><style>@media print{ table{border-collapse: collapse;} table, .ca, th{ border:1px solid #000; text-align:center;} .la{ border:1px solid #000; text-align:left;}</style></head>")
   newWin.print();
   newWin.close();
   }else{
   	alert("Table is empty");
   }
 }

 $('#button_print').on('click',function(){
  printDataSummary();
});
</script>

<!-- Download as Exccel -->
<button id="sumrep_export_excel"  class="btn btn-success" align="center">Download as excel file</button>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="../scripts/xlsx.full.min.js"></script>
<script src="../scripts/FileSaver.min.js"></script>
<script type="text/javascript">

var wb = XLSX.utils.table_to_book(document.getElementById("7"),{sheet:"Summary Report"});
var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
function s2ab(s) { 
                var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
                var view = new Uint8Array(buf);  //create uint8array as viewer
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
                return buf;    
}
$("#sumrep_export_excel").click(function(){
       saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), '<?php echo $year."_"."$sem"."_Sem_SummaryReport"; ?>.xlsx');
});      
//       $( "[id$=sumrep_export_excel]" ).click(function(e) {   
//   window.open('data:application/vnd.ms-excel,' + $('div[id$=summary_report_table]').html());
//   e.preventDefault();
// });
</script>
</div>
<div id="summary_report_table">
<table id="7" class="table table-bordered">
  <thead>
      <tr>
       
      <!-- <th style="text-align: center;">Course Name</th> -->
      <th style="text-align: center;">Faculty ID</th>
      <th style="text-align: center;">Faculty Name</th>
      <th style="text-align: center;">Course Type</th>
      <th style="text-align: center;">Course Name</th>
      <th style="text-align: center;">Class</th>
      <?php if($sem=="Both"){ ?>
      <th style="text-align: center;">Sem</th>  
      <?php } ?>
      <th style="text-align: center;">Div/Batch</th>
      <th style="text-align: center;">Mid Sem</th>
      <th style="text-align: center;">End Sem</th>
      <th style="text-align: center;">Average</th>
      <!-- <th style="text-align: center;">Add More Faculty</th> -->

    </tr>

    
<?php

function getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $sem, $year, $dept_id, $sem_type_flag, $is_elective = false) {
    $sem_type = ($sem % 2 == 1) ? 'Odd' : 'Even';
    $resp_table = ($sem_type_flag == 'mid') ? 'response_midsem' : 'response_endsem';

    $c = 'TH';
    $code_upper = strtoupper($course_code);
    if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
        $c = 'LAB';
    } elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false || ($code_upper[0] == 'T' && strlen($code_upper) > 1 && $code_upper[1] != 'H')) {
        $c = 'TU';
    } else {
        $c = 'TH';
    }

    if ($is_elective) {
        $roll_no_list_q = "SELECT roll_no FROM student WHERE sem='$sem' AND dept_id='$dept_id' AND (elective_or_IDC_ID='$course_code' OR elective_or_IDC_BatchID='$course_code' OR elective_or_IDC_ID1='$course_code' OR elective_or_IDC_ID2='$course_code' OR elective_or_IDC_ID3='$course_code' OR elective_or_IDC_ID4='$course_code' OR elective_or_IDC_ID5='$course_code' OR elective_or_IDC_BatchID1='$course_code' OR elective_or_IDC_BatchID2='$course_code' OR elective_or_IDC_BatchID3='$course_code' OR elective_or_IDC_BatchID4='$course_code' OR elective_or_IDC_BatchID5='$course_code') AND acad_year='$year'";
    } else {
        $roll_no_list_q = "SELECT roll_no FROM student WHERE class='$class' AND sem='$sem' AND (batch='$section_or_batch' OR section='$section_or_batch') AND dept_id='$dept_id' AND acad_year='$year'";
    }

    $b = $conn->query($roll_no_list_q);
    $roll_no = [];
    if($b) {
        while($s_row = $b->fetch_assoc()) { $roll_no[] = $s_row['roll_no']; }
    }
    if(count($roll_no) == 0) return -1;
    $roll_no_list = "'" . implode("','", $roll_no) . "'";

    $resp_sql = "SELECT count(DISTINCT roll_no) as total_resp FROM $resp_table WHERE course_code='$course_code' AND f_id='$f_id' AND acad_year='$year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
    $resp_res = $conn->query($resp_sql);
    $total_responded = ($resp_res && $row = $resp_res->fetch_assoc()) ? (int)$row['total_resp'] : 0;
    if($total_responded == 0) return -1;

    $sem_parity = ($sem % 2 == 0) ? 2 : 1;
    $sql_q = "SELECT q.id as q_id, q.is_text_input FROM question_set qs JOIN question_heading h ON qs.id = h.question_set_id JOIN feedback_question q ON h.id = q.heading_id WHERE qs.code = '$c' AND qs.acad_year = '$year' AND qs.semester = '$sem_parity'";
    $res_q = $conn->query($sql_q);
    
    $overall_achieved = 0;
    $overall_num_questions = 0;

    if($res_q) {
        while($q = $res_q->fetch_assoc()) {
            if($q['is_text_input']) continue;
            $q_id = $q['q_id'];

            $s_opt = "SELECT option_number FROM feedback_option WHERE question_id='$q_id'";
            $res_opt = $conn->query($s_opt);
            $vals = [];
            while($ro = $res_opt->fetch_assoc()) { $vals[] = (int)$ro['option_number']; }
            if(empty($vals)) continue;

            $csql = "SELECT response FROM $resp_table WHERE q_id='$q_id' AND course_code='$course_code' AND f_id='$f_id' AND acad_year='$year' AND sem_type='$sem_type' AND roll_no IN ($roll_no_list)";
            $rres = $conn->query($csql);
            if($rres) {
                while($rr = $rres->fetch_assoc()) {
                    $val = (int)$rr['response'];
                    if(in_array($val, $vals)) {
                        $overall_achieved += $val;
                    }
                }
            }
            $overall_num_questions++;
        }
    }

    if($overall_num_questions > 0 && $total_responded > 0) {
        return $overall_achieved / ($overall_num_questions * $total_responded);
    }
    return -1;
}

// fetching all faculty name
//$sql1 = "SELECT f_id, fname, lname FROM faculty WHERE (dept_id='$dept_id' OR dept_id='6') and f_id<>'0'";
$sql1 = "SELECT f_id, fname, lname FROM faculty WHERE f_id<>'0'";
$res1= $conn->query($sql1);
while($row1=$res1->fetch_assoc()){
	$f_id=$row1['f_id'];
	$fname=$row1['fname'];
	$lname=$row1['lname'];

// fetching course details that faculty teaches
	if($sem=="Odd"){
   $sql2 = "SELECT course_code, class, section_or_batch, sem FROM courses_faculty WHERE f_id='$f_id' and sem%2='1' and acad_year='$year' AND dept_id='$dept_id'";
}elseif($sem=="Even"){
   $sql2 = "SELECT course_code, class, section_or_batch, sem FROM courses_faculty WHERE f_id='$f_id' and sem%2='0' and acad_year='$year' AND dept_id='$dept_id'";
}elseif($sem=="Both"){
	$sql2 = "SELECT course_code, class, section_or_batch, sem FROM courses_faculty WHERE f_id='$f_id' and acad_year='$year' AND dept_id='$dept_id' and (sem%2='0' OR sem%2='1')";
}
	
	$res2= $conn->query($sql2);


// fetching elective details that faculty teaches
if($sem=="Odd"){
   $sql6 = "SELECT electiveID, electiveName, sem FROM electives WHERE f_id='$f_id' and sem%2='1' and acad_year='$year' and dept_id='$dept_id'";
}elseif($sem=="Even"){
   $sql6 = "SELECT electiveID, electiveName, sem FROM electives WHERE f_id='$f_id' and sem%2='0' and acad_year='$year' and dept_id='$dept_id'";
}elseif($sem=="Both"){
	$sql6 = "SELECT electiveID, electiveName, sem FROM electives WHERE f_id='$f_id' and acad_year='$year' and dept_id='$dept_id' and (sem%2='0' OR sem%2='1')";
}
$res2= $conn->query($sql2);
$res6= $conn->query($sql6);


	$cs = $res2->num_rows;
	$el = $res6->num_rows;
	$tot = $cs+$el; //total number of courses + electives that faculty teaches
	if($tot>0){
	?>
  <!-- faculty id and name rowspan -->
		<tr id="hdsj">
			<td class="ca" id="f_id"  rowspan="<?php echo $tot; ?>"><?= $f_id ?></td>
		  <td class="la" id="fname" rowspan="<?php echo $tot; ?>"><?= $fname.' '.$lname ?></td>
	<?php
	}

  // running the query and fetching one by one all courses details
	while($row2=$res2->fetch_assoc()){
		$course_code = $row2['course_code'];
		$class = $row2['class'];
		$section_or_batch = $row2['section_or_batch'];
    $s = $row2['sem'];

		$s3 = "SELECT c_name from subject where course_code='$course_code'";
		$r3= $conn->query($s3);
	while($res3=$r3->fetch_assoc()){
	  $cname=$res3['c_name'];
	}

	$c = 'TH'; // default
    $ct = 'Theory';
    $code_upper = strtoupper($course_code);
    if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
        $c = 'LAB';
        $ct = 'Lab';
    } elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false || ($code_upper[0] == 'T' && strlen($code_upper) > 1 && $code_upper[1] != 'H')) {
        $c = 'TU';
        $ct = 'Tutorial';
    } else {
        $c = 'TH';
        $ct = 'Theory';
    } 

  // calculating score
    $avg_mid = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $s, $year, $dept_id, 'mid', false);
    $avg_end = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $s, $year, $dept_id, 'end', false);

  if($cs>0){
    if ($dept_id==6)
    $class='FY';
?>
  <!-- printing details -->
    <td class="la" id="ctype"><?php echo $ct; ?></td>
    <td class="la" id="cname"><?= $cname ?></td>
    <td class="la" id="class"><?= $class ?></td>
    <?php if($sem=="Both"){ ?>
    <td class="la" id="semType"><?php if($s%2==1){echo "Odd";}else{echo "Even";} ?></td> 
    <?php } ?>
    <td class="la" id="div_batch"><?= $section_or_batch ?></td>
    <td class="ca" id="avgmid"><?php if($avg_mid >= 0){echo number_format((float)($avg_mid), 2,'.','');}else{ echo '-';} ?></td>
    <td class="ca" id="avgend"><?php if($avg_end >= 0){echo number_format((float)($avg_end), 2,'.','');}else{ echo '-';} ?></td>
    <td class="ca" id="avg"><?php 
        if($avg_mid >= 0 && $avg_end >= 0) { 
            echo number_format((float)(($avg_end+$avg_mid)/2), 2,'.',''); 
        } elseif($avg_mid >= 0) { 
            echo number_format((float)($avg_mid), 2,'.',''); 
        } elseif($avg_end >= 0) { 
            echo number_format((float)($avg_end), 2,'.',''); 
        } else { 
            echo "-"; 
        } 
    ?></td>
    </tr>

<?php
		}
	}

	
// THE ABOVE PROCEDURE FOR ELECTIVES

while($row6=$res6->fetch_assoc()){
	$cname = $row6['electiveName'];
	$course_code = $row6['electiveID'];
	$s = $row6['sem'];
	$c = 'TH';
    $ct = 'Theory';
    $section_or_batch = $course_code; // Print elective batchid here
    $code_upper = strtoupper($course_code);
    if($code_upper[0] == 'L' || strpos($code_upper, 'LAB') !== false) {
        $c = 'LAB';
        $ct = 'Lab';
    } elseif (strpos($code_upper, 'TU') !== false || strpos($code_upper, 'TUT') !== false || ($code_upper[0] == 'T' && strlen($code_upper) > 1 && $code_upper[1] != 'H')) {
        $c = 'TU';
        $ct = 'Tutorial';
    } else {
        $c = 'TH';
        $ct = 'Theory';
    }

  //finding out class using semester
  if($dept_id==6){
    $class='FY';
  }else{
    if($s==1 || $s==2){
      $class='MTech';
    }else if($s==3 || $s==4){
      $class='SY';
    }else if($s==5 || $s==6){
      $class='TY';
    }else{
      $class='LY';
    }
  }
  
    $avg_mid = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $s, $year, $dept_id, 'mid', true);
    $avg_end = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $s, $year, $dept_id, 'end', true);

  if($el>0){
?>
    <!-- printing details -->
    <td class="la" id="ctype"><?php echo $ct; ?></td>
    <td class="la" id="cname"><?= $cname." "."(Ele/IDC/Au)" ?></td>
    <td class="la" id="class"><?= $class ?></td>
    <?php if($sem=="Both"){ ?>
    <td class="la" id="semType"><?php if($s%2==1){echo "Odd";}else{echo "Even";} ?></td> 
    <?php } ?>
    <td class="la" id="div_batch"><?= $section_or_batch ?></td>
    <td class="ca" id="avgmid"><?php if($avg_mid >= 0){echo number_format((float)($avg_mid), 2,'.','');}else{ echo '-';} ?></td>
    <td class="ca" id="avgend"><?php if($avg_end >= 0){echo number_format((float)($avg_end), 2,'.','');}else{ echo '-';} ?></td>
    <td class="ca" id="avg"><?php 
        if($avg_mid >= 0 && $avg_end >= 0) { 
            echo number_format((float)(($avg_end+$avg_mid)/2), 2,'.',''); 
        } elseif($avg_mid >= 0) { 
            echo number_format((float)($avg_mid), 2,'.',''); 
        } elseif($avg_end >= 0) { 
            echo number_format((float)($avg_end), 2,'.',''); 
        } else { 
            echo "-"; 
        } 
    ?></td>
    </tr>
<?php
	}
}
}
?>

</thead>
<tbody>
</tbody>
</table>
</div>
</div>