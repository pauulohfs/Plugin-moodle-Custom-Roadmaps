<?php
/**
 * Management page for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('local_roadmaps_manage');

// Ensure user is logged in and has the capability.
require_login();
$context = context_system::instance();
require_capability('local_roadmaps:manage', $context);

// Set up the page.
$url = new moodle_url('/local/roadmaps/manage.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('manage_roadmaps', 'local_roadmaps'));
$PAGE->set_heading(get_string('manage_roadmaps', 'local_roadmaps'));

echo $OUTPUT->header();

// Create button.
echo $OUTPUT->single_button(new moodle_url('/local/roadmaps/edit.php'), get_string('create_roadmap', 'local_roadmaps'), 'get', ['class' => 'mb-3']);

// Define the table.
$table = new flexible_table('local_roadmaps_list');
$table->define_columns(['name', 'slug', 'accesslevel', 'actions']);
$table->define_headers([
    get_string('roadmap_name', 'local_roadmaps'),
    get_string('roadmap_slug', 'local_roadmaps'),
    get_string('access_level', 'local_roadmaps'),
    get_string('actions', 'local_roadmaps')
]);

$table->set_attribute('class', 'admintable generaltable');
$table->setup();

// Fetch roadmaps.
$roadmaps = $DB->get_records('local_roadmaps', null, 'name ASC');

foreach ($roadmaps as $roadmap) {
    $editurl = new moodle_url('/local/roadmaps/edit.php', ['id' => $roadmap->id]);
    $deleteurl = new moodle_url('/local/roadmaps/delete.php', ['id' => $roadmap->id, 'sesskey' => sesskey()]);
    $viewurl = new moodle_url('/local/roadmaps/' . $roadmap->slug);

    $actions = [];
    $actions[] = $OUTPUT->action_icon($viewurl, new pix_icon('t/preview', get_string('view_roadmap', 'local_roadmaps')));
    $actions[] = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
    $actions[] = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), 
        new confirm_action(get_string('confirm_delete', 'local_roadmaps', format_string($roadmap->name))));

    $access_strings = [
        0 => get_string('access_public', 'local_roadmaps'),
        1 => get_string('access_logged_in', 'local_roadmaps'),
        2 => get_string('access_enrolled', 'local_roadmaps'),
    ];

    $table->add_data([
        format_string($roadmap->name),
        s($roadmap->slug),
        $access_strings[$roadmap->accesslevel],
        implode(' ', $actions)
    ]);
}

$table->finish_output();

echo $OUTPUT->footer();
