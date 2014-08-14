SELECT 
    cc.userid As 'userid',
    ud.contractedhours As 'contractedhours',
    cm.course  As 'courseid', 
    1 AS 'checkpoint',
	IF(cmc.timemodified IS NULL,
		IF(UNIX_TIMESTAMP() - CAST(cc.timeenrolled AS SIGNED) < 1814400, 1, 2) ,
		IF((CAST(cmc.timemodified AS SIGNED) - CAST(cc.timeenrolled AS SIGNED)) < 1814400, 4, 3) 
	)  AS 'chkcomp' ,
	#IF(UNIX_TIMESTAMP() - CAST(cc.timeenrolled AS SIGNED) < 1814400, 1, 2) AS 'chkcomp2',
    #IF((CAST(cmc.timemodified AS SIGNED) - CAST(cc.timeenrolled AS SIGNED)) < 1814400, 4, 3) AS 'chkcomp',
	cmc.timemodified,
    cc.timeenrolled AS 'timeenrolled',
    coursemoduleid As 'coursemoduleid',
    l.name AS 'labelname',
    cmc.timemodified,
    completionstate
FROM mdl_course_completions cc 
INNER JOIN mdl_course_modules cm ON cc.course = cm.course
INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
INNER JOIN mdl_course c ON c.id = cc.course
INNER JOIN mdl_course_categories cat ON c.category = cat.id
LEFT JOIN mdl_dynamic_userdata ud On ud.userid = cc.userid
LEFT JOIN mdl_course_modules_completion cmc ON cc.userid = cmc.userid AND cm.id = cmc.coursemoduleid
WHERE cm.module = 10
#AND cc.course = 21
AND cat.path LIKE '/2%' 
AND l.name IN ('Welcome') #Change labels
AND ud.contractedhours = 'full time'