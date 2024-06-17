<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$wpm_lang = [
    // web-push-notification.page
    'help_agent_function' => <<<'EOD'
Tout d'abord, demandez l'autorisation, puis inscrivez-vous au service de notification push.
Vous devez aller sur cette page avec chaque appareil sur lequel vous souhaitez utiliser la notification push afin de les enregistrer.

Ordinateur (bureau/portable) : vous devez garder votre navigateur ouvert afin de recevoir les notifications. S'il est fermé, les notifications seront en attente et arriveront lorsque vous l'ouvrirez à nouveau.
iPhone/iPad : il est nécessaire d'ajouter cette page à l'écran d'accueil afin d'activer les notifications push.
EOD,
    'registered_devices'            => 'Appareils enregistrés :',
    'button_show_list'              => 'Afficher la liste',
    'button_hide_list'              => 'Cacher la liste',
    'button_show_advanced_settings' => 'Afficher les paramètres avancés',
    'button_hide_advanced_settings' => 'Cacher les paramètres avancés',
    'vapid_public_key'              => 'Clé publique VAPID :',
    'vapid_private_key'             => 'Clé privée VAPID :',
    'help_vapid_public_key'         => 'Clé nécessaire pour chiffrer et envoyer une notification au service de notification push.',
    'help_vapid_private_key'        => 'Clé nécessaire pour authentifier votre message et envoyer une notification au service de notification push.',
    'must_be_generated'             => 'Doit être générée',
    'button_generate_vapid_keys'    => 'Générer les clés VAPID',
    'help_generate_vapid_keys'      => 'La génération d\'un nouveau VAPID révoquera les appareils enregistrés. Vous devrez les enregistrer à nouveau.',
    'permission'                    => 'Permission :',
    'permission_status'             => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut.',
    'help_permission'               => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut. Vous devez accéder à cette page avec chaque appareils pour lesquels vous souhaitez utiliser la notification push, puis cliquer sur le bouton ci-dessous.',
    'error'                         => 'Erreur :',
    'button_request_register'       => 'Autoriser et Inscrire',
    'silent_notification'           => 'Notificiation silencieuse :',
    'help_silent_notification'      => 'Sélectionnez les niveaux de notification pour lesquels vous souhaitez qu\'elles soient silencieuses (pas de vibreur/son) à leur réception.',
    'http_plugin_disabled'          => 'Ne fonctionne que sur une connexion HTTPS avec un certificat valide. Voir <a href="/Settings/ManagementAccess">Gestion des Accès</a>.',
    'test_event'                    => 'Statut Unraid',
    'test_subject'                  => 'Test',
    'test_description'              => 'Ça fonctionne ?',

    // actions.php
    'error_msg_default'    => 'C\'est peut-être OK ou pas, mais quelque chose s\'est passé...',
    'device_removed'       => 'Appareil supprimé.',
    'no_message_to_push'   => 'Aucun message à envoyer.',
    'no_registered_device' => 'Aucun appareil enregistré vers lequel envoyer la notification.',
    'device_not_found'     => 'Appareil non trouvé.',
    'push_to_x_devices'    => 'Message envoyé à %1$d appareil%2$s.',
    'unknown_action'       => 'Action inconnue.',

    // web-push-notification.js
    'no_service_worker_support'             => 'Service worker non pris en charge par le navigateur.',
    'no_push_api_support'                   => 'API Push non prise en charge par le navigateur.',
    'safari_ios_home_screen'                => 'iPhone/iPad: vous devez ajouter cette page à l\'écran d\'accueil afin d\'activer les notifications push.',
    'permissions_granted_sw_not_registered' => 'Autorisations accordées mais service worker non installé.',
    'permissions_granted_no_subscription'   => 'Autorisations accordées mais non inscrit au service de notifications push.',
    'permissions_granted_registered'        => 'Autorisations accordées et inscription faite pour les notifications push.',
    'error_retrieving_subscription'         => 'Erreur lors de la récupération de l\'inscription aux notifications push :',
    'error_retrieving_registrations'        => 'Erreur lors de la récupération des inscriptions des service workers :',
    'permissions_denied'                    => 'Autorisations refusées pour les notifications. Autoriser les notifications pour ce site dans les préférences du navigateur.',
    'permissions_not_granted'               => 'Autorisations non accordées pour les notifications.',
    'error_subscribing'                     => 'Erreur lors de l\'inscription aux notifications push.',
    'error_registering'                     => 'Erreur lors de l\'enregistrement du service worker.',
    'error_unregistering'                   => 'Erreur lors de la désinscription du service worker :',
    'error_unsubscribing'                   => 'Erreur lors de la désinscription des notifications push :',
    'action'                                => 'Action',
    'date'                                  => 'Date',
    'user_agent'                            => 'User Agent',
    'ip_address'                            => 'Adresse IP',
    'loading'                               => 'Chargement...',
    'no_devices'                            => 'Pas d\'appareil.',
    'test'                                  => 'Tester',
    'remove'                                => 'Supprimer',
    'remove_device'                         => 'Supprimer cet appareil ?',
    'current_device'                        => 'Appareil actuel',
    'registration_progress'                 => 'En train de faire plein de trucs...',
    'generate_vapid_keys'                   => 'Voulez-vous générer les clés VAPID ?',
    'error_while_generating'                => 'Erreur lors de la génération',
];
