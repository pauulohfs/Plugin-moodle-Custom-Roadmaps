<?php
/**
 * Library functions for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Adds a settings link to the plugins list page.
 *
 * @param array $links Existing links
 * @param string $plugin Plugin name
 * @return array Modified links
 */
function local_roadmaps_plugin_action_links($links, $plugin) {
    if ($plugin === 'local_roadmaps') {
        $url = new moodle_url('/local/roadmaps/manage.php');
        $links[] = html_writer::link($url, get_string('manage_roadmaps', 'local_roadmaps'));
    }
    return $links;
}
