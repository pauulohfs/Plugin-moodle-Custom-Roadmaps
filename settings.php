<?php
/**
 * Admin settings for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// O Moodle precisa saber se o usuário tem permissão para ver o menu.
$capability = 'local/roadmaps:manage';

if ($hassiteconfig || has_capability($capability, context_system::instance())) {

    // 1. Criamos a URL que aponta para o gerenciador.
    $manageurl = new moodle_url('/local/roadmaps/manage.php');

    // 2. Adicionamos uma "admin_externalpage" DIRETAMENTE ao nó 'localplugins'.
    // Isso faz com que, ao clicar no nome do plugin em "Plugins Locais",
    // o Moodle abra o manage.php imediatamente, sem páginas intermediárias.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_roadmaps_manage',                       // ID único (deve ser o mesmo no setup do manage.php)
        get_string('pluginname', 'local_roadmaps'),    // Nome que aparece no menu
        $manageurl,                                    // Destino direto
        $capability                                    // Permissão
    ));
}