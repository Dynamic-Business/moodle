<?php

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_next_responsive
 * @copyright  2014
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_next_responsive_core_renderer extends theme_dynamicbase_core_renderer {
    protected $debugging = false;

    public function dynamic_header($displayNav = true){
        global $CFG, $SITE, $OUTPUT; ?>




        <header role="banner" class="navbar moodle-has-zindex">



            <nav role="navigation" class="navbar-inner-dyn">
                <div class="navbar-inner-wrap top">

                    <div class="closebtn">
                        <?php if(isloggedin()){ ?>
                            <a href="javascript:window.location='<?php echo $CFG->wwwroot . "/login/logout.php?sesskey=" . $USER->sesskey; ?>'; window.opener='x';window.close();">Close</a>

                        <?php }else{ ?>
                            <a href="javascript:window.opener='x';window.close();"></a>
                        <?php } ?>
                    </div>


                    <div class="container-fluid">
                        <a class="brand" href="<?php echo $CFG->wwwroot;?>">
                            <img src="<?php echo $OUTPUT->pix_url('Site-logo','theme'); ?>" width="97" height="29" alt="" />
                        </a>
                    </div>
                </div>
         <?php  if($displayNav){
                
                    $this->navHTML();

                } ?>
            </nav>
        </header>

<?php }

    public function body_attributes($additionalclasses = array()) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        $userTypeClass = (!$this->is_administrator()) ? 'student-user ' : 'admin-user ';
        return ' id="'. $this->body_id().'" class="'.$userTypeClass.''.$this->body_css_classes($additionalclasses).' '.$this->develeopment_css_class().'"';
    }

    public function navHTML(){
        global $CFG, $OUTPUT; ?>
        
        <a class="menu-btn" data-toggle="collapse" data-target=".nav-collapse">
            <img src="<?php echo $OUTPUT->pix_url('menu-btn','theme'); ?>" width="20" height="20" alt="Open"/>Menu
        </a>
        <div class="navbar-inner-wrap bottom">
            <div class="nav-wrap">
                <ul class="nav main-nav">
<?php
/**
 * Nav Menus
 * ---------
 * Each calls a protected function that build that items list (if it has one)
 * Structure is as follows:
 * <li class="dropdown">
 *   <a class="dropdown-toggle" data-toggle="dropdown">...</a>
 *   <ul class="dropdown-menu">
 *     <li>...</li>
 *     ...
 *   </ul>
 * </li>
 * The <a> is the text that appears in the menu and is what's clicked.
 *
 * For a submenu it's like this:
 * <li class="dropdown">
 *   <a class="dropdown-toggle" data-toggle="dropdown">
 *   <ul class="dropdown-menu">
 *     <li class="dropdown">
 *       <a class="dropdown-toggle" data-toggle="dropdown">...</a>
 *       <ul class="dropdown-menu right">
 *         <li>...</li>
 *         ...
 *       </ul>
 *       <li>...</li>
 *     </li>
 *     ...
 *   </ul>
 * </li>
 * The First <a> is the same as before. The second <a> is the bit thats
 * clicked to reveal the submenu.
 *
 * If you want to only show certina tabs to certian user roles, do
 * a permission check around the <li> underneeth this. e.g.
 * if(user_is_retail()){
 *    <li class="dropdown"><?php $this->retailAcademyMenu(); ?></li>
 * }
 */
?>
                    <li><?php $this->myLearningMenu(); ?></li>

                    <?php if($this->showRetailTab()){ ?>
                    <li class="dropdown"><?php $this->retailAcademyMenu(); ?></li>
                    <?php } ?>

                    <?php if($this->showManagementTab()){ ?>
                    <li class="dropdown"><?php $this->managementAcademyMenu(); ?></li>
                    <?php } ?>

                    <li class="dropdown"><?php $this->howToMenu(); ?></li>

                    <?php if($this->is_administrator()){ ?>
                    <li><a href="<?php echo $CFG->wwwroot; ?>/course/" class="nav-text">All Courses</a></li>
                    <?php } ?>

                    <?php if($this->showManagementTab() || $this->isHeadTeamCoach()){ ?>
                    <li><a href="<?php echo $CFG->wwwroot; ?>/admin/user.php"  class="nav-text">Admin</a></li> <!-- admin tab shows to Managers -->
                    <?php } ?>

                    <?php if($this->showManagementTab() || $this->isTeamCoach() || $this->isHeadTeamCoach() ){ ?>
                    <li class="dropdown"><?php $this->reportsMenu(); ?></li>
                    <?php } ?>

                    <?php if($this->is_administrator()){ ?>
                    <li><?php $this->adminMenu(); ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

<?php }

    protected function myLearningMenu(){
        global $CFG; ?>

            <a href="<?php echo $CFG->wwwroot . '/my'; ?>" class="nav-text">My Learning</a>

<?php }

    protected function retailAcademyMenu(){
        global $CFG; ?>

            <!--
            
            Under the new heading of Salesfloor & Home the next level should be as follows: 
            Tills, Using Your Initiative, Service, Replen, Product Knowledge, VM, Home PC, Room Sets, Shelved Stock

            -->

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Retail Academy</a>
            <ul class="dropdown-menu">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Salesfloor</a>
                    <ul class="dropdown-menu right">
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=57" ?>">Tills</a></li> 
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=58" ?>">Using Your Initiative</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=7" ?>">Service</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=10" ?>">Replen</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=12" ?>">Product Knowledge</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=13" ?>">VM</a></li> 
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Salesfloor &amp; Home</a>
                    <ul class="dropdown-menu right">
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=57" ?>">Tills</a></li> 
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=58" ?>">Using Your Initiative</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=7" ?>">Service</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=10" ?>">Replen</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=12" ?>">Product Knowledge</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=13" ?>">VM</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=14" ?>">Home PC</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=15" ?>">Roomsets</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=16" ?>">Shelved Stock</a></li>
                        
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Delivery</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=17" ?>">Tubs/Sets</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=18" ?>">Home</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=19" ?>">3 Way Scan</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=20" ?>">Orders</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=21" ?>">Cash</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=22" ?>">Stock</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=23" ?>">Paperwork</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=24" ?>">People</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Additional Learning</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=28" ?>">Product Specialist</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=29" ?>">Tailoring Specialist</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=30" ?>">Lingerie Specialist</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=31" ?>">Style Advisor</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=32" ?>">Dressing Room Sets</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=33" ?>">Furniture Deliveries</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=34" ?>">Table Leader</a></li>
                        <li><a href="<?php echo $CFG->wwwroot."/course/view.php?id=36" ?>">Team Coach</a></li>
                    </ul>
                </li>
            </ul>

<?php }


    protected function managementAcademyMenu(){
        global $CFG; ?>

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Management Academy</a>
            <ul class="dropdown-menu">
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=44" ?>">Salesfloor Roles</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=45" ?>">Stockroom Roles</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=46" ?>">Office Roles</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=47" ?>">Security Roles</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=48" ?>">Senior Manager Roles</a>
                </li>
            </ul>

<?php }

    protected function howToMenu(){
        global $CFG; ?>

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">How To...</a>
            <ul class="dropdown-menu">
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=36" ?>">Salesfloor</a>
                </li>    
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=37" ?>">Home</a>
                </li>  
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=38" ?>">Delivery</a>
                </li> 
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=39" ?>">Admin</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=40" ?>">Additional Learning</a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot."/course/index.php?categoryid=43" ?>">Health and Safety</a>
                </li>
            </ul>

<?php }

    protected function reportsMenu(){
        global $CFG; ?>

        
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports</a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_reports/reportactivity-builder.php'; ?>">Learning Tracker</a></li>    
                <li><a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_reports/reportuser-builder.php'; ?>">Individual Learning Report</a></li>
                <li><a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_reports/reportacademyoverdue-builder.php'; ?>">Retail Academy Overdue Report</a></li>
                <li><a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_reports/reportcourse-builder.php'; ?>">Progress Report</a></li>
                <li><a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_reports/reportpayincrease-builder.php'; ?>">Pay Increase Overdue Report</a></li>
            </ul>

<?php }

    protected function adminMenu(){
        global $CFG; ?>

        
            <a href="<?php echo $CFG->wwwroot . '/admin/dynamic_admin/dynamic_enrolments'; ?>" class="nav-text">Group Admin</a>


<?php }
    protected function is_administrator(){
        global $CFG,$DB,$USER;
        $sql = "SELECT value FROM mdl_config WHERE name = 'siteadmins'";
        $data = $DB->get_field_sql($sql);
        $arr = explode(',',$data);
        $exists = array_search($USER->id,$arr);
        if($exists === FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    protected function showRetailTab(){
        global $CFG,$DB,$USER;
        if($this->is_administrator()){
            return TRUE;
        }
        $sql = "
            SELECT u.id 
            FROM mdl_user u
            LEFT JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
            WHERE u.id = ? 
            AND (ud.jobcode IN('SCON','APRN','VAND'))
        ";
        $data = $DB->get_record_sql($sql,array($USER->id));
        if ($data){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    protected function showManagementTab(){
        global $CFG,$DB,$USER; 
        if($this->is_administrator()){
            return TRUE;
        } 
        $sql = "
            SELECT u.id 
            FROM mdl_user u
            LEFT JOIN mdl_dynamic_userdata ud ON u.id = ud.userid
            WHERE u.id = ? 
            AND (ud.jobcode IN('MGRB','MGRA','MGRC','MGRC','MSAM','MGRS','MOPA','MOFF','MSTK','MPTM','MDEP','MNDM','MOWM','MPTO','MSEC','SECO','MCDE','MCOF','MCSA'))
        ";
        $data = $DB->get_record_sql($sql,array($USER->id));
        //var_dump($data);
        if ($data){
            return TRUE;
        }else{
            return FALSE;
        }

    }
    //Has user completed the team coach module
    protected function isTeamCoach(){
        global $CFG,$DB,$USER;
        $GroupManagerRoleID = array(99,100); //The id of the new group manager role
        $gids = implode(",", $GroupManagerRoleID);
        $sql = "
            SELECT roleid,userid FROM mdl_role_assignments 
            WHERE roleid IN ( " . $gids . ") AND userid = ? 

        ";
        $data = $DB->get_record_sql($sql,array($USER->id));
        
        if ($data){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    //Is the user a head teacm coach
    protected function isHeadTeamCoach(){
        global $CFG,$DB,$USER;
        $GroupManagerRoleID = array(101); //The id of the new group manager role
        $gids = implode(",", $GroupManagerRoleID);
        $sql = "
            SELECT roleid,userid FROM mdl_role_assignments 
            WHERE roleid IN ( " . $gids . ") AND userid = ? 

        ";
        $data = $DB->get_record_sql($sql,array($USER->id));
        
        if ($data){
            return TRUE;
        }else{
            return FALSE;
        }
    }


}