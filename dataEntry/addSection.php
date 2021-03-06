<?php
require("../library/system.php");

if (isset($_POST["waitlisttotal"]))
{
	$startdate = "";
	$enddate = "";
	
	switch($_POST["season"])
	{
		case "Fall":
			$startdate = $_POST["year"] . "-08-24";
			$enddate = $_POST["year"] . "-12-18";
			break;
		case "Spring":
			$startdate = $_POST["year"] . "-01-11";
			$enddate = $_POST["year"] . "-05-13";
			break;
		case "Summer":
			$startdate = $_POST["year"] . "-05-19";
			$enddate = $_POST["year"] . "-08-07";
			break;
		default:
			$startdate = "1940-10-9";
			$enddate = "1980-12-8";
			break;
	}
	
	$sql = "INSERT INTO tblsection(intSectionNumber, strCourseID, intRoomID, intFacultyID, intTimeSlotID, intScheduleID, intYear, strSeason, dtmStartDate, dtmEndDate, intCapacity, intWaitlistTotal, blnOnlineSection)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
	dbPush($sql, "isiiiiisssiii", $_POST["sectionnumber"], $_POST["courseid"], $_POST["roomid"], $_POST["facultyid"], $_POST["timeslot"], $_POST["schedule"], $_POST["year"], $_POST["season"], $startdate, $enddate, $_POST["capacity"], $_POST["waitlisttotal"], $_POST["onlinesection"]);
}

function seasonoptions($name)
{
	echo "<select name=\"" . $name . "\">";
	echo "<option value=\"Fall\">Fall</option>";
	echo "<option value=\"Spring\">Spring</option>";
	echo "<option value=\"Summer\">Summer</option>";
	echo "</select>";
}

function roomoptions($name)
{
	$sql = "SELECT intRoomID, strRoomNumber, tblFacility.strFacilityName AS \"strFacility\"
		FROM tblroom
		INNER JOIN tblfacility ON tblroom.intFacilityID = tblFacility.intFacilityID
		ORDER BY tblFacility.strFacilityName, tblRoom.strRoomNumber;";
	
	echo "<select name=\"" . $name . "\">";
	
	if ($result = dbGetAll($sql))
	{
		foreach ($result as $row)
		{
			echo "<option value=\"" . $row["intRoomID"] . "\">" . $row["strRoomNumber"] . " - " . $row["strFacility"] . "</option>";
		}
    }
	
	echo "</select>";
}

function facultyoptions($name)
{
	$sql = "SELECT intFacultyID, strFirstName, strLastName FROM tblfaculty ORDER BY strLastname, strFirstname;";
	
	echo "<select name=\"" . $name . "\">";
	
	if ($result = dbGetAll($sql))
	{
		foreach ($result as $row)
		{
			echo "<option value=\"" . $row["intFacultyID"] . "\">" . $row["strFirstName"] . " " . $row["strLastName"] . "</option>";
		}
    }
	
	echo "</select>";
}

function scheduleoptions($name)
{
	$sql = "SELECT intDaySlotID, strDayFormat FROM tblsectionschedule;";
	
	echo "<select name=\"" . $name . "\">";
	
	if ($result = dbGetAll($sql))
	{
		foreach ($result as $row)
		{
			echo "<option value=\"" . $row["intDaySlotID"] . "\">" . $row["strDayFormat"] . "</option>";
		}
    }
	
	echo "</select>";
}

function timeslotoptions($name)
{
	$sql = "SELECT intTimeSlotID, CONCAT(timStartTime,\" to \",timEndTime) AS \"strTime\" FROM tblsectiontimes;";
	
	echo "<select name=\"" . $name . "\">";
	
	if ($result = dbGetAll($sql))
	{
		foreach ($result as $row)
		{
			echo "<option value=\"" . $row["intTimeSlotID"] . "\">" . $row["strTime"] . "</option>";
		}
    }
	
	echo "</select>";
}

function showcourses()
{
	$sql = "SELECT crs.strCourseID AS \"cid\" FROM tblcourse crs LEFT JOIN tblsection sec ON sec.strCourseID = crs.strCourseID WHERE sec.intSectionID IS NULL;";
	
	if ($result = dbGetAll($sql))
	{
		echo "<ul style=\"font-size:0.75em\">";
		
		foreach ($result as row)
		{
			echo "<li>" . $row["cid"] . "</li>";
		}
		
		echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Section</title>
</head>
<body>
<h2>Add a Section!</h2>
<form method="POST" action="addSection.php">
<table>
<tr><td>Section Number:</td><td><input type="text" name="sectionnumber" value="1" /></td></tr>
<tr><td>Course ID:</td><td><input type="text" name="courseid" value="" /></td></tr>
<tr><td>Room ID:</td><td><?php roomoptions("roomid"); ?></td></tr>
<tr><td>Faculty ID:</td><td><?php facultyoptions("facultyid"); ?></td></tr>
<tr><td>Time Slot ID:</td><td><?php timeslotoptions("timeslot"); ?></td></tr>
<tr><td>Schedule ID:</td><td><?php scheduleoptions("schedule"); ?></td></tr>
<tr><td>Year:</td><td><input type="text" name="year" value="" /></td></tr>
<tr><td>Season:</td><td><?php seasonoptions("season"); ?></td></tr>
<tr><td>Capacity:</td><td><input type="text" name="capacity" value="30" /></td></tr>
</table>
<input type="hidden" name="waitlisttotal" value="0" />
<input type="hidden" name="onlinesection" value="0" />
<input type="submit" />
</form>
<br />
Courses With No Section:
<?php showcourses(); ?>
</body>
</html>