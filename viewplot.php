<?php
  include('pretitle.php');

  if(isset($_GET['plot']))
  {
    $getid=$_GET['plot'];
    $plot="SELECT Plot_Name FROM StagePlots WHERE (Plot_ID='$getid')"; $plotid=""; $name="";
    if(!$rs=mysqli_query($db,$plot)) { echo("Unable to Run Query: $plot"); exit; }
    while($row = mysqli_fetch_array($rs)) { $id=$row['Plot_ID']; $name=$row['Plot_Name']; }
  }
  else
  {
    $plots="SELECT Plot_ID, Plot_Name, Plot_Start1, Plot_Start2, Plot_Start3 FROM StagePlots"; $plotid=""; $name=""; $idarray=array(); $names=array(); 
    if(!$rs=mysqli_query($db,$plots)) { echo("Unable to Run Query: $plots"); exit; }
    while($row = mysqli_fetch_array($rs))
    {
      $id=$row['Plot_ID']; $names[$id]=$row['Plot_Name'];
      $start1=strtotime($row['Plot_Start1']);
      $start2=strtotime($row['Plot_Start2']);
      $start3=strtotime($row['Plot_Start3']);
  
      if($start1 <= time()) { $idarray[$start1]=$id; }
      if($start2 <= time()) { $idarray[$start2]=$id; }
      if($start3 <= time()) { $idarray[$start3]=$id; }
    }
    ksort($idarray);
    if(count($idarray) > 0) { $plotid=$idarray[0]; $name=$names[$plotid]; }
  }

  echo("<title>$name Stage Plot</title>");
  
  include('posttitle.php');

  if ($plotid != "")
  {
    echo("<div class='viewplotname'><br>$name</div><br>");
    if(file_exists("files/$id.png")) { echo("<img src='files/$id.png' class='viewplotimage'>"); }
    else { echo("<div class='viewplotname'><br><br>No Plot File Available</div><br>"); }
  }

  echo("<form action='viewplot.php' style='text-align:center'><input type='submit' value='REFRESH' /></form>");
  
  include('footer.php');
?>
