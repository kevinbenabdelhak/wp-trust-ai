<?php

/*
Plugin Name: WP Trust AI
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-trust-ai/
Description: WP Trust AI est un plugin WordPress qui utilise OpenAI pour générer des avis produits automatisés basés à partir de styles d'écriture personnalisables.
Version: 1.0
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr/
Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) {
    exit; 
}


require_once plugin_dir_path(__FILE__) . 'options/options.php';
require_once plugin_dir_path(__FILE__) . 'avis/ajax.php';
require_once plugin_dir_path(__FILE__) . 'avis/requete.php';

