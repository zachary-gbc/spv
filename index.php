<?php
  include('pretitle.php'); $urlvars="";

  if(isset($_GET['plot']))
  {
    $getid=$_GET['plot'];
    $plot="SELECT Plot_ID, Plot_Name, Plot_Location FROM StagePlots WHERE (Plot_ID='$getid')"; $plotid=""; $location=""; $name="";
    if(!$rs=mysqli_query($db,$plot)) { echo("Unable to Run Query: $plot"); exit; }
    while($row = mysqli_fetch_array($rs)) { $plotid=$row['Plot_ID']; $name=$row['Plot_Name']; $location=$row['Plot_Location']; }
    $urlvars="<input type='hidden' name='location' value='$location' />";
  }
  elseif(isset($_GET['location']))
  {
    $location=$_GET['location']; $plotid=""; $name=""; $idarray=array(); $names=array(); 
    $plots="SELECT Plot_ID, Plot_Name, Plot_Start1, Plot_Start2, Plot_Start3 FROM StagePlots WHERE (Plot_Location=$location)";
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
    $urlvars="<input type='hidden' name='location' value='$location' />";
  }
  else
  {
    echo("<div style='margin:auto;width:75%;text-align:center'>\n"); $plotid=""; $name="";
    $alllocations="SELECT Location_ID, Location_Name FROM StagePlots INNER JOIN Locations ON StagePlots.Plot_Location=Locations.Location_ID GROUP BY Plot_Location ORDER BY Location_Name"; $haslocation=false;
    if(!$rs=mysqli_query($db,$alllocations)) { echo("Unable to Run Query: $alllocations"); exit; }
    while($row = mysqli_fetch_array($rs))
    { echo("<br><h1><a href='?location=" . $row['Location_ID'] . "'>" . $row['Location_Name'] . "</a></h1><br>\n"); $haslocation=true; }
    if($haslocation == false) { echo("<h1>No Locations or Plots Available, Please Check Back Later</h1>\n"); }
    echo("</div>\n");
  }

  echo("<title>$name Stage Plot</title>");

  include('posttitle.php');

  if ($plotid != "")
  {
    echo("<div class='viewplotname'><br>$name</div><br>");
    if(file_exists("files/$plotid.png")) { echo("<img src='files/$plotid.png' class='viewplotimage'>"); }
    else { echo("<div class='viewplotname'><br><br>No Plot File Available</div><br>"); }
    echo("<form method='get' action='index.php' style='text-align:center'>$urlvars<input type='submit' value='REFRESH' /></form>");
  }
  include('footer.php');
?>
