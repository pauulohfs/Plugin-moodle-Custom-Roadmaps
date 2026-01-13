<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local_roadmaps:manage' => [ // <--- Use UNDERSCORE aqui
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
        'clonepermissionsfrom' => 'moodle/site:config'
    ],
];