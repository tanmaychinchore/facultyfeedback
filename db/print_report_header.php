
<hr>
    <p style="text-align: center; color: black; text-align: center; font-size: 24px;  "><strong>K.J. SOMAIYA COLLEGE OF ENGINEERING, MUMBAI</strong></p>
    <p style="text-align: center; color: black; text-align: center; font-size: 18px; "><strong>(A Constituent College of Somaiya Vidyavihar University)</strong></p>
  <hr>
    <p style="text-align: center; color: black; text-align: center; font-size: 18px; "><strong>Department of <?php echo $dept_name; ?></strong></p>
<hr>
<?php
echo '<p style="text-align: center; font-size: 24px;"><strong>FACULTY FEEDBACK REPORT ('.$acad_year.')</strong></p>';
  if($sem_type=="Odd"){
    $sem_type_num=1;
    if($status==0)
      echo '<p style="text-align: center; font-size: 20px;"><strong>Odd Semester (Mid Term) </strong></p>';
    else
      echo '<p style="text-align: center; font-size: 18px;"><strong>Odd Semester (End Term) </strong></p>';
  }else{
    $sem_type_num=2;
    if($status==0)
      echo '<p style="text-align: center; font-size: 20px;"><strong>Even Semester (Mid Term) </strong></p>';
    else
      echo '<p style="text-align: center; font-size: 18px;"><strong>Even Semester (End Term) </strong></p>';
  }
  echo "<hr>";


  echo "<b style='font-size: 18px;'>Faculty Name: </b><strong style='color:#162252; font-size: 16px; '>".$fname." ".$lname."</strong><br><hr><br>";
?>
