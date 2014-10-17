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
 * A  secure layout for the Bootstrapbase theme.
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
</head>

<body <?php echo $OUTPUT->body_attributes('two-column'); ?>>

    <?php echo $OUTPUT->standard_top_of_body_html() ?>

    <?php echo $OUTPUT->dynamic_header(); ?>

    <div id="page" class="container-fluid">

        <header id="page-header" class="clearfix">
            <div id="page-navbar" class="clearfix">
                <nav class="breadcrumb-nav navbar"><?php echo $OUTPUT->navbar(); ?></nav>
                <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
            </div>
        </header>

        <div id="page-content" class="row-fluid">
            <section id="region-main" class="<?php echo ($left) ? ' pull-right' : ' pull-left'; ?>">
                <div class="page-title-header clearfix">
                    <?php echo $OUTPUT->page_heading(); ?>
                    <div id="course-header">
                        <?php echo $OUTPUT->course_header(); ?>
                    </div>
                </div>
                <?php
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->main_content();
                echo $OUTPUT->course_content_footer();
                ?>
            </section>
            <?php
            $classextra = '';
            if ($left) {
                $classextra = ' desktop-first-column';
            }
            echo $OUTPUT->blocks('side-pre', (!$left) ? 'pull-right ' : 'pull-left ' . $classextra);
            ?>
        </div>

    </div>

    <footer id="page-footer">
        <?php $OUTPUT->dynamic_footer(); ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>