<?php 


if (!defined('ABSPATH')) {
    exit; 
}

add_action('admin_menu', 'wp_trust_ai_add_admin_menu');
function wp_trust_ai_add_admin_menu() {
    add_options_page('WP Trust AI', 'WP Trust AI', 'manage_options', 'wp_trust_ai', 'wp_trust_ai_options_page');
}

add_action('admin_init', 'wp_trust_ai_register_settings');
function wp_trust_ai_register_settings() {
    register_setting('wp_trust_ai_options_group', 'wp_trust_ai_api_key');
    register_setting('wp_trust_ai_options_group', 'wp_trust_ai_min_words');
    register_setting('wp_trust_ai_options_group', 'wp_trust_ai_max_words');
    register_setting('wp_trust_ai_options_group', 'wp_trust_ai_gpt_model');
    register_setting('wp_trust_ai_options_group', 'wp_trust_ai_default_writing_style');
}

function wp_trust_ai_options_page() {
    ?>
    <div class="wrap">
        <h1>WP Trust AI - Réglages</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('wp_trust_ai_options_group');
            do_settings_sections('wp_trust_ai_options_group');
            $api_key = get_option('wp_trust_ai_api_key', '');
            $min_words = get_option('wp_trust_ai_min_words', 5);
            $max_words = get_option('wp_trust_ai_max_words', 20);
            $gpt_model = get_option('wp_trust_ai_gpt_model', 'gpt-4o-mini');
            $default_writing_style = get_option('wp_trust_ai_default_writing_style', 'écrit de manière professionnelle..');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Clé API OpenAI</th>
                    <td><input type="text" name="wp_trust_ai_api_key" value="<?php echo esc_attr($api_key); ?>" />
                        <p><a href="https://platform.openai.com/api-keys" target="_blank">Générer une clé OpenAI</a></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Nombre de mots minimum dans un avis</th>
                    <td><input type="number" name="wp_trust_ai_min_words" value="<?php echo esc_attr($min_words); ?>" min="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Nombre de mots maximum dans un avis</th>
                    <td><input type="number" name="wp_trust_ai_max_words" value="<?php echo esc_attr($max_words); ?>" min="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Modèle GPT</th>
                    <td>
                        <select name="wp_trust_ai_gpt_model">
                            <option value="gpt-5" <?php selected($gpt_model, 'gpt-5'); ?>>gpt-5</option>
                            <option value="gpt-4o-mini" <?php selected($gpt_model, 'gpt-4o-mini'); ?>>gpt-4o-mini</option>
                            <option value="gpt-4o" <?php selected($gpt_model, 'gpt-4o'); ?>>gpt-4o</option>
                            <option value="gpt-3.5-turbo" <?php selected($gpt_model, 'gpt-3.5-turbo'); ?>>gpt-3.5-turbo</option>
                        </select>
                        <p>Sélectionnez un modèle d'OpenAI</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Style d'écriture par défaut</th>
                    <td><textarea name="wp_trust_ai_default_writing_style" rows="3" style="width: 100%;"><?php echo esc_textarea($default_writing_style); ?></textarea>
                        <p>Indiquer le style d'écriture généraliste à utiliser.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


