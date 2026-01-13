<?php
/**
 * Page for creating and editing roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);

require_login();
$context = context_system::instance();
require_capability('local_roadmaps:manage', $context);

$url = new moodle_url('/local/roadmaps/edit.php', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$roadmap = null;
if ($id) {
    $roadmap = $DB->get_record('local_roadmaps', ['id' => $id], '*', MUST_EXIST);
    $PAGE->set_title(get_string('edit_roadmap', 'local_roadmaps', $roadmap->name));
} else {
    $PAGE->set_title(get_string('create_roadmap', 'local_roadmaps'));
}

$mform = new \local_roadmaps\form\edit_form($url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/roadmaps/manage.php'));
} else if ($data = $mform->get_data()) {
    $data->timemodified = time();
    if ($data->id) {
        $DB->update_record('local_roadmaps', $data);
    } else {
        $data->timecreated = $data->timemodified;
        $DB->insert_record('local_roadmaps', $data);
    }
    redirect(new moodle_url('/local/roadmaps/manage.php'), get_string('save_success', 'local_roadmaps'));
}

if ($roadmap) {
    $mform->set_data($roadmap);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($PAGE->title);
$mform->display();
echo $OUTPUT->footer();
