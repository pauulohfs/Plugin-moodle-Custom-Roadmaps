<?php
/**
 * Management page for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Paulo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

// Configura a página no contexto administrativo do Moodle.
admin_externalpage_setup('local_roadmaps_manage');

echo $OUTPUT->header();

// Botão para criar novo Roadmap.
echo $OUTPUT->single_button(new moodle_url('/local/roadmaps/edit.php'), get_string('create_roadmap', 'local_roadmaps'), 'get', ['class' => 'mb-3']);

// Definição da tabela.
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

// Busca os roadmaps cadastrados.
$roadmaps = $DB->get_records('local_roadmaps', null, 'name ASC');

foreach ($roadmaps as $roadmap) {
    $editurl = new moodle_url('/local/roadmaps/edit.php', ['id' => $roadmap->id]);
    $deleteurl = new moodle_url('/local/roadmaps/delete.php', ['id' => $roadmap->id, 'sesskey' => sesskey()]);
    
    // --- ATUALIZAÇÃO PARA URL AMIGÁVEL ---
    // Construímos a URL amigável como uma string direta.
    $viewurl = $CFG->wwwroot . '/local/roadmaps/' . $roadmap->slug;

    $actions = [];
    
    // Ícone de Visualização (Preview) apontando para a URL amigável.
    // O Moodle aceita a string $viewurl diretamente no action_icon.
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