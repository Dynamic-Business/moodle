<?php

/**
 * Configuration for Moodle's bootstrap theme.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package    theme_next_responsive
 * @copyright  2014 Dynamic Business, www.dynamicbusiness.co.uk
 * @author     Dynamic Business
 * @author     Dale Whiteley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->name = 'dynamicdemo_responsive';

/////////////////////////////////
// The only thing you need to change in this file when copying it to
// create a new theme is the name above. You also need to change the name
// in version.php and lang/en/theme_clean.php as well.
//////////////////////////////////
//
$THEME->doctype = 'html5';
$THEME->parents = array('dynamicbase');
$THEME->sheets = array('style');
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();
$THEME->enable_dock = false;
$THEME->editor_sheets = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->blockrtlmanipulations = array(
    'side-pre' => 'side-post',
    'side-post' => 'side-pre'
);

/**
 * $THEME->layouts = array(
 *      Use this if you want to use a custom layout (e.g. add some
 *      PHP to popup.php to detect something etc..) Make a copy,
 *      make changes then reference here e.g
 * 
 *      'popup' => array(
 *          'file' => 'popup-modified.php',
 *          'regions' => array(),
 *          'options' => array('nofooter' => true, 'nonavbar' => true),
 *      ),
 *
 *   );
 */