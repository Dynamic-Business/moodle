<?php

/**
 * Renderers to override dynamicbase. this can be used to customise the
 * HTML of the header and navigation. You might just override functions
 * that are in themes/dynamicbase/renderers/core_renderer.php
 *
 * Renderers can be found all thoughout moodle and can be used to customise
 * that part of the site. For more info look here:
 *
 * 		http://
 *
 * or speak to Dale :)
 *
 * @package    theme_blank_responsive
 * @copyright  2014
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_dynamicdemo_responsive_core_renderer extends theme_dynamicbase_core_renderer {
    protected $debugging = true;

    public function dynamic_header($displayNav = true){
        global $CFG, $SITE; ?>

        <header role="banner" class="navbar moodle-has-zindex"><div class="container-fluid header">
                    <div class="pull-right">
                        <?php echo $this->login_info(); ?>
                    </div>
                    <a class="brand" href="<?php echo $CFG->wwwroot;?>"><h1><?php echo $SITE->shortname; ?></h1></a>
                </div>
            <nav role="navigation" class="navbar-inner-dyn">
                
         <?php  if($displayNav){
                
                    $this->navHTML();

                } ?>
            </nav>
        </header>

<?php }

    public function navHTML(){
        global $CFG; ?>
        
        <a class="menu-btn" data-toggle="collapse" data-target=".nav-collapse">
            Menu
        </a>
        <div class="nav-wrap">
            <ul class="nav main-nav">
                <li><a href="<?php echo $CFG->wwwroot; ?>">Home</a></li>
                <li><a href="<?php echo $CFG->wwwroot; ?>/course/">All Courses</a></li>
                <li><a href="<?php echo $CFG->wwwroot; ?>/my/">My Courses</a></li>
            </ul>
        <?php if($this->is_administrator() || $this->is_groupmanager()){ ?>
            <ul class="nav dynamic-reports">
                <li><a href="<?php echo $CFG->wwwroot; ?>/admin/dynamic_admin/dynamic_enrolments">Group Administration</a></li>
                <li><a href="<?php echo $CFG->wwwroot; ?>/admin/dynamic_admin/dynamic_reports">Group Reporting</a></li>
            </ul>
        <?php } ?>
        </div>

<?php }

}