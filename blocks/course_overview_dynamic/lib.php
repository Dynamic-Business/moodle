<?php

/*

	Function copied directly from course/lib.php and modified


*/


//copied from above specifically for new overview block
function print_overview_dynamic($courses, array $remote_courses=array()) {
	
    global $CFG, $USER, $DB, $OUTPUT;

    $htmlarray = array();
    if ($modules = $DB->get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php')) {
                include_once(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($courses,$htmlarray);
                }
            }
        }
    }
	echo $OUTPUT->box_start('coursebox');
	echo "<div class='overview-info-icon heading'>&nbsp;</div>";
	echo "<div class='overview-info-course heading'>Course</div>";
	echo "<div class='overview-info-other de heading'>Date<br>Enrolled</div>";
	echo "<div class='overview-info-other cs heading'>Course<br>Status</div>";
	echo "<div class='overview-info-other ac heading'>Activities<br>Completed</div>";
	echo "<div class='overview-info-other dc heading'>Date<br>Completed</div>";
	echo $OUTPUT->box_end();
	
    foreach ($courses as $course) {
		//DYNAMIC CODE
		$sql = "SELECT userid,course,FROM_UNIXTIME(timeenrolled,'%d/%m/%Y') AS 'timeenrolled',timestarted AS 'ts', FROM_UNIXTIME(timestarted,'%d/%m/%Y') AS 'timestarted',FROM_UNIXTIME(timecompleted,'%d/%m/%Y') AS 'timecompleted' FROM mdl_course_completions WHERE course = " . $course->id . " AND userid = " . $USER->id;
		$rs = $DB->get_records_sql($sql);
		//
		$sql2 = "SELECT count(id) AS 'totalactivities' FROM mdl_course_completion_criteria WHERE course = " . $course->id  . " LIMIT 1";
		$rs2 = $DB->get_field_sql($sql2);
		//
		$sql3 = "SELECT count(cmc.id) AS 'activitiesCompleted' FROM mdl_course_modules cm
				INNER JOIN mdl_course_modules_completion cmc ON cmc.coursemoduleid = cm.id
				INNER JOIN mdl_course_completion_criteria ccc ON ccc.moduleinstance = cmc.coursemoduleid
				WHERE cm.course = " . $course->id . " AND userid = " . $USER->id . " AND completionstate IN (1,2) ";
		$rs3 = $DB->get_field_sql($sql3);
		//
		$statusVal = "Not Tracked";
		$enrolledVal = "&nbsp;";
		//$completedVal = "&nbsp;";
		$completedVal = "N/A";
		$activitiesCompleted = "0";
		$totalActivities = "0";
		$statusimg = "0";
		$totalActivities = $rs2;
		$activitiesCompleted = $rs3;
		//
		foreach($rs as $row) {
			if($row->ts == 0){
				$statusVal = "Not Started";
				$statusimg = "notattempted";
			}else if($row->timecompleted != NULL) {
				$statusVal = "Complete";
				$statusimg = "passed";
			}else{
				$statusVal = "In Progress";
				$statusimg = "incomplete";
			}
			if (isset($row->timeenrolled)){$enrolledVal = $row->timeenrolled;};
			$completedVal = $row->timecompleted;
		}
		
		if($statusVal != "Not Tracked"){
			$activitesCombined = $activitiesCompleted . " of " . $totalActivities;
		}else{
			//$activitesCombined = "&nbsp;";
			$activitesCombined = "N/A";
		}
		//
		//echo $OUTPUT->box_start('coursebox');
		if($statusVal == "Not Tracked"){
			echo $OUTPUT->box_start('coursebox nottracked');
		}else{
			echo $OUTPUT->box_start('coursebox');
		}
		
		
		echo "<div class='overview-info-icon'>";
		if ($statusimg != "0"){
			echo "<img src='" . $OUTPUT->pix_url($statusimg, 'scorm') . "' alt='icon' />";
		}else{
			echo "&nbsp;";
		}
		echo "</div>";
        $attributes = array('title' => s($course->fullname));
        if (empty($course->visible)) {
            $attributes['class'] = 'dimmed';
        }
		echo "<div class='box overview-info-course'>";
		echo "<h6><a href='" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . "'>" . $course->fullname . "</a></h6>"; 
		echo "</div>";
		
        /*if (array_key_exists($course->id,$htmlarray)) {foreach ($htmlarray[$course->id] as $modname => $html) {echo $html;}}*/ //commented out from original
		echo "<div class='overview-info-other de'>" . $enrolledVal . "</div>";
		echo "<div class='overview-info-other cs'>" . $statusVal . "</div>";
		echo "<div class='overview-info-other ac'>". $activitesCombined . "</div>";
		echo "<div class='overview-info-other dc'>" . $completedVal  . "</div>";

		//---- DYNMAIC CODE END
		echo $OUTPUT->box_end();
    }

	//Removed Remote courses Code - never used by our clients
    /*if (!empty($remote_courses)) {
        echo $OUTPUT->heading(get_string('remotecourses', 'mnet'));
    }
    foreach ($remote_courses as $course) {
        echo $OUTPUT->box_start('coursebox');
        $attributes = array('title' => s($course->fullname));
        echo $OUTPUT->heading(html_writer::link(
            new moodle_url('/auth/mnet/jump.php', array('hostid' => $course->hostid, 'wantsurl' => '/course/view.php?id='.$course->remoteid)),
            format_string($course->shortname),
            $attributes) . ' (' . format_string($course->hostname) . ')', 3);
        echo $OUTPUT->box_end();
    }*/
}

?>