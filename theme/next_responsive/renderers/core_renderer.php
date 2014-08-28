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
                    <div class="container-fluid">
                        <a class="brand" href="<?php echo $CFG->wwwroot;?>">
                            <img src="<?php echo $OUTPUT->pix_url('Site-logo','theme'); ?>" width="114" height="34" alt="" />
                        </a>
                    </div>
                </div>
         <?php  if($displayNav){
                
                    $this->navHTML();

                } ?>
            </nav>
        </header>

<?php }

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
                    <li class="dropdown"><?php $this->retailAcademyMenu(); ?></li>
                    <li class="dropdown"><?php $this->managementAcademyMenu(); ?></li>
                    <li class="dropdown"><?php $this->howToMenu(); ?></li>
                    <li><a href="<?php echo $CFG->wwwroot; ?>/course/" class="nav-text">All Courses</a></li>
                    <li class="dropdown"><?php $this->reportsMenu(); ?></li>
                    <li><?php $this->adminMenu(); ?></li>
                </ul>
            </div>
        </div>

<?php }

    protected function myLearningMenu(){
        global $CFG; ?>

            <a href="#" class="nav-text">My Learning</a>

<?php }

    protected function retailAcademyMenu(){
        global $CFG; ?>

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Retail Academy</a>
            <ul class="dropdown-menu">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Home</a>
                    <ul class="dropdown-menu right">
                        <li><a href="#">Home PC</a></li>    
                        <li><a href="#">Roomsets</a></li>
                        <li><a href="#">Shelved Stock</a></li>
                        <li><a href="#">Product Knowledge</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Salesfloor</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="#">Service</a></li>
                        <li><a href="#">Replenishment</a></li>
                        <li><a href="#">Product Knowledge</a></li>
                        <li><a href="#">Visual Merchandising</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Delivery</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="#">Tubs / Sets</a></li>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">3 Way Scan</a></li>
                        <li><a href="#">Orders</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="#">Cash</a></li>
                        <li><a href="#">Stock</a></li>
                        <li><a href="#">Paperwork</a></li>
                        <li><a href="#">People</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Additional Learning</a>
                    <ul class="dropdown-menu right">  
                        <li><a href="#">Floor Moves</a></li>
                        <li><a href="#">Product Specialist</a></li>
                        <li><a href="#">Lingerie Specialist</a></li>
                        <li><a href="#">Style Advisor</a></li>
                        <li><a href="#">Team Coach</a></li>
                        <li><a href="#">Dressing Roomsets</a></li>
                        <li><a href="#">Home Design Consultant</a></li>
                        <li><a href="#">Table Leader</a></li>
                    </ul>
                </li>
            </ul>

<?php }


    protected function managementAcademyMenu(){
        global $CFG; ?>

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Management Academy</a>
            <ul class="dropdown-menu">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Co-ordinator</a>
                    <ul class="dropdown-menu right">
                        <li><a href="#">Salesfloor</a></li>    
                        <li><a href="#">Delivery</a></li>
                        <li><a href="#">Office</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Sales Manager</a>
                </li>
                <li>
                    <a href="#">Stockroom Manager</a>
                </li>
                <li>
                    <a href="#">Office Manager</a>
                </li>
                <li>
                    <a href="#">Deputy Manager</a>
                </li>
                <li>
                    <a href="#">Commercial Manager</a>
                </li>
                <li>
                    <a href="#">Ops Manager</a>
                </li>
                <li>
                    <a href="#">Store Manager</a>
                </li>
            </ul>

<?php }

    protected function howToMenu(){
        global $CFG; ?>

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">How To..</a>
            <ul class="dropdown-menu">
                <li><a href="#">Sales Floor</a></li>    
                <li><a href="#">Home</a></li>
                <li><a href="#">Delivery</a></li>
                <li><a href="#">Admin</a></li>
            </ul>

<?php }

    protected function reportsMenu(){
        global $CFG; ?>

        
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports</a>
            <ul class="dropdown-menu">
                <li><a href="#">Learning Tracker</a></li>    
                <li><a href="#">Individual Learning Report</a></li>
                <li><a href="#">Exception Report</a></li>
                <li><a href="#">Overview Report</a></li>
            </ul>

<?php }

    protected function adminMenu(){
        global $CFG; ?>

        
            <a href="#" class="nav-text">Admin</a>

<?php }

}