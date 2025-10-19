<?php include('pretitle.php'); ?>
<title>Stage Plot Viewer Locations</title>
<?php include('posttitle.php'); ?>

<div class="header">Locations</div><br>
<?php
	if(isset($_POST['submit']))
	{
		$x=1;
		while(isset($_POST["id$x"]))
		{
			$id=str_replace("'","''",$_POST["id$x"]);
			$name=str_replace("'","''",$_POST["name$x"]);
			$template="";
			if($_FILES["template$x"]["error"] == 0)
			{
				$uploadname=$_FILES["template$x"]["name"]; $extarr=explode(".",$uploadname); $uploadext=strtolower(end($extarr));
				if(file_exists("/var/www/html/spv/files/$name.$uploadext")) { unlink("/var/www/html/spv/files/$name.$uploadext"); }
				move_uploaded_file($_FILES["template$x"]["tmp_name"], ("/var/www/html/spv/files/$name.$uploadext"));
				$template=", Template_File='$name.$uploadext'";
			}

			$update="UPDATE Locations SET Location_Name='$name' $template WHERE (Location_ID='$id')";
			if(!mysqli_query($db,$update)) { echo("Unable to Run Query: $update"); exit; }

			if(isset($_POST["delete$x"])) { $delete="DELETE FROM Locations WHERE (Location_ID='$id')";
			if(!mysqli_query($db,$delete)) { echo("Unable to Run Query: $delete"); exit; } }
			$x++;
		}

		if(isset($_POST['newloc']) && trim($_POST['newloc']) != "")
		{
			$newloc=str_replace("'","''",$_POST["newloc"]);
			$insert="INSERT INTO Locations(Location_Name) VALUES('$newloc')";
			if(!mysqli_query($db,$insert)) { echo("Unable to Run Query: $insert"); exit; }
		}
	}

	$locations="SELECT * FROM Locations ORDER BY Location_Name"; $table=""; $x=0;
	if(!$rs=mysqli_query($db,$locations)) { echo("Unable to Run Query: $locations"); exit; }
	while($row = mysqli_fetch_array($rs))
	{
		if(($x%2) == 0) { $table.=("<tr class='tr_odd'>\n"); } else { $table.=("<tr class='tr_even'>\n"); } $x++;
		$table.=("<th>" . $row['Location_ID'] . "<input type='hidden' name='id$x' value=\"" . $row['Location_ID'] . "\" /></th>\n");
		$table.=("<td><input type='text' name='name$x' value=\"" . $row['Location_Name'] . "\" /></td>\n");
		$table.=("<td><input type='file' name='template$x' /></td>\n");
		$table.=("<th><input type='checkbox' name='delete$x' /></th>\n");
		$table.=("</tr>\n");
	}

	echo("<form method='post' action='' enctype='multipart/form-data'>\n");
	echo("<table>\n<tr>\n<th>ID</th>\n<th>Name</th><th>Template</th><th>Delete</th>\n</tr>\n$table</table>\n");
	echo("<br>New Location Name: <input type='text' name='newloc' />\n <br><br><input type='submit' name='submit' value='Submit Changes' />\n</form>\n");

	echo("<br><br><a href='manageplot.php'>Add / Manage Plots</a>\n");
?>

<?php include('footer.php'); ?>
