SELECT * FROM mdl_dynamic_spdata sp
INNER JOIN mdl_dynamic_userdata ud On ud.userid = sp.userid
INNER JOIN (

) ch1

WHERE ud.storedetails = 'N0423 Leeds Birstall Rp'
AND courseid = 25

##
SELECT cc.userid,cc.course
FROM mdl_course_completions cc
INNER JOIN (
    SELECT * FROM mdl_dynamic_spdata sp
    WHERE checkpoint =1
) ch1 ON ch1.courseid = cc.course AND ch1.userid = cc.userid

SELECT 
    ud.storedetails,
    u.firstname,
    u.lastname,
    cc.course,
    IF(ch1.chkcompl IS NULL,"",IF(ch1.chkcompl = 1,'Yes','No')) AS 'Checkpoint 1 Completed',
    ch2.chkcompl AS 'Checkpoint 2 Completed',
    ch3.chkcompl AS 'Checkpoint 3 Completed',
    ch4.chkcompl AS 'Checkpoint 4 Completed'
FROM mdl_course_completions cc
INNER JOIN mdl_dynamic_userdata ud ON ud.userid = cc.userid
INNER JOIN mdl_user u ON u.id = cc.userid
LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=1) ch1 ON ch1.courseid = cc.course AND ch1.userid = cc.userid
LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=2) ch2 ON ch1.courseid = cc.course AND ch2.userid = cc.userid
LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=3) ch3 ON ch1.courseid = cc.course AND ch3.userid = cc.userid
LEFT JOIN (SELECT * FROM mdl_dynamic_spdata WHERE checkpoint=4) ch4 ON ch1.courseid = cc.course AND ch4.userid = cc.userid
WHERE cc.course = 25
AND ud.storedetails = 'N0423 Leeds Birstall Rp'
AND (
    (ch1.chkcompl = 0 OR ch1.chkcompl IS NULL) 
    OR (ch2.chkcompl = 0 OR ch2.chkcompl IS NULL) 
    OR (ch3.chkcompl = 0 OR ch3.chkcompl IS NULL) 
    OR (ch4.chkcompl = 0 OR ch4.chkcompl IS NULL)
) 







