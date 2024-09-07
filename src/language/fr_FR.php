<?php

/*
 * This file is part of Web Push Notification Agent plugin for Unraid.
 *
 * (c) Peuuuur Noel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$wpmLang = [
    // web-push-notification.page
    'help_agent_function' => <<<'EOD'
Tout d'abord, demandez l'autorisation, puis inscrivez-vous au service de notification push.
Vous devez aller sur cette page avec chaque appareil sur lequel vous souhaitez utiliser la notification push afin de les enregistrer.

Ordinateur (bureau/portable) : vous devez garder votre navigateur ouvert afin de recevoir les notifications. S'il est fermé, les notifications seront en attente et arriveront lorsque vous l'ouvrirez à nouveau.
iPhone/iPad : il est nécessaire d'ajouter cette page à l'écran d'accueil afin d'activer les notifications push.
EOD,
    'registered_devices'              => 'Appareils enregistrés :',
    'button_show_list'                => 'Afficher la liste',
    'button_hide_list'                => 'Cacher la liste',
    'button_show_advanced_settings'   => 'Afficher les paramètres avancés',
    'button_hide_advanced_settings'   => 'Cacher les paramètres avancés',
    'vapid_public_key'                => 'Clé publique VAPID :',
    'vapid_private_key'               => 'Clé privée VAPID :',
    'help_vapid_public_key'           => 'Clé nécessaire pour chiffrer et envoyer une notification au service de notification push.',
    'help_vapid_private_key'          => 'Clé nécessaire pour authentifier et envoyer une notification au service de notification push.',
    'must_be_generated'               => 'Doit être générée',
    'button_generate_vapid_keys'      => 'Générer les clés VAPID',
    'help_generate_vapid_keys'        => 'La génération d\'un nouveau VAPID révoquera les appareils enregistrés. Vous devrez les enregistrer à nouveau.',
    'permission'                      => 'Permission :',
    'permission_status'               => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut.',
    'help_permission'                 => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut. Vous devez accéder à cette page avec chaque appareils pour lesquels vous souhaitez utiliser la notification push, puis cliquer sur le bouton ci-dessous.',
    'error'                           => 'Erreur :',
    'button_request_register'         => 'Autoriser et Inscrire',
    'help_notifications_silent_level' => '<strong>Notificiations silencieuses :</strong> cochez si vous voulez que les notifications soient silencieuses (pas de vibreur/son) à leur réception.<br><br> <strong>Niveau de notification minimum :</strong> niveau de notification minimum à envoyer à l\'appareil.<br> Les niveaux disponibles dépendront des niveaux sélectionnés dans la colonne "Agents" dans l\'option "Entité de notification" en haut de cette page.',
    'http_plugin_disabled'            => 'Ne fonctionne que sur une connexion HTTPS avec un certificat valide. Voir <a href="/Settings/ManagementAccess">Gestion des Accès</a>.',
    'test_event'                      => 'Statut Unraid',
    'test_subject'                    => 'Test',
    'test_description'                => 'Ça fonctionne ?',

    // actions.php
    'error_msg_default'    => 'C\'est peut-être OK ou pas, mais quelque chose s\'est passé...',
    'device_removed'       => 'Appareil supprimé.',
    'no_message_to_push'   => 'Aucune notification à envoyer.',
    'no_registered_device' => 'Aucun appareil enregistré vers lequel envoyer la notification.',
    'device_not_found'     => 'Appareil non trouvé.',
    'push_to_x_devices'    => 'Notification envoyée à %1$d appareil%2$s.',
    'unknown_action'       => 'Action inconnue.',

    // web-push-notification.js
    'no_service_worker_support'             => 'Service worker non pris en charge par le navigateur.',
    'no_push_api_support'                   => 'API Push non prise en charge par le navigateur.',
    'safari_ios_home_screen'                => 'Vous devez ajouter cette page à l\'écran d\'accueil afin d\'activer les notifications push.',
    'permissions_granted_sw_not_registered' => 'Autorisation accordée mais service worker non installé.',
    'permissions_granted_no_subscription'   => 'Autorisation accordée mais non inscrit au service de notifications push.',
    'permissions_granted_registered'        => 'Autorisation accordée et inscription faite aux notifications push.',
    'error_retrieving_subscription'         => 'Erreur lors de la récupération de l\'inscription aux notifications push :',
    'error_retrieving_registrations'        => 'Erreur lors de la récupération des inscriptions des service workers :',
    'permissions_denied'                    => 'Autorisation refusée pour les notifications. Autoriser les notifications pour ce site dans les préférences du navigateur.',
    'permissions_not_granted'               => 'Autorisation non accordée pour les notifications.',
    'error_subscribing'                     => 'Erreur lors de l\'inscription aux notifications push.',
    'error_registering'                     => 'Erreur lors de l\'enregistrement du service worker.',
    'error_unregistering'                   => 'Erreur lors de la désinscription du service worker :',
    'error_unsubscribing'                   => 'Erreur lors de la désinscription aux notifications push :',
    'action'                                => 'Action',
    'device_info'                           => 'Information sur l\'appareil',
    'notification_settings'                 => 'Paramètres de notification',
    'device_name'                           => 'Nom :',
    'date'                                  => 'Date :',
    'user_agent'                            => 'Agent Utilisateur :',
    'ip_address'                            => 'Adresse IP :',
    'loading'                               => 'Chargement...',
    'no_devices'                            => 'Aucun appareil.',
    'test'                                  => 'Tester',
    'remove'                                => 'Supprimer',
    'remove_device'                         => 'Supprimer cet appareil ?',
    'current_device'                        => 'Appareil actuel',
    'rename'                                => 'Renommer',
    'notification_level_lowest'             => 'Niveau de notification minimum :',
    'notification_level_default'            => 'Défaut',
    'notification_level_notices'            => 'Annonces',
    'notification_level_warnings'           => 'Avertissements',
    'notification_level_alerts'             => 'Alertes',
    'silent_notifications'                  => 'Notificiations silencieuses :',
    'unsupported_firefox'                   => 'Non pris en charge par Firefox',
    'save'                                  => 'Enregistrer',
    'registration_progress'                 => 'En train de faire plein de trucs...',
    'generate_vapid_keys'                   => 'Voulez-vous générer les clés VAPID ?',
    'error_while_generating'                => 'Erreur lors de la génération',
];
