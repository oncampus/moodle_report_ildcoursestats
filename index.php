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
 * @package    report_ildcoursestats
 * @copyright  2017 Fachhochschule Lübeck ILD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$period = optional_param('period', 0, PARAM_INT);
$chart_type = optional_param('chart', 0, PARAM_INT);
$export = optional_param('export', 0, PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $id));

if (!$course) {
    print_error('invalidcourseid');
}

$context = context_course::instance($course->id);
require_capability('report/ildcoursestats:view', $context);

$PAGE->set_context($context);
$PAGE->set_url('/report/ildcoursestats/index.php');
$PAGE->set_title(get_string('pluginname', 'report_ildcoursestats'));
$PAGE->set_heading(get_string('pluginname', 'report_ildcoursestats'));
$PAGE->set_pagelayout('report');

$renderable = new report_ildcoursestats_renderable($period, $id);
$renderer = $PAGE->get_renderer('report_ildcoursestats');

/**
 * CSV-Export. Exit if completed
 */
if (!empty($period) && !empty($export)) {
    $renderable->ildcoursestats_set_context($context);
    echo $renderer->render($renderable);
    echo $renderer->report_ildcoursestats_generate_export();
}

/**
 * Render Page.
 */
echo $OUTPUT->header();
$renderable->ildcoursestats_set_chart_type($chart_type);
$renderable->ildcoursestats_set_context($context);
echo $renderer->render($renderable);

if (!empty($period) && !empty($chart_type)) {
    echo $renderer->report_ildcoursestats_generate_chart();
}

echo $renderer->report_ildcoursestats_forum_entries();

echo $renderer->report_ildcoursestats_badges();

echo $OUTPUT->footer();