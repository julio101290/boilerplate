<?php

return [
    'global' => [
        'save' => 'Konservi',
        'close' => 'Fermi',
        'action' => 'Ago',
        'logout' => 'Elsaluti',
        'search' => 'Serĉi',
        'sweet' => [
            'title' => 'Ĉu vi certas?',
            'text' => 'Tio ne povas esti malfarita!',
            'confirm_delete' => 'Jes, forigi!',
        ],
    ],
    /**
     * Permission.
     */
    'permission' => [
        'add' => 'Aldoni permeson',
        'edit' => 'Redakti permeson',
        'title' => 'Permesa administrado',
        'subtitle' => 'Listo de permesoj',
        'fields' => [
            'name' => 'Permeso',
            'description' => 'Priskribo',
            'plc_name' => 'Nomo de la permeso',
            'plc_description' => 'Priskribo de la permeso',
        ],
        'msg' => [
            'msg_insert' => 'La permeso estis sukcese aldonita.',
            'msg_update' => 'La permeso kun identigilo {0} estis sukcese ĝisdatigita.',
            'msg_delete' => 'La permeso kun identigilo {0} estis sukcese forigita.',
            'msg_get' => 'La permeso kun identigilo {0} estis sukcese akirita.',
            'msg_get_fail' => 'La permeso kun identigilo {0} ne estis trovita aŭ jam estis forigita.',
        ],
    ],
    /**
     * Role.
     */
    'role' => [
        'add' => 'Aldoni rolon',
        'edit' => 'Redakti rolon',
        'title' => 'Rola administrado',
        'subtitle' => 'Listo de roloj',
        'fields' => [
            'name' => 'Rolo',
            'description' => 'Priskribo',
            'plc_name' => 'Nomo de la rolo',
            'plc_description' => 'Priskribo de la rolo',
        ],
        'msg' => [
            'msg_insert' => 'La rolo estis sukcese aldonita.',
            'msg_update' => 'La rolo kun identigilo {0} estis sukcese ĝisdatigita.',
            'msg_delete' => 'La rolo kun identigilo {0} estis sukcese forigita.',
            'msg_get' => 'La rolo kun identigilo {0} estis sukcese akirita.',
            'msg_get_fail' => 'La rolo kun identigilo {0} ne estis trovita aŭ jam estis forigita.',
        ],
    ],
    /**
     * Menu.
     */
    'menu' => [
        'expand' => 'Etendi',
        'collapse' => 'Maletendi',
        'refresh' => 'Refreŝigi',
        'add' => 'Aldoni menuon',
        'edit' => 'Redakti menuon',
        'title' => 'Menua administrado',
        'subtitle' => 'Listo de menuoj',
        'fields' => [
            'parent' => 'Patro',
            'warning_parent' => 'Averto! La menuo subtenas nur maksimuman profundon de 2.',
            'active' => 'Aktiva',
            'non_active' => 'Neaktiva',
            'icon' => 'Piktogramo',
            'info_icon' => 'Por pliaj piktogramoj, bonvolu vidi',
            'place_icon' => 'Font Awesome piktogramo.',
            'name' => 'Titolo',
            'place_title' => 'Nomo de la menuo.',
            'route' => 'Itinero',
            'place_route' => 'Itinero de la menu-ligilo.',
        ],
        'msg' => [
            'msg_insert' => 'La menuo estis sukcese aldonita.',
            'msg_update' => 'La menuo estis sukcese ĝisdatigita.',
            'msg_delete' => 'La menuo estis sukcese forigita.',
            'msg_get' => 'La menuo estis sukcese akirita.',
            'msg_get_fail' => 'La menuo ne estis trovita aŭ jam estis forigita.',
            'msg_fail_order' => 'Malsukcesis reordigi la menuon.',
        ],
    ],
    /**
     * User.
     */
    'user' => [
        'add' => 'Aldoni uzanton',
        'edit' => 'Redakti uzanton',
        'title' => 'Uzanta administrado',
        'subtitle' => 'Listo de uzantoj',
        'lastname' => 'Familinomo',
        'firstname' => 'Persona nomo',
        'fields' => [
            'active' => 'Aktiva',
            'profile' => 'Profilo',
            'join' => 'Membro ekde',
            'setting' => 'Agordoj',
            'non_active' => 'Neaktiva',
        ],
        'msg' => [
            'msg_insert' => 'La uzanto estis sukcese aldonita.',
            'msg_update' => 'La uzanto estis sukcese ĝisdatigita.',
            'msg_delete' => 'La uzanto estis sukcese forigita.',
            'msg_get' => 'La uzanto estis sukcese akirita.',
            'msg_get_fail' => 'La uzanto ne estis trovita aŭ jam estis forigita.',
        ],
    ],
    /**
     * Auth
     */
    'Auth' => [
        'showPassword' => 'Montri pasvorton',
    ],
];
