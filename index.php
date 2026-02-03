<?php
/**
 * Router for friendly URLs.
 * @package    local_roadmaps
 */

require_once(__DIR__ . '/../../config.php');

// O .htaccess joga o slug para cá através do parâmetro 'path'
$path = optional_param('path', '', PARAM_RAW);

if (empty($path)) {
    // Se tentarem acessar a pasta sem slug, manda para o gerenciador
    redirect(new moodle_url('/local/roadmaps/manage.php'));
}

// Busca no banco de dados
$roadmap = $DB->get_record('local_roadmaps', ['slug' => $path]);

if (!$roadmap) {
    // Se o slug não existir no banco, mostra erro amigável
    print_error('roadmap_not_found', 'local_roadmaps');
}

// Agora que achamos o $roadmap, chamamos o seu view.php antigo
// Ele vai herdar a variável $roadmap automaticamente.
require(__DIR__ . '/view.php');