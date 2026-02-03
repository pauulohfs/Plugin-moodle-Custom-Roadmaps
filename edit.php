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
require_once(__DIR__ . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);

require_login();
$context = context_system::instance();
require_capability('local/roadmaps:manage', $context);

$url = new moodle_url('/local/roadmaps/edit.php', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

$roadmap = null;
$oldcourseid = 0;
$oldslug = '';

if ($id) {
    $roadmap = $DB->get_record('local_roadmaps', ['id' => $id], '*', MUST_EXIST);
    $oldcourseid = $roadmap->courseid;
    $oldslug = $roadmap->slug;
    $PAGE->set_title(get_string('edit_roadmap', 'local_roadmaps', format_string($roadmap->name)));
} else {
    $PAGE->set_title(get_string('create_roadmap', 'local_roadmaps'));
}

$mform = new \local_roadmaps\form\edit_form($url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/roadmaps/manage.php'));
} else if ($data = $mform->get_data()) {
    $data->timemodified = time();
    
    if ($data->id) {
        // --- LÓGICA DE LIMPEZA ---
        // Se mudou o curso OU mudou o slug, precisamos limpar o link antigo no curso antigo.
        if ($oldcourseid && ($oldcourseid != $data->courseid || $oldslug != $data->slug)) {
            local_roadmaps_remove_from_course($oldcourseid, $oldslug);
        }

        $DB->update_record('local_roadmaps', $data);
        $roadmapid = $data->id;
    } else {
        $data->timecreated = $data->timemodified;
        $roadmapid = $DB->insert_record('local_roadmaps', $data);
    }

    // Busca os dados atualizados para garantir que o link use o slug novo.
    $roadmap = $DB->get_record('local_roadmaps', ['id' => $roadmapid], '*', MUST_EXIST);

    // Handle course linking.
    if (!empty($roadmap->courseid)) {
        local_roadmaps_link_to_course($roadmap->courseid, $roadmap);
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