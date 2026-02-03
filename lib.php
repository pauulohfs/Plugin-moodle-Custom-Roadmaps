<?php
/**
 * Library functions for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Paulo Henrique Freitas
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
        // Link para a página de gerenciamento no menu de plugins.
        $url = new moodle_url('/local/roadmaps/manage.php');
        $links[] = html_writer::link($url, get_string('manage_roadmaps', 'local_roadmaps'));
    }
    return $links;
}
/**
 * Remove qualquer link de roadmap do sumário de um curso.
 *
 * @param int $courseid
 * @param string $slug Opcional: se enviado, remove apenas o link desse slug específico.
 */

function local_roadmaps_remove_from_course($courseid, $slug = null) {
    global $DB;

    $course = $DB->get_record('course', ['id' => $courseid]);
    if (!$course) {
        return;
    }

    // Regex que identifica o nosso botão pelo padrão da URL.
    if ($slug) {
        $pattern = '/\n?<a href="[^"]*\/local\/roadmaps\/' . preg_quote($slug, '/') . '"[^>]*>.*?<\/a>/is';
    } else {
        $pattern = '/\n?<a href="[^"]*\/local\/roadmaps\/[^"]*"[^>]*>.*?<\/a>/is';
    }

    $newsummary = preg_replace($pattern, '', $course->summary);

    if ($newsummary !== $course->summary) {
        $course->summary = $newsummary;
        $course->timemodified = time();
        $DB->update_record('course', $course);
        rebuild_course_cache($courseid);
    }
}

/**
 * Automatically adds the roadmap button to the course description.
 *
 * @param int $courseid The ID of the course.
 * @param object $roadmap The roadmap record.
 * @return bool Success status.
 */
function local_roadmaps_link_to_course($courseid, $roadmap) {
    global $DB, $CFG;

    if (empty($courseid) || empty($roadmap->slug)) {
        return false;
    }

    try {
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    } catch (dml_exception $e) {
        return false;
    }
    
    // Geramos a URL amigável.
    $viewurl = $CFG->wwwroot . '/local/roadmaps/' . $roadmap->slug;
    
    // Criamos o HTML do botão. Note que removemos o ->out(), pois $viewurl é uma string.
    $buttonhtml = "\n" . '<a href="' . $viewurl . '" target="_blank" class="btn btn-outline-primary btn-sm mt-2">Ver Roadmap →</a>';

    // Verificação de duplicata atualizada para o formato da URL amigável.
    if (strpos($course->summary, '/local/roadmaps/' . $roadmap->slug) !== false) {
        return true;
    }

    // Adiciona o botão ao final do resumo do curso.
    $course->summary .= $buttonhtml;
    $course->summaryformat = FORMAT_HTML; // Garante que o resumo aceite HTML.
    $course->timemodified = time();

    $DB->update_record('course', $course);
    
    // Limpa o cache do curso para refletir a mudança.
    rebuild_course_cache($courseid);
    
    return true;
}