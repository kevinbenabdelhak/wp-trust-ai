<?php  


if (!defined('ABSPATH')) {
    exit; 
}

add_action('wp_ajax_wp_trust_ai_generate_product_review', 'wp_trust_ai_generate_product_review');
function wp_trust_ai_generate_product_review() {
    check_ajax_referer('generate_product_review_nonce', 'nonce');

    $product_id = intval($_POST['product_id']);
    $writing_style = sanitize_text_field($_POST['writing_style']);
    $api_key = get_option('wp_trust_ai_api_key', '');

    if (empty($api_key)) {
        wp_send_json_error('Clé API manquante.');
    }

    $product = wc_get_product($product_id);
    $product_name = $product->get_name();
    $product_short_description = $product->get_short_description();
    $product_description = $product->get_description();
    $min_words = get_option('wp_trust_ai_min_words', 5);
    $max_words = get_option('wp_trust_ai_max_words', 20);

    $default_writing_style = get_option('wp_trust_ai_default_writing_style', 'écrit de manière professionnelle et concise'); // Valeur par défaut

    $first_names = ['Alice', 'Bob', 'Charlie', 'Daisy', 'Eve', 'Frank'];
    $last_names = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis'];

    $gpt_model = get_option('wp_trust_ai_gpt_model', 'gpt-4o-mini');
    
    $random_first_name = $first_names[array_rand($first_names)];
    $random_last_name = $last_names[array_rand($last_names)];
    $full_name = "$random_first_name $random_last_name";

    $prompt = [
        [
            'role' => 'user',
            'content' => "Voici le style d'écriture globale à respecter : $default_writing_style. Donne-moi un avis sur le produit '$product_name' dans le style suivant : '$writing_style'. Trois clés à inclure dans la réponse: 'Nom', 'Prénom', et 'Avis'. Exemple de format : { \"Nom\": \"John\", \"Prénom\": \"Doe\", \"Avis\": \"C'est un excellent produit.\" Donne un nom et prénom qui n'est pas commun et qui ne risque pas d'exister et français, ainsi qu'un avis personnel.}"
        ],
    ];

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
        'timeout' => 100,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode([
            'model' => $gpt_model,
            'messages' => $prompt,
            'temperature' => 0.6,
            'top_p' => 0.6,
            'max_tokens' => 300,
        ]),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('Erreur lors de la communication avec l\'API.');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        wp_send_json_error('La réponse de l\'API ne contient pas de contenu d\'avis valide.');
    }

    $json_response = json_decode(trim($data['choices'][0]['message']['content']), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Erreur de parsing de la réponse JSON.');
    }

    $full_name = $json_response['Prénom'] . ' ' . $json_response['Nom'];
    $review_content = $json_response['Avis'];

    $review_data = array(
        'comment_post_ID' => $product_id,
        'comment_content' => $review_content,
        'comment_author' => $full_name,
        'comment_author_email' => '',
        'comment_approved' => 1,
        'comment_type' => 'review',
    );

    $comment_id = wp_insert_comment($review_data);

    if ($comment_id) {
        $comment_html = '<tr id="comment-' . $comment_id . '" class="review even thread-even depth-1 approved">' .
            '<td class="author column-author" data-colname="Auteur/autrice">' .
              '<strong>' . esc_html($full_name) . '</strong><br>' .
            '</td>' .
            '<td class="comment column-comment has-row-actions column-primary" data-colname="Commentaire">' .
               '<div class="comment-author">' .
                  '<strong>' . esc_html($full_name) . '</strong><br>' .
               '</div>' .
               '<p>' . esc_html($review_content) . '</p>' .
               '<div class="row-actions">' .
                   '<span class="approve"><a href="#">Approuver</a></span>' .
                   '<span class="unapprove"><a href="#">Désapprouver</a></span>' .
                   '<span class="reply"> | <button type="button" data-comment-id="' . $comment_id . '" data-action="replyto" class="comment-inline button-link">Répondre</button></span>' .
               '</div>' .
            '</td>' .
            '</tr>';

        wp_send_json_success($comment_html);
    } else {
        wp_send_json_error('Erreur lors de l\'insertion du commentaire.');
    }
}