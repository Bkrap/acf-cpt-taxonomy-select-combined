<?php
/**
 * Example Usage: ACF CPT & Taxonomy Select Combined
 * 
 * This file demonstrates how to use the custom field type in your WordPress theme or plugin.
 * DO NOT include this file in production - it's for reference only.
 */

// ===================================
// Example 1: Basic Field Display
// ===================================

function example_display_combined_field() {
    // Get the field value
    $selected = get_field('my_combined_field');
    
    if (empty($selected)) {
        echo '<p>No items selected</p>';
        return;
    }
    
    echo '<h3>Selected Items</h3>';
    echo '<ul>';
    
    // Display selected posts
    if (!empty($selected['post_objects'])) {
        foreach ($selected['post_objects'] as $post_id) {
            $post_title = get_the_title($post_id);
            $post_link = get_permalink($post_id);
            echo '<li><a href="' . esc_url($post_link) . '">' . esc_html($post_title) . '</a> (Post)</li>';
        }
    }
    
    // Display selected taxonomy terms
    if (!empty($selected['taxonomy_terms'])) {
        foreach ($selected['taxonomy_terms'] as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                $term_link = get_term_link($term);
                echo '<li><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a> (Term)</li>';
            }
        }
    }
    
    echo '</ul>';
}

// ===================================
// Example 2: Create Field Group Programmatically
// ===================================

function example_register_field_group() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_example_combined',
            'title' => 'Example Combined Field',
            'fields' => array(
                array(
                    'key' => 'field_example_combined',
                    'label' => 'Related Content',
                    'name' => 'related_content',
                    'type' => 'cpt_taxonomy_select',
                    
                    // Filter by post types
                    'post_type' => array('post', 'page', 'product'),
                    
                    // Filter by taxonomies
                    'taxonomy' => array('category', 'post_tag', 'product_cat'),
                    
                    // Appearance options
                    'field_type' => 'select',  // 'select', 'checkbox', or 'radio'
                    'multiple' => 1,            // Allow multiple selections
                    'allow_null' => 1,          // Allow empty selection
                    'show_hierarchy' => 1,      // Show "Post:" or "Term:" prefix
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
            ),
        ));
    }
}
add_action('acf/init', 'example_register_field_group');

// ===================================
// Example 3: Query Posts by Selected Items
// ===================================

function example_query_related_content() {
    $selected = get_field('related_content');
    
    if (empty($selected)) {
        return;
    }
    
    // Query posts by selected post IDs
    if (!empty($selected['post_objects'])) {
        $args = array(
            'post__in' => $selected['post_objects'],
            'post_type' => 'any',
            'posts_per_page' => -1,
            'orderby' => 'post__in',
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            echo '<h3>Related Posts</h3>';
            echo '<div class="related-posts">';
            
            while ($query->have_posts()) {
                $query->the_post();
                echo '<article>';
                echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
                echo '<div>' . get_the_excerpt() . '</div>';
                echo '</article>';
            }
            
            echo '</div>';
            wp_reset_postdata();
        }
    }
    
    // Display selected taxonomy terms
    if (!empty($selected['taxonomy_terms'])) {
        echo '<h3>Related Categories/Tags</h3>';
        echo '<div class="related-terms">';
        
        foreach ($selected['taxonomy_terms'] as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                echo '<a href="' . get_term_link($term) . '" class="term-badge">' . $term->name . '</a> ';
            }
        }
        
        echo '</div>';
    }
}

// ===================================
// Example 4: Conditional Logic
// ===================================

function example_conditional_display() {
    $selected = get_field('related_content');
    
    // Check if any posts are selected
    $has_posts = !empty($selected['post_objects']);
    
    // Check if any terms are selected
    $has_terms = !empty($selected['taxonomy_terms']);
    
    // Check if specific post ID is selected
    $specific_post_selected = $has_posts && in_array(123, $selected['post_objects']);
    
    // Check if specific term ID is selected
    $specific_term_selected = $has_terms && in_array(456, $selected['taxonomy_terms']);
    
    if ($specific_post_selected) {
        echo '<div class="notice">Special post is selected!</div>';
    }
    
    if ($specific_term_selected) {
        echo '<div class="notice">Special term is selected!</div>';
    }
    
    // Count total selections
    $total = ($has_posts ? count($selected['post_objects']) : 0) + 
             ($has_terms ? count($selected['taxonomy_terms']) : 0);
    
    echo '<p>Total items selected: ' . $total . '</p>';
}

