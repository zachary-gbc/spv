<?php
  include('pretitle.php');

  if(isset($_GET['plot']))
  {
    $getid=$_GET['plot'];
    $plot="SELECT Plot_ID, Plot_Name FROM StagePlots WHERE (Plot_ID='$getid')"; $plotid=""; $name="";
    if(!$rs=mysqli_query($db,$plot)) { echo("Unable to Run Query: $plot"); exit; }
    while($row = mysqli_fetch_array($rs)) { $plotid=$row['Plot_ID']; $name=$row['Plot_Name']; }
  }
  elseif(isset($_GET['location']))
  {
    $location=$_GET['location'];
    $plots="SELECT Plot_ID, Plot_Name, Plot_Start1, Plot_Start2, Plot_Start3 FROM StagePlots WHERE (Plot_Location=$location)"; $plotid=""; $name=""; $idarray=array(); $names=array(); 
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
    if(count($idarray) > 0) { $plotid=end($idarray); $name=$names[$plotid]; }
  }
  else
  {
    $alllocations="SELECT Location_ID, Location_Name FROM StagePlots INNER JOIN Locations ON StagePlots.Plot_Location=Locations.Location_ID GROUP BY Plot_Location ORDER BY Location_Name"; $haslocation=false;
    if(!$rs=mysqli_query($db,$alllocations)) { echo("Unable to Run Query: $alllocations"); exit; }
    while($row = mysqli_fetch_array($rs))
    { echo("<h1><a href='?location=" . $row['Location_ID'] . "'>" . $row['Location_Name'] . "</a></h1><br>\n"); $haslocation=true; }
    if($haslocation == false) { echo("<h1>No Locations or Plots Available, Please Check Back Later</h1>\n"); }
  }

  echo("<title>$name Stage Plot</title>");
  
  include('posttitle.php');

  if ($plotid != "")
  {
    echo("<div class='viewplotname'><br>$name</div><br>");
    if(file_exists("files/$plotid.png")) { echo("<img src='files/$plotid.png' class='viewplotimage'>"); }
    else { echo("<div class='viewplotname'><br><br>No Plot File Available</div><br>"); }
  }

  echo("<form action='index.php' style='text-align:center'><input type='submit' value='REFRESH' /></form>");
  //echo("<video width='111' height='1' loop autoplay><source src=keepscreenon.mp4' type='video/mp4'></video>");
  include('footer.php');
?>
