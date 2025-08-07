<?php

/*
Plugin Name: WP Trust AI
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-trust-ai/
Description: WP Trust AI est un plugin WordPress qui utilise OpenAI pour générer des avis produits automatisés basés à partir de styles d'écriture personnalisables.
Version: 1.2
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr/
Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) {
    exit; 
}




if ( !class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$monUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kevinbenabdelhak/wp-trust-ai/', 
    __FILE__,
    'wp-trust-ai' 
);
$monUpdateChecker->setBranch('main');







require_once plugin_dir_path(__FILE__) . 'options/options.php';
require_once plugin_dir_path(__FILE__) . 'avis/ajax.php';
require_once plugin_dir_path(__FILE__) . 'avis/requete.php';

