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
}?>
<div style="margin: 20px;">
<button id="button_print"  class="btn btn-success" onclick="printDataSummary()" align="center">Print</button>
<script>
  function printDataSummary()
  {
   var divToPrint=document.getElementById("overall_course_feedback_table");
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
<button id="overall_course_export_excel"  class="btn btn-success" align="center">Download as excel file</button>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="../scripts/xlsx.full.min.js"></script>
<script src="../scripts/FileSaver.min.js"></script>
<script type="text/javascript">

var wb = XLSX.utils.table_to_book(document.getElementById("overall_course_feedback_table"),{sheet:"Summary Report"});
var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
function s2ab(s) { 
                var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
                var view = new Uint8Array(buf);  //create uint8array as viewer
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
                return buf;    
}
$("#overall_course_export_excel").click(function(){
       saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), '<?php echo $year."_"."$sem"."_Overall_CourseReport"; ?>.xlsx');
});      
//       $( "[id$=sumrep_export_excel]" ).click(function(e) {   
//   window.open('data:application/vnd.ms-excel,' + $('div[id$=summary_report_table]').html());
//   e.preventDefault();
// });
</script>
</div>
<table id="overall_course_feedback_table" class="table table-bordered">
  <thead>
    <tr>  
      <th style="text-align: center;">Course Code</th>
      <th style="text-align: center;">Course Name</th>
      <th style="text-align: center;">Course Type</th>
      <th style="text-align: center;">Mid sem</th>
      <th style="text-align: center;">End sem</th>
      <th style="text-align: center;">Overall Average</th>
    </tr>

    <?php 
    $sql1 = "SELECT DISTINCT c_name , course_code FROM subject where acad_year='$year' and sem='$sem' and dept_id='$dept_id'";
    $res1= $conn->query($sql1);
while($row1=$res1->fetch_assoc()){
	$c_name=$row1['c_name'];
  $course_code=$row1['course_code'];

  	$c = 'TH';
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

    $s_batches = "SELECT DISTINCT section_or_batch, class FROM courses_faculty WHERE course_code='$course_code' AND acad_year='$year' AND sem='$sem' AND dept_id='$dept_id'";
    $res_batches = $conn->query($s_batches);
    $batch_mid_avgs = [];
    $batch_end_avgs = [];

    if($res_batches) {
        while($batch = $res_batches->fetch_assoc()) {
            $section_or_batch = $batch['section_or_batch'];
            $class = $batch['class'];

            $s_fac = "SELECT f_id FROM courses_faculty WHERE course_code='$course_code' AND section_or_batch='$section_or_batch' AND class='$class' AND acad_year='$year' AND sem='$sem' AND dept_id='$dept_id'";
            $res_fac = $conn->query($s_fac);
            
            $fac_mid_avgs = [];
            $fac_end_avgs = [];

            if($res_fac) {
                while($fac = $res_fac->fetch_assoc()) {
                    $f_id = $fac['f_id'];
                    $mid_avg = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $sem, $year, $dept_id, 'mid', false);
                    if($mid_avg >= 0) $fac_mid_avgs[] = $mid_avg;
                    
                    $end_avg = getWeightedAverage($conn, $f_id, $course_code, $class, $section_or_batch, $sem, $year, $dept_id, 'end', false);
                    if($end_avg >= 0) $fac_end_avgs[] = $end_avg;
                }
            }

            if(count($fac_mid_avgs) > 0) {
                $batch_mid_avgs[] = array_sum($fac_mid_avgs) / count($fac_mid_avgs);
            }
            if(count($fac_end_avgs) > 0) {
                $batch_end_avgs[] = array_sum($fac_end_avgs) / count($fac_end_avgs);
            }
        }
    }

    $avg_mid = count($batch_mid_avgs) > 0 ? array_sum($batch_mid_avgs) / count($batch_mid_avgs) : -1;
    $avg_end = count($batch_end_avgs) > 0 ? array_sum($batch_end_avgs) / count($batch_end_avgs) : -1;

    ?>

<tr id="swacr">
    
    <td class="ca" id="ccode"><?= $course_code ?></td>
    <td class="ca"  id="cname"><?= $c_name ?></td>
    <td class="ca"  id="ctype"><?= $ct ?></td>
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

<?php } 

//FOR ELECTIVES
$sqlE = "SELECT electiveID, electiveName FROM electives WHERE acad_year='$year' and sem='$sem' and dept_id='$dept_id'";
$resE= $conn->query($sqlE);
while($rowE=$resE->fetch_assoc()){
  $course_code=$rowE['electiveID'];
  $c_name=$rowE['electiveName'];
  

  	$c = 'TH';
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

    $s_fac = "SELECT DISTINCT f_id FROM electives WHERE electiveID='$course_code' AND acad_year='$year' AND sem='$sem' AND dept_id='$dept_id'";
    $res_fac = $conn->query($s_fac);

    $fac_mid_avgs = [];
    $fac_end_avgs = [];

    if($res_fac) {
        while($fac = $res_fac->fetch_assoc()) {
            $f_id = $fac['f_id'];
            $mid_avg = getWeightedAverage($conn, $f_id, $course_code, '', '', $sem, $year, $dept_id, 'mid', true);
            if($mid_avg >= 0) $fac_mid_avgs[] = $mid_avg;
            
            $end_avg = getWeightedAverage($conn, $f_id, $course_code, '', '', $sem, $year, $dept_id, 'end', true);
            if($end_avg >= 0) $fac_end_avgs[] = $end_avg;
        }
    }

    $avg_mid = count($fac_mid_avgs) > 0 ? array_sum($fac_mid_avgs) / count($fac_mid_avgs) : -1;
    $avg_end = count($fac_end_avgs) > 0 ? array_sum($fac_end_avgs) / count($fac_end_avgs) : -1;
?>

<tr id="swacr">
    
    <td class="ca" id="ccode"><?= $course_code ?></td>
    <td class="ca"  id="cname"><?= $c_name." (Ele/IDC/Au)" ?></td>
    <td class="ca"  id="ctype"><?= $ct ?></td>
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

<?php } ?>

</thead>

    <tbody>


    </tbody>
</table>

<!-- Print Table -->
<!-- <button id="button_print_swac"  class="btn btn-success" onclick="printDataSummarySWAC()">Print</button>
<script>

  function printDataSummarySWAC()
  {
   var divToPrint=document.getElementById("6");
   newWin= window.open("");
   newWin.document.write(divToPrint.outerHTML);
   newWin.document.write("<head><style>@media print{ table{border-collapse: collapse;} table, td, th{ border:1px solid #000; text-align:center;padding-left:20px;padding-right:20px;} </style></head>")
   newWin.print();
   newWin.close();
 }

 $('#button_print_swac').on('click',function(){
  printDataSummary();
});
</script>

Download as Excel
<button id="swac_export_excel"  class="btn btn-success" onclick="swacExport()">Download as excel file</button>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
    <script type="text/javascript">
        function swacExport() {
            $("#6").table2excel({
                filename: "Overall_Course_feedback.xls"
            });
        }
    </script>
-->
