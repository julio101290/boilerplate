<?php

return [
    'global' => [
        'save' => 'Salva',
        'close' => 'Chiudi',
        'action' => 'Azione',
        'logout' => 'Disconnetti',
        'search' => 'Cerca',
        'sweet' => [
            'title' => 'Sei sicuro?',
            'text' => 'Non potrai annullare questa operazione!',
            'confirm_delete' => 'Sì, elimina!',
        ],
    ],
    /**
     * Permission.
     */
    'permission' => [
        'add' => 'Aggiungi permesso',
        'edit' => 'Modifica permesso',
        'title' => 'Gestione dei permessi',
        'subtitle' => 'Elenco dei permessi',
        'fields' => [
            'name' => 'Permesso',
            'description' => 'Descrizione',
            'plc_name' => 'Nome del permesso',
            'plc_description' => 'Descrizione del permesso',
        ],
        'msg' => [
            'msg_insert' => 'Il permesso è stato aggiunto con successo.',
            'msg_update' => 'Il permesso con ID {0} è stato aggiornato con successo.',
            'msg_delete' => 'Il permesso con ID {0} è stato eliminato con successo.',
            'msg_get' => 'Il permesso con ID {0} è stato recuperato con successo.',
            'msg_get_fail' => 'Il permesso con ID {0} non è stato trovato o è già stato eliminato.',
        ],
    ],
    /**
     * Role.
     */
    'role' => [
        'add' => 'Aggiungi ruolo',
        'edit' => 'Modifica ruolo',
        'title' => 'Gestione dei ruoli',
        'subtitle' => 'Elenco dei ruoli',
        'fields' => [
            'name' => 'Ruolo',
            'description' => 'Descrizione',
            'plc_name' => 'Nome del ruolo',
            'plc_description' => 'Descrizione del ruolo',
        ],
        'msg' => [
            'msg_insert' => 'Il ruolo è stato aggiunto con successo.',
            'msg_update' => 'Il ruolo con ID {0} è stato aggiornato con successo.',
            'msg_delete' => 'Il ruolo con ID {0} è stato eliminato con successo.',
            'msg_get' => 'Il ruolo con ID {0} è stato recuperato con successo.',
            'msg_get_fail' => 'Il ruolo con ID {0} non è stato trovato o è già stato eliminato.',
        ],
    ],
    /**
     * Menu.
     */
    'menu' => [
        'expand' => 'Espandi',
        'collapse' => 'Comprimi',
        'refresh' => 'Aggiorna',
        'add' => 'Aggiungi menu',
        'edit' => 'Modifica menu',
        'title' => 'Gestione dei menu',
        'subtitle' => 'Elenco dei menu',
        'fields' => [
            'parent' => 'Genitore',
            'warning_parent' => 'Attenzione! Il menu supporta solo una profondità massima di 2.',
            'active' => 'Attivo',
            'non_active' => 'Non attivo',
            'icon' => 'Icona',
            'info_icon' => 'Per altre icone, vedere',
            'place_icon' => 'Icona Font Awesome.',
            'name' => 'Titolo',
            'place_title' => 'Nome del menu.',
            'route' => 'Percorso',
            'place_route' => 'Percorso del link del menu.',
        ],
        'msg' => [
            'msg_insert' => 'Il menu è stato aggiunto con successo.',
            'msg_update' => 'Il menu è stato aggiornato con successo.',
            'msg_delete' => 'Il menu è stato eliminato con successo.',
            'msg_get' => 'Il menu è stato recuperato con successo.',
            'msg_get_fail' => 'Il menu non è stato trovato o è già stato eliminato.',
            'msg_fail_order' => 'Impossibile riordinare il menu.',
        ],
    ],
    /**
     * User.
     */
    'user' => [
        'add' => 'Aggiungi utente',
        'edit' => 'Modifica utente',
        'title' => 'Gestione utenti',
        'subtitle' => 'Elenco utenti',
        'lastname' => 'Cognome',
        'firstname' => 'Nome',
        'fields' => [
            'active' => 'Attivo',
            'profile' => 'Profilo',
            'join' => 'Membro dal',
            'setting' => 'Impostazioni',
            'non_active' => 'Non attivo',
        ],
        'msg' => [
            'msg_insert' => 'L’utente è stato aggiunto con successo.',
            'msg_update' => 'L’utente è stato aggiornato con successo.',
            'msg_delete' => 'L’utente è stato eliminato con successo.',
            'msg_get' => 'L’utente è stato recuperato con successo.',
            'msg_get_fail' => 'L’utente non è stato trovato o è già stato eliminato.',
        ],
    ],
    /**
     * Auth
     */
    'Auth' => [
        'showPassword' => 'Mostra password',
    ],
];
