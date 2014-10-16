<?php

/*

This was a direct copy of the course overview block.
Only real difference is that the csall to print_overview() in course/lib now calls my custom
print_overview_dynamic() in a local lib file. Edit this function to make enhancements to the page.

Setup:
- Install the plugin as normal
- Got to Appearance - Default My Moodle Page and customise the page.
- Add Course overview Dynamic as a block to centre of screen and remove the old version.


*/

require_once($CFG->dirroot.'/lib/weblib.php');
require_once($CFG->dirroot . '/lib/formslib.php');

class block_course_overview_dynamic extends block_base {
    /**
     * block initializations
     */
    public function init() {
        //$this->title   = get_string('pluginname', 'block_course_overview');
		$this->title   = "My Courses";
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $USER, $CFG;
        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $content = array();

        // limits the number of courses showing up
        $courses_limit = 1000;
        // FIXME: this should be a block setting, rather than a global setting
        if (isset($CFG->mycoursesperpage)) {
            $courses_limit = $CFG->mycoursesperpage;
        }

        $morecourses = false;
        if ($courses_limit > 0) {
            $courses_limit = $courses_limit + 1;
        }

        $courses = enrol_get_my_courses('id, shortname, modinfo', 'visible DESC,sortorder ASC', $courses_limit);
        $site = get_site();
        $course = $site; //just in case we need the old global $course hack

        if (is_enabled_auth('mnet')) {
            $remote_courses = get_my_remotecourses();
        }
        if (empty($remote_courses)) {
            $remote_courses = array();
        }

        if (($courses_limit > 0) && (count($courses)+count($remote_courses) >= $courses_limit)) {
            // get rid of any remote courses that are above the limit
            $remote_courses = array_slice($remote_courses, 0, $courses_limit - count($courses), true);
            if (count($courses) >= $courses_limit) {
                //remove the 'marker' course that we retrieve just to see if we have more than $courses_limit
                array_pop($courses);
            }
            $morecourses = true;
        }


        if (array_key_exists($site->id,$courses)) {
            unset($courses[$site->id]);
        }

        foreach ($courses as $c) {
			//echo $c->id;
            if (isset($USER->lastcourseaccess[$c->id])) {
                $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
            } else {
                $courses[$c->id]->lastaccess = 0;
				
            }
        }

        if (empty($courses) && empty($remote_courses)) {
            $content[] = get_string('nocourses','my');
        } else {
            ob_start();

            require_once $CFG->dirroot."/course/lib.php";
			require_once $CFG->dirroot."/blocks/course_overview_dynamic/lib.php";
			//DYNAMIC CHANGE
            print_overview_dynamic($courses, $remote_courses);
			//echo "hello";
			//sayHi();
			
			

            $content[] = ob_get_contents();
            ob_end_clean();
        }

        // if more than 20 courses
        if ($morecourses) {
            $content[] = '<br />...';
        }

        $this->content->text = implode($content);

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my-index'=>true);
    }
	
	//Copied (and amended) from print-overview in course/lib.php
	/*public function print_overview_dynamic($courses, array $remote_courses=array()) {
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
		foreach ($courses as $course) {
			echo $OUTPUT->box_start('coursebox');
			$attributes = array('title' => s($course->fullname));
			if (empty($course->visible)) {
				$attributes['class'] = 'dimmed';
			}
			echo $OUTPUT->heading(html_writer::link(
				new moodle_url('/course/view.php', array('id' => $course->id)), format_string($course->fullname), $attributes), 3);
			if (array_key_exists($course->id,$htmlarray)) {
				foreach ($htmlarray[$course->id] as $modname => $html) {
					echo $html;
				}
			}
			echo $OUTPUT->box_end();
		}
	
		if (!empty($remote_courses)) {
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
		}
	}*/
	//copied from above specifically for new overview block

}
?>
