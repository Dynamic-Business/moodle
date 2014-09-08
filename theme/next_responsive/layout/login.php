<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A two column layout for the Bootstrapbase theme.
 *
 * @package   theme_bootstrapbase
 * @copyright 2012 Bas Brands, www.basbrands.nl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$left = (!right_to_left());  // To know if to add 'pull-right' and 'desktop-first-column' classes in the layout for LTR.
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!isloggedin()){ ?>
        
        <SCRIPT LANGUAGE=JavaScript>
            <!--

            /*
            // ///////////////////////////
            // checkCapsLock v1.0
            // 
            // If a user enters his password in a Web-based form with Caps Lock 
            // accidentally on, he can become frustrated because his password is not 
            // being accepted... and he may assume the application is the source of the 
            // problem.
            // 
            // This JavaScript function will let the user know his Caps Lock is on and 
            // about the potential for error.
            // ///////////////////////////
            */

            function checkCapsLock( e ) {
                //console.log(e);
                var myKeyCode=0;
                var myShiftKey=false;
                var myMsg='Caps Lock is On.\n\nTo prevent entering your password incorrectly,\nyou should press Caps Lock to turn it off.';

                // Internet Explorer 4+
                if ( document.all ) {
                    myKeyCode=e.keyCode;
                    myShiftKey=e.shiftKey;

                // Netscape 4
                } else if ( document.layers ) {
                    myKeyCode=e.which;
                    myShiftKey=( myKeyCode == 16 ) ? true : false;

                // Netscape 6
                } else if ( document.getElementById ) {
                    if (!window.chrome){
                        myKeyCode=e.which;
                        myShiftKey=( myKeyCode == 16 ) ? true : false;
                    }

                }
                //console.log(myShiftKey);
                // Upper case letters are seen without depressing the Shift key, therefore Caps Lock is on
                if ( ( myKeyCode >= 65 && myKeyCode <= 90 ) && !myShiftKey ) {
                    alert( myMsg );

                // Lower case letters are seen while depressing the Shift key, therefore Caps Lock is on
                } else if ( ( myKeyCode >= 97 && myKeyCode <= 122 ) && myShiftKey ) {
                    alert( myMsg );
                }
            }
            // -->
            </SCRIPT>   
        
        <?php }?>
</head>

<body <?php echo $OUTPUT->body_attributes('two-column'); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php echo $OUTPUT->dynamic_header(false); ?>

<div id="page" class="container-fluid">

    <section id="login-main" class="row-fluid">
        <div class="login-form-box"> 
            <?php echo $OUTPUT->main_content(); ?>
        </div>
        <div class="extra-info-box">
            <div class="login-info-wrap">
                <h1><img src="<?php echo $OUTPUT->pix_url('Site-logo','theme'); ?>" alt="next"><br/>learning<br/><span>portal</span></h1>
            </div>
        </div>
        <div class="final-info-box">
            <h2>Is this your first time here?</h2>
            <p>Log onto the site using your payroll number.</p>
        </div>
    </section>

</div>

<footer id="page-footer">
    <div class="container-fluid">
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>
</footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>
