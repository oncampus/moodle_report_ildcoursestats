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

defined('MOODLE_INTERNAL') || die;

/**
 * Userstats report renderer class.
 *
 * @package    report_ildcoursestats
 */
class report_ildcoursestats_renderer extends plugin_renderer_base {
    protected $renderable;

    /**
     * Renderer constructor.
     *
     * @param report_ildcoursestats_renderable $renderable ildcoursestats report renderable instance.
     */
    protected function render_report_ildcoursestats(report_ildcoursestats_renderable $renderable) {
        $this->renderable = $renderable;
        $this->report_ildcoursestats_selector_form();
    }

    /**
     * This function is used to generate and display period filter.
     */
    public function report_ildcoursestats_selector_form() {
        $renderable = $this->renderable;
        $selectedPeriod = $renderable->selectedPeriod;
        $selectedChartType = $renderable->selectedChartType;
        $courseID = $renderable->courseID;

        $periods = array(
            0 => get_string('period', 'report_ildcoursestats'),
            1 => get_string('day', 'report_ildcoursestats'),
            2 => get_string('kw', 'report_ildcoursestats'),
            3 => get_string('month', 'report_ildcoursestats'));

        $chart_types = array(
            0 => get_string('chart-type', 'report_ildcoursestats'),
            1 => 'ColumnChart',
            2 => 'LineChart',
            3 => 'PieChart',
            4 => 'AreaChart',
            5 => 'ScatterChart',
            6 => 'BarChart');

        echo html_writer::start_tag('form', array('class' => 'userstats-form', 'action' => 'index.php', 'method' => 'post'));
        echo html_writer::start_div();
        echo html_writer::tag('p', get_string('member-active', 'report_ildcoursestats'));
        echo html_writer::select($periods, 'period', $selectedPeriod, false);
        echo html_writer::select($chart_types, 'chart', $selectedChartType, false);
        echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('show', 'report_ildcoursestats')));
        echo html_writer::empty_tag('input', array('name' => 'export', 'type' => 'submit', 'value' => get_string('export', 'report_ildcoursestats')));
        echo html_writer::empty_tag('input', array('name' => 'id', 'type' => 'hidden', 'value' => $courseID));
        echo html_writer::end_div();
        echo html_writer::end_tag('form');
    }

    /**
     * Generate chart
     */
    public function report_ildcoursestats_generate_chart() {
        $renderable = $this->renderable;
        $renderable->ildcoursestats_get_gchart_data();

        echo $renderable->activeUsers;
    }

    /**
     * Generate export
     */
    public function report_ildcoursestats_generate_export() {
        $renderable = $this->renderable;
        $renderable->ildcoursestats_get_export_data();

        echo $renderable->activeUsers;
    }

    public function report_ildcoursestats_forum_entries() {
        $renderable = $this->renderable;
        $renderable->ildcoursestats_get_forum_entries();

        echo $renderable->forumEntries;
    }

    public function report_ildcoursestats_badges() {
        $renderable = $this->renderable;
        $renderable->ildcoursestats_get_badges();

        echo $renderable->badges;
    }
}