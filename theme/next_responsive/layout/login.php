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
        
        <script type="text/javascript">
                var existing = window.onload;
                window.onload = function()
                { 
                    if(typeof(existing) == "function")
                    {
                          existing();
                    }
                    loadCapsChecker();
                }
                  
                function loadCapsChecker()
                {   
                    capsClass = "capLocksCheck";
                    capsNotice = "capsLockNotice";
                    
                    var inputs = document.getElementsByTagName('INPUT');
                    var elements = new Array();
                    for(var i=0; i<inputs.length; i++)
                    {
                        if(inputs[i].className.indexOf(capsClass) != -1)
                        {
                            elements[elements.length] = inputs[i];
                        }
                    }   
                    for(var i=0; i<elements.length; i++)
                    {
                        if(document.addEventListener)
                        {
                            elements[i].addEventListener("keypress",checkCaps,"false");
                        }
                        else
                        {
                            elements[i].attachEvent("onkeypress",checkCaps);
                        }
                    }   
                }
                
                function checkCaps(e)
                {
                    var pushed = (e.charCode) ? e.charCode : e.keyCode;
                    var shifted = false;        
                    if(e.shiftKey)
                    {
                        shifted = e.shiftKey;
                    }
                    else if (e.modifiers)
                    {
                        shifted = !!(e.modifiers & 4);
                    }           
                    var upper = (pushed >= 65 && pushed <= 90);
                    var lower = (pushed >= 97 && pushed <= 122);
                    if((upper && !shifted) || (lower && shifted))
                    {
                        if(document.getElementById(capsNotice))
                        {
                            //document.getElementById(capsNotice).style.display = 'block';
                            alert("Caps Lock is turned on");
                        }
                        else
                        {
                            alert("Caps Lock is turned on");
                        }
                    }
                    else if((lower && !shifted) || (upper && shifted))
                    {
                        if(document.getElementById(capsNotice))
                        {
                            //document.getElementById(capsNotice).style.display = 'none';
                        }
                    }
                }
            </script>  
        
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
        echo '<span style="font-size: xx-small;"> Server: ' . gethostname() . '</span>';
        ?>
    </div>
</footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>