// ===================================
// Example 5: Get Full Post/Term Objects
// ===================================

function example_get_full_objects() {
    $selected = get_field('related_content');
    
    if (empty($selected)) {
        return array();
    }
    
    $results = array(
        'posts' => array(),
        'terms' => array(),
    );
    
    // Get full post objects
    if (!empty($selected['post_objects'])) {
        foreach ($selected['post_objects'] as $post_id) {
            $post = get_post($post_id);
            if ($post) {
                $results['posts'][] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'url' => get_permalink($post->ID),
                    'excerpt' => get_the_excerpt($post->ID),
                    'thumbnail' => get_the_post_thumbnail_url($post->ID, 'medium'),
                );
            }
        }
    }
    
    // Get full term objects
    if (!empty($selected['taxonomy_terms'])) {
        foreach ($selected['taxonomy_terms'] as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                $results['terms'][] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'url' => get_term_link($term),
                    'count' => $term->count,
                    'taxonomy' => $term->taxonomy,
                );
            }
        }
    }
    
    return $results;
}

// ===================================
// Example 6: REST API Integration
// ===================================

function example_rest_api_field() {
    register_rest_field(
        'post', // Post type
        'related_content', // Field name
        array(
            'get_callback' => function($post) {
                $selected = get_field('related_content', $post['id']);
                
                if (empty($selected)) {
                    return null;
                }
                
                $response = array(
                    'posts' => array(),
                    'terms' => array(),
                );
                
                // Add post data
                if (!empty($selected['post_objects'])) {
                    foreach ($selected['post_objects'] as $post_id) {
                        $response['posts'][] = array(
                            'id' => $post_id,
                            'title' => get_the_title($post_id),
                            'link' => get_permalink($post_id),
                        );
                    }
                }
                
                // Add term data
                if (!empty($selected['taxonomy_terms'])) {
                    foreach ($selected['taxonomy_terms'] as $term_id) {
                        $term = get_term($term_id);
                        if ($term && !is_wp_error($term)) {
                            $response['terms'][] = array(
                                'id' => $term->term_id,
                                'name' => $term->name,
                                'link' => get_term_link($term),
                            );
                        }
                    }
                }
                
                return $response;
            },
            'schema' => array(
                'description' => 'Related content (posts and terms)',
                'type' => 'object',
            ),
        )
    );
}
add_action('rest_api_init', 'example_rest_api_field');

// ===================================
// Example 7: Shortcode Implementation
// ===================================

function example_related_content_shortcode($atts) {
    $atts = shortcode_atts(array(
        'field' => 'related_content',
        'show_posts' => 'yes',
        'show_terms' => 'yes',
    ), $atts);
    
    $selected = get_field($atts['field']);
    
    if (empty($selected)) {
        return '';
    }
    
    ob_start();
    
    echo '<div class="related-content-widget">';
    
    // Display posts
    if ($atts['show_posts'] === 'yes' && !empty($selected['post_objects'])) {
        echo '<div class="related-posts">';
        echo '<h4>Related Posts</h4>';
        echo '<ul>';
        foreach ($selected['post_objects'] as $post_id) {
            echo '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    
    // Display terms
    if ($atts['show_terms'] === 'yes' && !empty($selected['taxonomy_terms'])) {
        echo '<div class="related-terms">';
        echo '<h4>Related Terms</h4>';
        echo '<ul>';
        foreach ($selected['taxonomy_terms'] as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }
    
    echo '</div>';
    
    return ob_get_clean();
}
add_shortcode('related_content', 'example_related_content_shortcode');

// Usage: [related_content field="related_content" show_posts="yes" show_terms="yes"]

