#Get the labels and their associated course
SELECT course,name FROM mdl_label WHERE name REGEXP 'checkpoint 1|competent review|experienced review|final review';
SELECT course,name FROM mdl_label WHERE name REGEXP '12 Week|review|Service';

### Id for label module is 10
SELECT l.course,name,cm.* FROM mdl_label l
INNER JOIN mdl_course_modules cm ON l.course = cm.course
WHERE l.name REGEXP '12 Week|review|Service'
AND cm.instance =10;

-- Loop through all conditions for each course type:
SELECT cmc.userid,cc.timeenrolled,cm.course,c.fullname,coursemoduleid,l.name,completionstate,cmc.timemodified,cm.section
FROM mdl_course_modules_completion cmc
INNER JOIN mdl_course_modules cm ON cm.id = cmc.coursemoduleid
INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
INNER JOIN mdl_course c ON c.id = cm.course
INNER JOIN mdl_course_completions cc ON cc.userid = cmc.userid AND cc.course = cm.course
WHERE module = 10
AND l.name IN ('service','welcome') #Change labels
ORDER BY userid;

-- -- So for example
# Retail Academy Part Time
SELECT cmc.userid,ud.contractedhours,cc.timeenrolled,cm.course,c.fullname,coursemoduleid,l.name,completionstate,cmc.timemodified,cm.section
FROM mdl_course_modules_completion cmc
INNER JOIN mdl_course_modules cm ON cm.id = cmc.coursemoduleid
INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
INNER JOIN mdl_course c ON c.id = cm.course
INNER JOIN mdl_course_completions cc ON cc.userid = cmc.userid AND cc.course = cm.course
INNER JOIN mdl_course_categories cat ON c.category = cat.id
LEFT JOIN mdl_dynamic_userdata ud On ud.userid = cmc.userid
WHERE module = 10
AND cat.path LIKE '/36%'  #change path - comes from rules table
AND l.name IN ('welcome') #Change labels
AND ud.contractedhours = "Part Time" #If course level then course LIKE "%part time%'
ORDER BY userid;

#
SELECT 
    'chk1' AS 'checkpoint',
    cmc.userid,ud.contractedhours,
    cc.timeenrolled,
    cm.course,
    c.fullname,
    coursemoduleid,
    l.name,
    completionstate,
    cmc.timemodified,
    cm.section #,
    # IF((CAST(cmc.timemodified AS SIGNED) - CAST(cc.timeenrolled AS SIGNED)) < 1814400, "yes", "no") AS 'chk1'
FROM mdl_course_modules_completion cmc
INNER JOIN mdl_course_modules cm ON cm.id = cmc.coursemoduleid
INNER JOIN mdl_label l ON l.course = cm.course AND l.id = cm.instance
INNER JOIN mdl_course c ON c.id = cm.course
INNER JOIN mdl_course_completions cc ON cc.userid = cmc.userid AND cc.course = cm.course
INNER JOIN mdl_course_categories cat ON c.category = cat.id
LEFT JOIN mdl_dynamic_userdata ud On ud.userid = cmc.userid
WHERE module = 10
#AND cat.path LIKE '/2%' 
AND l.name IN ('Welcome') #Change labels
#AND ud.contractedhours = 'full time' 

CREATE  TABLE `next2`.`mdl_dynamic_sp` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT ,
  `userid` BIGINT(10) NOT NULL ,
  `courseid` BIGINT(10) NOT NULL ,
  `contractedhours` VARCHAR(15) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `uid` (`userid` ASC) ,
  INDEX `cid` (`courseid` ASC) ,
  UNIQUE INDEX `uid_cid_uq` (`courseid` ASC, `userid` ASC) )

SELECT 
    cc.userid,cc.course,ud.contractedhours
FROM mdl_course_completions cc
INNER JOIN mdl_course c ON c.id = cc.course
INNER JOIn mdl_dynamic_userdata ud On cc.userid = ud.userid
INNER JOIN mdl_course_categories cat ON c.category = cat.id
WHERE cc.deleted IS NULL
AND (cat.path LIKE '/2%' OR cat.path LIKE '/2%' OR cat.path LIKE '/36%' OR cat.path LIKE '/39%' )

SELECT *
FROM mdl_dynamic_spdata sp
INNER JOIN mdl_dynamic_userdata ud ON ud.userid = sp.userid
WHERE sp.courseid = 21 
AND storedetails = 'N0724 Brighton Hollingbury'
AND checkpoint = 1;

