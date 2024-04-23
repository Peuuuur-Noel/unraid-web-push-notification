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
    // index.page
    'help_agent_function' => <<<'EOD'
Générer le VAPID en premier si ce n'est pas déjà fait, puis demandez l'autorisation afin de vous inscrire au service de notification push.

Vous devez aller sur cette page avec chaque appareil avec lequel vous souhaitez utiliser la notification push afin de les enregistrer aussi.
Vous devez garder votre navigateur ouvert pour recevoir les notifications. S'il est fermé, les notifications seront en attente et arriveront lorsque vous l'ouvrirez à nouveau.

Sur mobile, il peut être nécessaire d'ajouter cette page à l'écran d'accueil de votre appareil afin d'activer les notifications push.
EOD,
    'button_show_registered_devices' => 'Afficher les appareils enregistrés',
    'button_hide_registered_devices' => 'Cacher les appareils enregistrés',
    'vapid_public_key' => 'Clé publique VAPID :',
    'vapid_private_key' => 'Clé privée VAPID :',
    'help_vapid_public_key' => 'Clé nécessaire pour chiffrer et envoyer une notification au service de notification push.',
    'help_vapid_private_key' => 'Clé nécessaire pour authentifier votre message et envoyer une notification au service de notification push.',
    'must_be_generated' => 'Doit être générée',
    'button_generate_vapid_keys' => 'Générer les clés VAPID',
    'help_generate_vapid_keys' => 'La génération d\'un nouveau VAPID révoquera les appareils enregistrés. Vous devrez les enregistrer à nouveau.',
    'permission' => 'Permission :',
    'permission_status' => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut.',
    'help_permission' => 'Cliquez sur le bouton ci-dessous pour demander l\'autorisation et vous inscrire aux notifications push ou vérifier le statut. Vous devez accéder à cette page avec chaque appareils pour lesquels vous souhaitez utiliser la notification push, puis cliquer sur le bouton ci-dessous.',
    'error' => 'Erreur :',
    'button_request_register' => 'Autoriser et Inscrire',
    'silent_notification' => 'Notificiation silencieuse :',
    'help_silent_notification' => 'Sélectionnez les niveaux de notification pour lesquels vous ne souhaitez pas qu\'elles vibrent/émettent de son à leur réception.',

    // actions.php
    'error_msg_default' => 'C\'est peut-être OK ou pas, mais quelque chose s\'est passé...',
    'device_removed' => 'Appareil supprimé.',
    'no_message_to_push' => 'Aucun message à envoyer.',
    'no_registered_device' => 'Aucun appareil enregistré vers lequel envoyer la notification.',
    'push_to_x_devices' => 'Message envoyé à %1$d appareil%2$s.',
    'unknown_action' => 'Action inconnue.',

    // index.js
    'no_service_worker_support' => 'Service worker non pris en charge par le navigateur.',
    'no_push_api_support' => 'API Push non prise en charge par le navigateur.',
    'permissions_granted_not_registered' => 'Autorisations accordées mais non inscrit au service. Inscrivez-vous pour recevoir les notifications push.',
    'permissions_granted_registered' => 'Autorisations accordées et inscription faite pour les notifications push.',
    'error_retrieving_subscription' => 'Erreur lors de la récupération de l\'inscription aux notifications push :',
    'error_retrieving_registrations' => 'Erreur lors de la récupération des inscriptions des service workers :',
    'permissions_denied' => 'Autorisations refusées pour les notifications. Autoriser les notifications pour ce site dans les préférences du navigateur.',
    'permissions_not_granted' => 'Autorisations non accordées pour les notifications.',
    'error_subscribing' => 'Erreur lors de l\'inscription aux notifications push.',
    'error_registering' => 'Erreur lors de l\'enregistrement du service worker.',
    'service_worker_not_registered' => 'Service worker non enregistré.',
    'error_unregistering' => 'Erreur lors de la désinscription du service worker :',
    'error_unsubscribing' => 'Erreur lors de la désinscription des notifications push :',
    'action' => 'Action',
    'date' => 'Date',
    'user_agent' => 'User Agent',
    'ip_address' => 'Adresse IP',
    'loading' => 'Chargement...',
    'no_devices' => 'Pas d\'appareil.',
    'remove' => 'Supprimer',
    'remove_device' => 'Supprimer cet appareil ?',
    'current_device' => 'Appareil actuel',
    'registration_progress' => 'En train de faire plein de trucs...',
    'generate_vapid_keys' => 'Voulez-vous générer les clés VAPID ?',
    'error_while_generating' => 'Erreur lors de la génération',
];
