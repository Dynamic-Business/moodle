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

class block_course_overview_next extends block_base {
    /**
     * block initializations
     */
    public function init() {
        //$this->title   = get_string('pluginname', 'block_course_overview');
		$this->title   = "My Training Sessions";
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
        //dynamic - error here.
        //$courses = enrol_get_my_courses('id, shortname, modinfo', 'visible DESC,sortorder ASC', $courses_limit);
        $courses = $this->custom_enrol_get_my_courses('id, shortname', 'visible DESC,sortorder ASC', $courses_limit);

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
            //$content[] = get_string('nocourses','my');
			$content[] = "No Training Session information to show.";
        } else {
            ob_start();

            require_once $CFG->dirroot."/course/lib.php";
			require_once $CFG->dirroot."/blocks/course_overview_next/lib.php";
			//DYNAMIC CHANGE
            print_overview_next($courses, $remote_courses); //------------------------------------------------------
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

    public function custom_enrol_get_my_courses($fields = NULL, $sort = 'visible DESC,sortorder ASC', $limit = 0) {
        global $DB, $USER;

        // Guest account does not have any courses
        if (isguestuser() or !isloggedin()) {
            return(array());
        }

        $basefields = array('id', 'category', 'sortorder',
                            'shortname', 'fullname', 'idnumber',
                            'startdate', 'visible',
                            'groupmode', 'groupmodeforce');

        if (empty($fields)) {
            $fields = $basefields;
        } else if (is_string($fields)) {
            // turn the fields from a string to an array
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
            $fields = array_unique(array_merge($basefields, $fields));
        } else if (is_array($fields)) {
            $fields = array_unique(array_merge($basefields, $fields));
        } else {
            throw new coding_exception('Invalid $fileds parameter in enrol_get_my_courses()');
        }
        if (in_array('*', $fields)) {
            $fields = array('*');
        }

        $orderby = "";
        $sort    = trim($sort);
        if (!empty($sort)) {
            $rawsorts = explode(',', $sort);
            $sorts = array();
            foreach ($rawsorts as $rawsort) {
                $rawsort = trim($rawsort);
                if (strpos($rawsort, 'c.') === 0) {
                    $rawsort = substr($rawsort, 2);
                }
                $sorts[] = trim($rawsort);
            }
            $sort = 'c.'.implode(',c.', $sorts);
            $orderby = "ORDER BY $sort";
        }

        $wheres = array("c.id <> :siteid");
        $params = array('siteid'=>SITEID);

        if (isset($USER->loginascontext) and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            // list _only_ this course - anything else is asking for trouble...
            $wheres[] = "courseid = :loginas";
            $params['loginas'] = $USER->loginascontext->instanceid;
        }

        $coursefields = 'c.' .join(',c.', $fields);
        list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        $wheres = implode(" AND ", $wheres);

        //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
        $sql = "
                SELECT $coursefields $ccselect
                    ,IF(cc.timestarted is null OR cc.timestarted=0 OR cc.timestarted='',1,IF(cc.timecompleted is null,2,3)) AS 'status'
                    FROM {course} c
                    JOIN (SELECT DISTINCT e.courseid,ue.userid
                          FROM {enrol} e
                          JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                         WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)
                    ) en ON (en.courseid = c.id)
                    LEFT JOIN {course_completions} cc ON  en.courseid = cc.course AND en.userid = cc.userid
               $ccjoin
                WHERE $wheres
                ORDER BY status,c.fullname
              ";
        $params['userid']  = $USER->id;
        $params['active']  = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
        $params['now1']    = round(time(), -2); // improves db caching
        $params['now2']    = $params['now1'];
        // echo "<pre>";
        // echo $sql; die;
        $courses = $DB->get_records_sql($sql, $params, 0, $limit);

        // preload contexts and check visibility
        foreach ($courses as $id=>$course) {
            context_instance_preload($course);
            if (!$course->visible) {

                /*if (!$context = context_course::instance($id, IGNORE_MISSING)) {
                    unset($courses[$id]);
                    continue;
                }*/

                $context = context_system::instance();

                if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                    unset($courses[$id]);
                    continue;
                }
            }
            $courses[$id] = $course;
        }

        //wow! Is that really all? :-D

        return $courses;
    }



}
?>