<?php
/**
 * Delete script for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

require_login();
$context = context_system::instance();
require_capability('local/roadmaps:manage', $context);
require_sesskey();

$roadmap = $DB->get_record('local_roadmaps', ['id' => $id], '*', MUST_EXIST);

// 1. Busca os dados do roadmap antes de apagar


// 2. Se ele estiver vinculado a um curso, remove o link de lá
if (!empty($roadmap->courseid)) {
    local_roadmaps_remove_from_course($roadmap->courseid, $roadmap->slug);
}

// 3. Agora sim, deleta do banco
$DB->delete_records('local_roadmaps', ['id' => $id]);


redirect(new moodle_url('/local/roadmaps/manage.php'), get_string('delete_success', 'local_roadmaps'));
