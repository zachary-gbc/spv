<?php include('pretitle.php'); ?>
<title>Stage Plot Viewer</title>
<?php include('posttitle.php'); ?>

<div class="header">Stage Plot Viewer</div><br>
<a href='index.php'>View Next Plot</a>
<?php
	if(isset($_POST['submit']))
	{
		$x=1;
		while(isset($_POST["id$x"]))
    {
			$id=str_replace("'","''",$_POST["id$x"]);
			$name=str_replace("'","''",$_POST["name$x"]);
			$eventdateinput=strtotime($_POST["eventdate$x"]); $eventdate=date('Y-m-d',$eventdateinput);
			$start1input=strtotime($_POST["start1$x"]); $start1=date('Y-m-d H:i:s',$start1input);
			$start2input=strtotime($_POST["start2$x"]); $start2=date('Y-m-d H:i:s',$start2input);
			$start3input=strtotime($_POST["start3$x"]); $start3=date('Y-m-d H:i:s',$start3input);

			$update="UPDATE StagePlots SET Plot_Name='$name', Plot_EventDate='$eventdate', Plot_Start1='$start1', Plot_Start2='$start2', Plot_Start3='$start3' WHERE (Plot_ID='$id')";
			if(!mysqli_query($db,$update)) { echo("Unable to Run Query: $update"); exit; }
			if(isset($_POST["delete$x"])) { $delete="DELETE FROM StagePlots WHERE (Plot_ID='$id')"; if(!mysqli_query($db,$delete)) { echo("Unable to Run Query: $delete"); exit; } }
			$x++;
		}

		if(isset($_POST['newplot']) && trim($_POST['newplot']) != "" && $_FILES["uploadfile"]["error"] == 0)
		{
			$newplot=str_replace("'","''",$_POST["newplot"]);
			$newlocation=str_replace("'","''",$_POST["newlocation"]);
			$neweventdateinput=strtotime($_POST['neweventdate']); $neweventdate=date('Y-m-d',$neweventdateinput);
			$newstart1input=strtotime($_POST['newstart1']); $newstart1=date('Y-m-d H:i:s',$newstart1input);
			$newstart2input=strtotime($_POST['newstart2']); $newstart2=date('Y-m-d H:i:s',$newstart2input);
			$newstart3input=strtotime($_POST['newstart3']); $newstart3=date('Y-m-d H:i:s',$newstart3input);
			$insert="INSERT INTO StagePlots(Plot_Name, Plot_Location, Plot_EventDate, Plot_Start1, Plot_Start2, Plot_Start3) VALUES('$newplot', '$newlocation', '$neweventdate', '$newstart1', '$newstart2', '$newstart3')";
			if(!mysqli_query($db,$insert)) { echo("Unable to Add New Plot"); exit; }

      $lastid=mysqli_insert_id($db);
			$image=imagecreatefromstring(file_get_contents($_FILES["uploadfile"]["tmp_name"]));
			imagepng($image,"files/$lastid.png");
			imagedestroy($image);
		}

    $deleteold="SELECT Plot_ID FROM StagePlots WHERE (Plot_EventDate < NOW() - INTERVAL 6 DAY)"; $id="";
		if(!$rs=mysqli_query($db,$deleteold)) { echo("Unable to Run Query: $deleteold"); exit; }
		while($row = mysqli_fetch_array($rs))
		{
			$id=$row['Plot_ID'];
			unlink("files/$id.png");
			$delete="DELETE FROM StagePlots WHERE (Plot_ID='$id')"; $id="";
			if(!mysqli_query($db,$delete)) { echo("Unable to Run Query: $delete"); exit; }
		}
	}

	$defaultnow=date("Y-n-d\TH:m");
	$plots="SELECT Plot_ID, Plot_Name, Plot_EventDate, Plot_Start1, Plot_Start2, Plot_Start3, Location_Name, GREATEST(Plot_Start1, Plot_Start2, Plot_Start3) AS MaxStart FROM StagePlots INNER JOIN Locations ON StagePlots.Plot_Location=Locations.Location_ID ORDER BY Plot_Location, MaxStart DESC"; $table=""; $x=0;
	if(!$rs=mysqli_query($db,$plots)) { echo("Unable to Run Query: $plots"); exit; }
	while($row = mysqli_fetch_array($rs))
	{
		if(($x%2) == 0) { $table.=("<tr class='tr_odd'>\n"); } else { $table.=("<tr class='tr_even'>\n"); } $x++;
    $value1=""; $value2=""; $value3="";
    if(substr($row['Plot_EventDate'],0,4) != "1969") { $eventvalue="value='" . $row['Plot_EventDate'] . "'"; }
    if(substr($row['Plot_Start1'],0,4) != "1969") { $value1="value='" . $row['Plot_Start1'] . "'"; }
    if(substr($row['Plot_Start2'],0,4) != "1969") { $value2="value='" . $row['Plot_Start2'] . "'"; }
    if(substr($row['Plot_Start3'],0,4) != "1969") { $value3="value='" . $row['Plot_Start3'] . "'"; }


		$table.=("<th>" . $row['Plot_ID'] . "<input type='hidden' name='id$x' value=\"" . $row['Plot_ID'] . "\" /></th>\n");
		$table.=("<td>" . $row['Location_Name'] . "</td>\n");
		$table.=("<td><input type='text' name='name$x' value=\"" . $row['Plot_Name'] . "\" /></td>\n");
		$table.=("<td><input type='date' name='eventdate$x' $eventvalue /></td>\n");
		$table.=("<td><input type='datetime-local' name='start1$x' $value1 /></td>\n");
		$table.=("<td><input type='datetime-local' name='start2$x' $value2 /></td>\n");
		$table.=("<td><input type='datetime-local' name='start3$x' $value3 /></td>\n");
		$table.=("<td style='text-align:center'><a href='index.php?plot=" . $row['Plot_ID'] . "'/>View</a></td>\n");
		$table.=("<td><input type='checkbox' name='delete$x' /></td>\n");
		$table.=("</tr>\n");
	}

	$alllocations="SELECT Location_ID, Location_Name FROM Locations ORDER BY Location_Name"; $locations="";
	if(!$rs=mysqli_query($db,$alllocations)) { echo("Unable to Run Query: $alllocations"); exit; }
	while($row = mysqli_fetch_array($rs))
	{ $locations.=("<option value='" . $row['Location_ID'] . "'>" . $row['Location_Name'] . "</option>"); }

	echo("<form method='post' action='' enctype='multipart/form-data'>\n");
  echo("New Plot Name: <input type='text' name='newplot' />\n<br>");
  echo("For Location: <select name='newlocation' />$locations</select>\n<br>");
  echo("File: <input type='file' name='uploadfile' />\n<br>");
  echo("Event Date: <input type='date' name='neweventdate' />\n<br>");
  echo("Start 1: <input type='datetime-local' name='newstart1' value='$defaultnow' />\n<br>");
  echo("Start 2: <input type='datetime-local' name='newstart2' />\n<br>");
  echo("Start 3: <input type='datetime-local' name='newstart3' />\n<br>");
  if($table != "")
  {
		echo("<br><br><table>\n<tr>\n<th>ID</th>\n<th>Location</th>\n<th>Plot Name</th>\n<th>Event Date</th>\n");
		echo("<th>Start 1</th>\n<th>Start 2</th>\n<th>Start 3</th>\n<th>View Plot</th>\n<th>Delete</th>\n</tr>\n$table</table>\n");
	}
  echo("<br><br><input type='submit' name='submit' value='Submit Changes' />\n</form>\n");

	echo("<br><br><a href='locations.php'>Manage Locations</a>\n");
?>

<?php include('footer.php'); ?>
