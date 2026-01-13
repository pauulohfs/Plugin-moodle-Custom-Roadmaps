<?php
/**
 * View page for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$slug = required_param('path', PARAM_ALPHANUMEXT);

// Busca o roadmap no banco de dados.
$roadmap = $DB->get_record('local_roadmaps', ['slug' => $slug]);

if (!$roadmap) {
    print_error('roadmap_not_found', 'local_roadmaps');
}

// Validação de acesso.
$context = context_system::instance();

switch ($roadmap->accesslevel) {
    case 1: // Logado
    case 2: // Inscrito (simplificado)
        require_login();
        break;
    case 0: // Público
    default:
        break;
}

// Configurações da página.
$PAGE->set_url(new moodle_url('/local/roadmaps/view.php', ['path' => $slug]));
$PAGE->set_context($context);
$PAGE->set_title(format_string($roadmap->name));

// AJUSTE 1: Deixamos o heading vazio para não duplicar o título na tela.
$PAGE->set_heading(' '); 

// AJUSTE 2: Layout 'base' é bom, mas se quiser a tela TOTALMENTE limpa use 'embedded'.
$PAGE->set_pagelayout('base'); 

echo $OUTPUT->header();

// AJUSTE 3: CSS de segurança para esconder o cabeçalho caso o tema insista em mostrar.
echo '<style>#page-header, .page-context-header { display: none !important; }</style>';

// AJUSTE 4: Renderização do conteúdo.
// Usamos format_text com 'noclean' => true para que suas tags <script> e <style> NÃO sejam deletadas.
$options = [
    'noclean' => true,
    'allowid' => true,
    'filter' => true
];
echo format_text($roadmap->content, FORMAT_HTML, $options);

echo $OUTPUT->footer();