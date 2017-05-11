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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/report/ildcoursestats/lib/gcharts.php');

class report_ildcoursestats extends Gcharts {
    /**
     * Generate Google Chart
     *
     * @param $period
     * @param $chart_type
     * @return mixed Google Chart
     */
    public function ildcoursestats_get_active_users_chart($period, $chart_type, $context, $courseid) {
        $this->set_graphic_type($chart_type);
        $data = $this->ildcoursestats_get_active_users_data($period, $context, $courseid);

        if (count($data) > 1) {
            return $this->generate($data);
        } else {
            return 'Keine Daten';
        }
    }

    /**
     * Create CSV-File
     *
     * @param $period
     */
    public function ildcoursestats_get_active_users_export($period, $context, $courseid) {
        $filename = "coursestats.csv";

        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename={$filename}");

        $data = $this->ildcoursestats_get_active_users_data($period, $context, $courseid);

        $fp = fopen('php://output', 'w');
        ob_end_clean();

        foreach ($data as $field) {
            fputcsv($fp, $field, ';');
        }

        fclose($fp);
        exit;
    }

    /**
     * Get data from database
     *
     * @param $period
     * @return array
     */
    private function ildcoursestats_get_active_users_data($period, $context, $courseid) {
        global $DB;

        setlocale(LC_TIME, 'de_DE.UTF8');

        $users = get_enrolled_users($context);

        $records = array();

        foreach ($users as $user) {
            $enrol = $DB->get_record_sql('SELECT ue.timecreated FROM {user_enrolments} AS ue LEFT JOIN {enrol} AS e ON ue.enrolid = e.id WHERE ue.userid = ? AND e.courseid = ? AND e.enrol ="autoenrol"', array($user->id, $courseid));

            if (!empty($enrol)) {
                array_push($records, $enrol->timecreated);
            }
        }

        asort($records);
        $records_data = $data = array();

        switch ($period) {
            case 1:
                $date_format = '%d.%m.%Y';
                $heading = array(get_string('day', 'report_ildcoursestats'), get_string('member', 'report_ildcoursestats'));
                break;
            case 2:
                $date_format = '%W';
                $heading = array(get_string('kw', 'report_ildcoursestats'), get_string('member', 'report_ildcoursestats'));
                break;
            case 3:
                $date_format = '%b %y';
                $heading = array(get_string('month', 'report_ildcoursestats'), get_string('member', 'report_ildcoursestats'));
        }

        foreach ($records as $record) {
            if ($record != 0) {
                $date = strftime($date_format, $record);

                if ($period == 2) {
                    $year = strftime('%y', $record);
                    $date .= '. KW ' . $year;
                }

                if (!array_key_exists($date, $records_data)) {
                    $records_data[$date] = 1;
                } else {
                    $records_data[$date] += 1;
                }
            }
        }

        array_push($data, $heading);

        $sum_up = 0;
        foreach ($records_data as $key => $value) {
            $sum_up += $value;
            array_push($data, array($key, $sum_up));
        }

        return $data;
    }

    public function ildcoursestats_forum_entries($course_id) {
        global $DB;
        $foren = $DB->get_records('forum', array('course' => $course_id));
        $all_posts = 0;
        $output = '<hr><p><strong>Forenbeiträge</strong></p>';

        foreach ($foren as $forum) {
            if ($forum->type != 'news') {
                $discussions = $DB->get_records('forum_discussions', array('course' => $course_id, 'forum' => $forum->id));
                $posts_counter = 0;

                foreach ($discussions as $discussion) {
                    $posts = $DB->get_records('forum_posts', array('discussion' => $discussion->id));
                    $posts_counter += count($posts);
                }

                $all_posts += $posts_counter;

                $output .= '<i>' . $forum->name . '</i> - Anzahl Posts: ' . $posts_counter . '</br>';
            }
        }

        $output .= '<i>Gesamtbeiträge:</i> ' . $all_posts . '<hr>';

        return $output;
    }

    public function ildcoursestats_get_badges_count($course_id) {
        global $DB;
        $badges = $DB->get_records('badge', array('courseid' => $course_id));
        $all_badges = 0;
        $output = '<p><strong>Badges</strong></p>';

        foreach ($badges as $badge) {
            $issued = $DB->get_records('badge_issued', array('badgeid' => $badge->id));
            $count_issued = count($issued);

            $output .= '<i>' . $badge->name . '</i> - Anzahl: ' . $count_issued . '</br>';

            $all_badges += $count_issued;
        }

        $output .= '<i>Gesamt:</i> ' . $all_badges . '<hr></br></br>';

        return $output;
    }
}