<?php
/**
 * Form for creating and editing roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_roadmaps\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class edit_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        // ID (hidden)
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Title
        $mform->addElement('text', 'name', get_string('roadmap_name', 'local_roadmaps'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Slug
        $mform->addElement('text', 'slug', get_string('roadmap_slug', 'local_roadmaps'), ['size' => '64']);
        $mform->setType('slug', PARAM_ALPHANUMEXT);
        $mform->addRule('slug', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('slug', 'roadmap_slug', 'local_roadmaps');

        // Access Level
        $options = [
            0 => get_string('access_public', 'local_roadmaps'),
            1 => get_string('access_logged_in', 'local_roadmaps'),
            2 => get_string('access_enrolled', 'local_roadmaps'),
        ];
        $mform->addElement('select', 'accesslevel', get_string('access_level', 'local_roadmaps'), $options);
        $mform->setType('accesslevel', PARAM_INT);
        $mform->setDefault('accesslevel', 0);

        // Content (Code Editor)
        $mform->addElement('textarea', 'content', get_string('roadmap_content', 'local_roadmaps'), ['rows' => 20, 'cols' => 80, 'style' => 'font-family: monospace;']);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('required'), 'required', null, 'client');

        // Buttons
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        // Check if slug is unique
        $sql = "slug = :slug";
        $params = ['slug' => $data['slug']];
        if (!empty($data['id'])) {
            $sql .= " AND id <> :id";
            $params['id'] = $data['id'];
        }

        if ($DB->record_exists_select('local_roadmaps', $sql, $params)) {
            $errors['slug'] = get_string('slug_exists', 'local_roadmaps');
        }

        return $errors;
    }
}
