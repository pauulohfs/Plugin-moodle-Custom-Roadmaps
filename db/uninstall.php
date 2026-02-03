<?php
/**
 * Custom cleanup during plugin uninstallation.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom uninstall function.
 */
function xmldb_local_roadmaps_uninstall() {
    global $DB;

    // 1. Buscar todos os roadmaps que possuem vínculo com curso.
    $roadmaps = $DB->get_records_select('local_roadmaps', "courseid IS NOT NULL AND courseid > 0");

    if ($roadmaps) {
        // Precisamos do arquivo lib para usar a função de remover.
        require_once(__DIR__ . '/../lib.php');

        foreach ($roadmaps as $roadmap) {
            // Remove o botão do resumo do curso.
            local_roadmaps_remove_from_course($roadmap->courseid, $roadmap->slug);
        }
    }

    return true;
}