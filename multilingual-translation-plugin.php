<?php
/**
 * Plugin Name: Multilingual Translation Plugin
 * Plugin URI:  https://example.com/multilingual-translation-plugin
 * Description: A simple plugin to add multilingual support for posts and pages.
 * Version:     1.0.0
 * Author:      Jay
 * Author URI:  https://example.com
 * License:     GPLv2 or later
 */

 // Add a meta box for translations
function mtp_add_translation_meta_box() {
    add_meta_box('mtp_translation_meta_box', __('Translations', 'text_domain'), 'mtp_translation_meta_box_callback', array('post', 'page'), 'normal', 'high');
}
add_action('add_meta_boxes', 'mtp_add_translation_meta_box');

// Meta box callback
function mtp_translation_meta_box_callback($post) {
    // Supported languages including new ones
    $languages = array(
        'fr' => 'French', 
        'es' => 'Spanish', 
        'de' => 'German',
        'it' => 'Italian', 
        'pt' => 'Portuguese', 
        'nl' => 'Dutch'
    );
    
    foreach ($languages as $code => $language) {
        $translated_content = get_post_meta($post->ID, '_mtp_translation_' . $code, true);
        ?>
        <p>
            <label for="mtp_translation_<?php echo esc_attr($code); ?>"><?php echo esc_html($language); ?> Translation</label>
            <textarea name="mtp_translation_<?php echo esc_attr($code); ?>" id="mtp_translation_<?php echo esc_attr($code); ?>" rows="4" class="large-text"><?php echo esc_textarea($translated_content); ?></textarea>
        </p>
        <?php
    }
}

// Save translation meta box data
function mtp_save_translation_meta_box_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Supported languages including new ones
    $languages = array('fr', 'es', 'de', 'it', 'pt', 'nl');
    
    foreach ($languages as $code) {
        if (isset($_POST['mtp_translation_' . $code])) {
            update_post_meta($post_id, '_mtp_translation_' . $code, sanitize_textarea_field($_POST['mtp_translation_' . $code]));
        }
    }
}
add_action('save_post', 'mtp_save_translation_meta_box_data');

// Add a language switcher in the frontend header with new languages
function mtp_add_language_switcher() {
    // Supported languages including new ones
    $languages = array(
        'en' => 'English', 
        'fr' => 'French', 
        'es' => 'Spanish', 
        'de' => 'German',
        'it' => 'Italian', 
        'pt' => 'Portuguese', 
        'nl' => 'Dutch'
    );
    $current_lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : 'en';

    echo '<div class="mtp-language-switcher">';
    foreach ($languages as $code => $language) {
        echo '<a href="' . add_query_arg('lang', $code) . '" class="' . ($current_lang === $code ? 'active' : '') . '">' . esc_html($language) . '</a> ';
    }
    echo '</div>';
}
add_action('wp_footer', 'mtp_add_language_switcher');

// Filter the content to display translations for all supported languages
function mtp_filter_content($content) {
    $current_lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : 'en';

    if ($current_lang !== 'en') {
        $translated_content = get_post_meta(get_the_ID(), '_mtp_translation_' . $current_lang, true);
        if (!empty($translated_content)) {
            return $translated_content;
        }
    }

    return $content;
}
add_filter('the_content', 'mtp_filter_content');

// Enqueue the plugin styles
function mtp_enqueue_styles() {
    wp_enqueue_style('mtp-style', plugin_dir_url(__FILE__) . 'assets/mtp-style.css');
}
add_action('wp_enqueue_scripts', 'mtp_enqueue_styles');
