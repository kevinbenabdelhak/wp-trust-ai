<?php 

if (!defined('ABSPATH')) {
    exit; 
}
add_action('admin_footer', 'wp_trust_ai_generate_review_script');
function wp_trust_ai_generate_review_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            if ($('#post_type').val() === 'product') {
                var reviewSection = '<div class="options_group" style="margin-top: 20px;">' +
                    '<h3>' + '<?php echo esc_js(__('Générer des avis', 'woocommerce')); ?>' + '</h3>' +
                    '<textarea id="writing_style" rows="3" style="width: 100%;" placeholder="Indiquez le style d\'écriture ici..."></textarea><br><br>' +
                    '<input type="number" id="review_count" min="1" value="1" style="width: 50px;" /> Avis à générer<br><br>' +
                    '<button id="generate-product-review" class="button">Générer des avis</button>' +
                    '<div id="review-status"></div>' + 
                    '</div>';
                
                $('#commentsdiv .inside').append(reviewSection);

                $('#generate-product-review').on('click', function(e) {
                    e.preventDefault();
                    var productID = $('#post_ID').val();
                    var reviewCount = parseInt($('#review_count').val()) || 1;
                    var writingStyle = $('#writing_style').val();

                    $('#review-status').html('<div class="loader">Génération des avis en cours...</div>');
                    let currentCount = 1;
                    let successCount = 0;

                    function generateReview() {
                        if (currentCount > reviewCount) {
                            $('#review-status').append('<p>Tous les avis générés avec succès ! Total : ' + successCount + '</p>');
                            return;
                        }
                        var data = {
                            'action': 'wp_trust_ai_generate_product_review',
                            'product_id': productID,
                            'writing_style': writingStyle || '<?php echo esc_js($default_writing_style); ?>',
                            'nonce': '<?php echo wp_create_nonce("generate_product_review_nonce"); ?>'
                        };
                        
                        // Utilisation d'AJAX pour traiter la requête
                        $.post(ajaxurl, data, function(response) {
                            if (response.success) {
                                successCount++;
                                $('#the-comment-list').append(response.data);
                                $('#review-status').append('<p>Avis ' + currentCount + '/' + reviewCount + ' généré avec succès.</p>');
                                $('.comments-box').css('display', 'block');
                            } else {
                                $('#review-status').append('<p style="color:red;">Erreur génération avis ' + currentCount + ': ' + response.data + '</p>');
                            }

                            currentCount++;
                            generateReview();
                        });
                    }

                    generateReview();
                });
            }
        });
    </script>
    <style>
        .loader {
            margin: 10px 0;
            font-weight: bold;
        }
        .options_group{
            padding:10px;
        }
    </style>
    <?php
}