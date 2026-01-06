<?php

return [
    'global' => [
        'save' => 'Speichern',
        'close' => 'Schließen',
        'action' => 'Aktion',
        'logout' => 'Abmelden',
        'search' => 'Suchen',
        'sweet' => [
            'title' => 'Sind Sie sicher?',
            'text' => 'Dies kann nicht rückgängig gemacht werden!',
            'confirm_delete' => 'Ja, löschen!',
        ],
    ],
    /**
     * Permission.
     */
    'permission' => [
        'add' => 'Berechtigung hinzufügen',
        'edit' => 'Berechtigung bearbeiten',
        'title' => 'Berechtigungsverwaltung',
        'subtitle' => 'Berechtigungsliste',
        'fields' => [
            'name' => 'Berechtigung',
            'description' => 'Beschreibung',
            'plc_name' => 'Name der Berechtigung',
            'plc_description' => 'Beschreibung der Berechtigung',
        ],
        'msg' => [
            'msg_insert' => 'Die Berechtigung wurde erfolgreich hinzugefügt.',
            'msg_update' => 'Die Berechtigung mit der ID {0} wurde erfolgreich aktualisiert.',
            'msg_delete' => 'Die Berechtigung mit der ID {0} wurde erfolgreich gelöscht.',
            'msg_get' => 'Die Berechtigung mit der ID {0} wurde erfolgreich abgerufen.',
            'msg_get_fail' => 'Die Berechtigung mit der ID {0} wurde nicht gefunden oder wurde bereits gelöscht.',
        ],
    ],
    /**
     * Role.
     */
    'role' => [
        'add' => 'Rolle hinzufügen',
        'edit' => 'Rolle bearbeiten',
        'title' => 'Rollenverwaltung',
        'subtitle' => 'Rollenliste',
        'fields' => [
            'name' => 'Rolle',
            'description' => 'Beschreibung',
            'plc_name' => 'Rollenname',
            'plc_description' => 'Rollenbeschreibung',
        ],
        'msg' => [
            'msg_insert' => 'Die Rolle wurde erfolgreich hinzugefügt.',
            'msg_update' => 'Die Rolle mit der ID {0} wurde erfolgreich aktualisiert.',
            'msg_delete' => 'Die Rolle mit der ID {0} wurde erfolgreich gelöscht.',
            'msg_get' => 'Die Rolle mit der ID {0} wurde erfolgreich abgerufen.',
            'msg_get_fail' => 'Die Rolle mit der ID {0} wurde nicht gefunden oder wurde bereits gelöscht.',
        ],
    ],
    /**
     * Menu.
     */
    'menu' => [
        'expand' => 'Erweitern',
        'collapse' => 'Einklappen',
        'refresh' => 'Aktualisieren',
        'add' => 'Menü hinzufügen',
        'edit' => 'Menü bearbeiten',
        'title' => 'Menüverwaltung',
        'subtitle' => 'Menüliste',
        'fields' => [
            'parent' => 'Übergeordnet',
            'warning_parent' => 'Achtung! Das Menü unterstützt nur eine maximale Tiefe von 2.',
            'active' => 'Aktiv',
            'non_active' => 'Inaktiv',
            'icon' => 'Symbol',
            'info_icon' => 'Für weitere Symbole siehe',
            'place_icon' => 'Font-Awesome-Symbol.',
            'name' => 'Titel',
            'place_title' => 'Menüname.',
            'route' => 'Route',
            'place_route' => 'Menü-Link-Route.',
        ],
        'msg' => [
            'msg_insert' => 'Das Menü wurde erfolgreich hinzugefügt.',
            'msg_update' => 'Das Menü wurde erfolgreich aktualisiert.',
            'msg_delete' => 'Das Menü wurde erfolgreich gelöscht.',
            'msg_get' => 'Das Menü wurde erfolgreich abgerufen.',
            'msg_get_fail' => 'Das Menü wurde nicht gefunden oder wurde bereits gelöscht.',
            'msg_fail_order' => 'Das Menü konnte nicht neu sortiert werden.',
        ],
    ],
    /**
     * User.
     */
    'user' => [
        'add' => 'Benutzer hinzufügen',
        'edit' => 'Benutzer bearbeiten',
        'title' => 'Benutzerverwaltung',
        'subtitle' => 'Benutzerliste',
        'lastname' => 'Nachname',
        'firstname' => 'Vorname',
        'fields' => [
            'active' => 'Aktiv',
            'profile' => 'Profil',
            'join' => 'Mitglied seit',
            'setting' => 'Einstellungen',
            'non_active' => 'Inaktiv',
        ],
        'msg' => [
            'msg_insert' => 'Der Benutzer wurde erfolgreich hinzugefügt.',
            'msg_update' => 'Der Benutzer wurde erfolgreich aktualisiert.',
            'msg_delete' => 'Der Benutzer wurde erfolgreich gelöscht.',
            'msg_get' => 'Der Benutzer wurde erfolgreich abgerufen.',
            'msg_get_fail' => 'Der Benutzer wurde nicht gefunden oder wurde bereits gelöscht.',
        ],
    ],
    /**
     * Auth
     */
    'Auth' => [
        'showPassword' => 'Passwort anzeigen',
    ],
];
