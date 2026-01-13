<?php
/**
 * Admin settings for Custom Roadmaps.
 */

defined('MOODLE_INTERNAL') || die();

// O ID da permissão deve ser exatamente o que está no seu db/access.php
$capability = 'local_roadmaps:manage';

if ($hassiteconfig || has_capability($capability, context_system::instance())) {

    // 1. Criamos a URL do seu gerenciador.
    $manageurl = new moodle_url('/local/roadmaps/manage.php');

    // 2. Adicionamos uma página externa DIRETAMENTE ao nó de plugins locais.
    // Usamos 'local_roadmaps' como ID para que o Moodle saiba que pertence a este plugin.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_roadmaps_manage',                       // ID único da página
        get_string('pluginname', 'local_roadmaps'),    // Nome que aparece: "Custom Roadmaps"
        $manageurl,                                    // O link para o manage.php
        $capability                                    // A permissão necessária
    ));
}