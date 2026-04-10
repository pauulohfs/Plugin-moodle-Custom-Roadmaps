<?php
/**
 * Form for uploading images.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_roadmaps\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class image_upload_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('upload_image', 'local_roadmaps'));

        // File manager.
        $mform->addElement('filemanager', 'imagefile', get_string('upload_image', 'local_roadmaps'), null, 
            ['subdirs' => 0, 'maxbytes' => 1024 * 1024 * 5, 'maxfiles' => -1, 'accepted_types' => ['image']]);
        
        // Buttons.
        $this->add_action_buttons(false, get_string('upload_image', 'local_roadmaps'));
    }
}
