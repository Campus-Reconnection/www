<?php

function searchCourses($search)
{
	$sql = "SELECT DISTINCT strCourseName AS courseName,
		intSectionID AS secID,
		CONCAT(tblcourse.strCourseID,'-',intSectionNumber) AS secNumber,
		strFirstName,
		strLastName,
		strDayFormat,
		CONCAT(DATE_FORMAT(timStartTime,'%l:%i%p'),'-',DATE_FORMAT(timEndTime,'%l:%i%p')) AS time,
		strFacilityName,
		strRoomNumber
		FROM tblCourse
		INNER JOIN tblsection ON tblcourse.strCourseID = tblsection.strCourseID
		INNER JOIN tblfaculty ON tblsection.intFacultyID = tblfaculty.intFacultyID
		INNER JOIN tblsectionschedule ON tblsection.intScheduleID = tblsectionschedule.intDaySlotID
		INNER JOIN tblsectiontimes ON tblsection.intTimeSlotID = tblsectiontimes.intTimeSlotID
		INNER JOIN tblroom ON tblsection.intRoomID = tblRoom.intRoomID
		INNER JOIN tblfacility ON tblroom.intFacilityID = tblFacility.intFacilityID";

	$term = preg_replace('/\s+/',' ',$search); 
	$term = preg_replace('/^\s|\s$/','',$term); 
//TODO: Sanitize $term
	$words = explode(' ',$term);
	 
	$fields = Array( 
	'tblcourse.strCourseID',  
	'tblcourse.strDeptCode',
	'tblfaculty.strLastName',  
	'tblfaculty.strFirstName',  
	'tblcourse.strCourseName'
	); 
	$where = NULL; 
	foreach ($words as $wd) { 
		$andwhere = NULL; 
		if ($where) { $where .= ' AND'; }
		foreach ($fields as $field) { 
			if (! empty($wd)) { 
				if ($andwhere) { $andwhere .= ' OR'; } 
				$andwhere .= " $field LIKE '%$wd%'"; 
			} 
		} 
		if ($andwhere) { $where .= " ($andwhere)"; } 
	} 

	if ($where) { $sql .= " WHERE $where ORDER BY secNumber"; }
	if ($result = dbGetAll($sql))
	{
		echo "<thead>";
		echo "<tr><td class=\"thr\">Select</td>";
		echo "<td class=\"thr\">Course Name</td>";
		echo "<td class=\"thr\">Section</td>";
		echo "<td class=\"thr\">Schedule</td>";
		echo "<td class=\"thr\">Time</td>";
		echo "<td class=\"thr\">Instructor</td>";
		echo "<td class=\"thr\">Facility</td>";
		echo "<td class=\"thr\">Room</td></tr>";
		echo "</thead><tbody>";
		
		foreach ($result as $row)
		{
			echo "<tr>";
			//echo "<td class=\"advcell\"><input type=\"checkbox\" name=\"check[".$row['secID']."]\" value=\"\" /></td>";
			echo "<td class=\"advcell\"><input type=\"checkbox\" name=\"check[]\" value=\"" .$row['secID']. "\" /></td>";
			echo "<td class=\"advcell\">".$row['courseName']."</td>";
			echo "<td class=\"advcell\">".$row['secNumber']."</td>";
			echo "<td class=\"advcell\">".$row['strDayFormat']."</td>";
			echo "<td class=\"advcell\">".$row['time']."</td>";
			echo "<td class=\"advcell\">".$row['strFirstName'].' '.$row['strLastName']."</td>";
			echo "<td class=\"advcell\">".$row['strFacilityName']."</td>";
			echo "<td class=\"advcell\">".$row['strRoomNumber']."</td>";	
			echo "</tr>";
		}
		
		echo "</tbody>";
		
	}
	else
	{
		echo "<tr><td class=\"advcell\">Your search returned no results.</td></tr>";
	}
}

function addCourse($stuID, $secID)
{
	$sql = "INSERT INTO tblstudentenrollment(intStudentID,intSectionID,strEnrollmentStatus) VALUES (?, ?, 'E');";
	return dbPush($sql, "ii", $stuID, $secID);
}

function getAllDepartments()
{
	$sql = "SELECT * FROM tbldepartment ORDER BY strDeptCode ASC";
	return $sql;
}


?>