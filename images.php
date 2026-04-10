<?php
/**
 * Image management page for Custom Roadmaps.
 *
 * @package    local_roadmaps
 * @copyright  2026 Manus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/filelib.php');

require_login();
$context = context_system::instance();
require_capability('local/roadmaps:manage', $context);

$url = new moodle_url('/local/roadmaps/images.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('image_manager', 'local_roadmaps'));
$PAGE->set_heading(get_string('image_manager', 'local_roadmaps'));

// 1. Instanciar o formulário primeiro para poder usá-lo no processamento
$mform = new \local_roadmaps\form\image_upload_form($url);

// 2. Lógica de Deleção
$deleteid = optional_param('delete', 0, PARAM_INT);
if ($deleteid && confirm_sesskey()) {
    $fs = get_file_storage();
    $file = $fs->get_file_by_id($deleteid);
    if ($file && $file->get_component() === 'local_roadmaps' && $file->get_filearea() === 'roadmap_images') {
        $file->delete();
        redirect($url, get_string('image_delete_success', 'local_roadmaps'));
    }
}

// 3. Lógica de Upload (Usando o objeto $mform corretamente)
if ($data = $mform->get_data()) {
    $draftitemid = file_get_submitted_draft_itemid('imagefile');
    
    // Salva os arquivos da área de rascunho para a área permanente
    file_save_draft_area_files(
        $draftitemid, 
        $context->id, 
        'local_roadmaps', 
        'roadmap_images', 
        0, 
        ['subdirs' => 0, 'maxfiles' => -1]
    );
    
    redirect($url, get_string('image_upload_success', 'local_roadmaps'));
}

// 4. Preparar os dados para o formulário (Carregar o que já existe no seletor)
$draftitemid = file_get_submitted_draft_itemid('imagefile');
file_prepare_draft_area(
    $draftitemid, 
    $context->id, 
    'local_roadmaps', 
    'roadmap_images', 
    0, 
    ['subdirs' => 0]
);
$mform->set_data(['imagefile' => $draftitemid]);

// --- Início da Renderização HTML ---

echo $OUTPUT->header();

// Exibição do Formulário
echo $OUTPUT->heading(get_string('upload_image', 'local_roadmaps'), 3);
$mform->display();

// Listagem de Imagens
echo $OUTPUT->heading(get_string('images_list', 'local_roadmaps'), 3);

$fs = get_file_storage();
// O itemid é 0 pois é uma galeria global do plugin
$files = $fs->get_area_files($context->id, 'local_roadmaps', 'roadmap_images', 0, 'filename', false);

if (empty($files)) {
    echo $OUTPUT->notification(get_string('no_images', 'local_roadmaps'), 'info');
} else {
    echo '<div class="row">';
    foreach ($files as $file) {
        // Gera a URL para exibir a imagem através do pluginfile.php
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(), 
            $file->get_component(), 
            $file->get_filearea(), 
            $file->get_itemid(), 
            $file->get_filepath(), 
            $file->get_filename()
        );
        
        $imgtag = '<img src="' . $fileurl . '" alt="' . s($file->get_filename()) . '">';
        
        echo '<div class="col-md-4 mb-4">';
        echo '<div class="card h-100">';
        echo '<div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 150px;">';
        echo '<img src="' . $fileurl . '" class="img-fluid" style="max-height: 140px; object-fit: contain;">';
        echo '</div>';
        echo '<div class="card-body p-2">';
        echo '<p class="small font-weight-bold mb-1 text-truncate">' . s($file->get_filename()) . '</p>';
        
        // Input para copiar a URL
        echo '<div class="input-group input-group-sm mb-2">';
        echo '<input type="text" class="form-control" value="' . $fileurl . '" id="url-' . $file->get_id() . '" readonly>';
        echo '<div class="input-group-append">';
        echo '<button class="btn btn-outline-secondary" type="button" onclick="copyText(\'url-' . $file->get_id() . '\')">URL</button>';
        echo '</div></div>';
        
        // Input para copiar a Tag HTML
        echo '<div class="input-group input-group-sm mb-2">';
        echo '<input type="text" class="form-control" value=\'' . s($imgtag) . '\' id="tag-' . $file->get_id() . '" readonly>';
        echo '<div class="input-group-append">';
        echo '<button class="btn btn-outline-secondary" type="button" onclick="copyText(\'tag-' . $file->get_id() . '\')">HTML</button>';
        echo '</div></div>';
        
        // Botão de deletar
        $deleteurl = new moodle_url($url, ['delete' => $file->get_id(), 'sesskey' => sesskey()]);
        echo $OUTPUT->single_button($deleteurl, get_string('delete'), 'post', ['class' => 'btn-sm btn-danger w-100']);
        
        echo '</div></div></div>';
    }
    echo '</div>';
}

// Script de cópia
echo '<script>
function copyText(id) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        alert("Copiado com sucesso!");
    }, function(err) {
        console.error("Erro ao copiar: ", err);
    });
}
</script>';

echo $OUTPUT->footer();