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
require_once($CFG->dirroot . '/report/ildcoursestats/classes/report_ildcoursestats.php');

/**
 * Userstats report renderable class.
 *
 * @package    report_ildcoursestats
 */
class report_ildcoursestats_renderable implements renderable {
    /**
     * @var string Stores users activity events return from google charts.
     */
    public $activeUsers;
    /**
     * @var int Stores selected period.
     */
    public $selectedPeriod;
    /**
     * @var int Stores selected chart type.
     */
    public $selectedChartType;
    /**
     * @var int Stores course id.
     */
    public $courseID;

    public $context;

    public $forumEntries;

    public $badges;

    /**
     * report_ildcoursestats_renderable constructor.
     * @param $period
     */
    public function __construct($period, $courseid) {
        $this->selectedPeriod = $period;
        $this->courseID = $courseid;
    }

    /**
     * Displays period related graph charts.
     */
    public function ildcoursestats_get_gchart_data() {
        $graphreport = new report_ildcoursestats();
        $this->activeUsers = $graphreport->ildcoursestats_get_active_users_chart($this->selectedPeriod, $this->selectedChartType, $this->context, $this->courseID);
    }

    /**
     * Displays period related csv-export.
     */
    public function ildcoursestats_get_export_data() {
        $export_data = new report_ildcoursestats();
        $this->activeUsers = $export_data->ildcoursestats_get_active_users_export($this->selectedPeriod, $this->context, $this->courseID);
    }

    public function ildcoursestats_get_forum_entries() {
        $entries = new report_ildcoursestats();
        $this->forumEntries = $entries->ildcoursestats_forum_entries($this->courseID);
    }

    public function ildcoursestats_get_badges() {
        $badges = new report_ildcoursestats();
        $this->badges = $badges->ildcoursestats_get_badges_count($this->courseID);
    }

    /**
     * Setter chart type.
     *
     * @param $type
     */
    public function ildcoursestats_set_chart_type($type) {
        $this->selectedChartType = $type;
    }

    public function ildcoursestats_set_context($context) {
        $this->context = $context;
    }
}