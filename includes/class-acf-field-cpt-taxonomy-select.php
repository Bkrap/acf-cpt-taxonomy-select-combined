<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ACF Field: CPT & Taxonomy Select Combined
 */
class acf_field_cpt_taxonomy_select extends acf_field {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Field name (database)
        $this->name = 'cpt_taxonomy_select';
        
        // Field label (UI)
        $this->label = __('CPT & Taxonomy Select', 'acf-cpt-taxonomy-select-combined');
        
        // Field category
        $this->category = 'relational';
        
        // Default values
        $this->defaults = array(
            'post_type'         => array(),
            'taxonomy'          => array(),
            'field_type'        => 'select',
            'allow_null'        => 0,
            'multiple'          => 0,
            'show_hierarchy'    => 0,
            'return_format'     => 'id'
        );
        
        // Call parent constructor
        parent::__construct();
    }
    
    /**
     * Render field settings (admin)
     */
    public function render_field_settings($field) {
        
        // Post Type filter
        acf_render_field_setting($field, array(
            'label'         => __('Filter by Post Type', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Select which post types to include', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'select',
            'name'          => 'post_type',
            'choices'       => acf_get_pretty_post_types(),
            'multiple'      => 1,
            'ui'            => 1,
            'allow_null'    => 1,
            'placeholder'   => __('All post types', 'acf-cpt-taxonomy-select-combined')
        ));
        
        // Taxonomy filter
        acf_render_field_setting($field, array(
            'label'         => __('Allow Taxonomy', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Select which taxonomies to include', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'select',
            'name'          => 'taxonomy',
            'choices'       => acf_get_taxonomy_labels(),
            'multiple'      => 1,
            'ui'            => 1,
            'allow_null'    => 1,
            'placeholder'   => __('All taxonomies', 'acf-cpt-taxonomy-select-combined')
        ));
        
        // Appearance (field type)
        acf_render_field_setting($field, array(
            'label'         => __('Appearance', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Select the appearance of this field', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'select',
            'name'          => 'field_type',
            'choices'       => array(
                'select'    => __('Select', 'acf-cpt-taxonomy-select-combined'),
                'checkbox'  => __('Checkbox', 'acf-cpt-taxonomy-select-combined'),
                'radio'     => __('Radio Buttons', 'acf-cpt-taxonomy-select-combined')
            )
        ));
        
        // Allow Null (for select and radio)
        acf_render_field_setting($field, array(
            'label'         => __('Allow Null?', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Allow an empty value to be selected', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'true_false',
            'name'          => 'allow_null',
            'ui'            => 1,
            'conditions'    => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select'
                    )
                ),
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'radio'
                    )
                )
            )
        ));
        
        // Allow Multiple (for select and checkbox)
        acf_render_field_setting($field, array(
            'label'         => __('Select Multiple?', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Allow multiple values to be selected', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'true_false',
            'name'          => 'multiple',
            'ui'            => 1,
            'conditions'    => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select'
                    )
                )
            )
        ));
        
        // Show Hierarchy
        acf_render_field_setting($field, array(
            'label'         => __('Show Hierarchy in Label', 'acf-cpt-taxonomy-select-combined'),
            'instructions'  => __('Display whether the item is a post or taxonomy term in the label', 'acf-cpt-taxonomy-select-combined'),
            'type'          => 'true_false',
            'name'          => 'show_hierarchy',
            'ui'            => 1
        ));
    }
    
    /**
     * Render field (frontend/backend)
     */
    public function render_field($field) {
        
        // Get choices
        $choices = $this->get_choices($field);
        
        // Prepare field attributes
        $field_name = $field['name'];
        $field_value = $field['value'];
        
        // Normalize value to array
        if (!is_array($field_value)) {
            $field_value = $field_value ? array($field_value) : array();
        }
        
        // Render based on field type
        switch ($field['field_type']) {
            case 'checkbox':
                $this->render_checkbox($field, $choices, $field_value);
                break;
                
            case 'radio':
                $this->render_radio($field, $choices, $field_value);
                break;
                
            case 'select':
            default:
                $this->render_select($field, $choices, $field_value);
                break;
        }
    }
    
    /**
     * Get choices (posts and terms)
     */
    private function get_choices($field) {
        $choices = array();
        
        // Get posts
        if (!empty($field['post_type'])) {
            $post_args = array(
                'post_type'      => $field['post_type'],
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'publish'
            );
            
            $posts = get_posts($post_args);
            
            if ($posts) {
                foreach ($posts as $post) {
                    $prefix = $field['show_hierarchy'] ? '[Post: ' . get_post_type_object($post->post_type)->labels->singular_name . '] ' : '';
                    $choices['post_' . $post->ID] = array(
                        'label' => $prefix . $post->post_title,
                        'type'  => 'post',
                        'id'    => $post->ID
                    );
                }
            }
        }
        
        // Get taxonomy terms
        if (!empty($field['taxonomy'])) {
            foreach ($field['taxonomy'] as $taxonomy) {
                $terms = get_terms(array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC'
                ));
                
                if (!is_wp_error($terms) && !empty($terms)) {
                    $tax_obj = get_taxonomy($taxonomy);
                    foreach ($terms as $term) {
                        $prefix = $field['show_hierarchy'] ? '[Term: ' . $tax_obj->labels->singular_name . '] ' : '';
                        $choices['term_' . $term->term_id] = array(
                            'label' => $prefix . $term->name,
                            'type'  => 'term',
                            'id'    => $term->term_id
                        );
                    }
                }
            }
        }
        
        return $choices;
    }
    
    /**
     * Render select field
     */
    private function render_select($field, $choices, $value) {
        $multiple = $field['multiple'] ? 'multiple' : '';
        $name = $field['multiple'] ? $field['name'] . '[]' : $field['name'];
        
        echo '<select name="' . esc_attr($name) . '" ' . $multiple . ' class="acf-cpt-tax-select">';
        
        if ($field['allow_null']) {
            echo '<option value="">- ' . __('Select', 'acf-cpt-taxonomy-select-combined') . ' -</option>';
        }
        
        foreach ($choices as $key => $choice) {
            $selected = in_array($key, $value) ? 'selected' : '';
            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($choice['label']) . '</option>';
        }
        
        echo '</select>';
    }
    
    /**
     * Render checkbox field
     */
    private function render_checkbox($field, $choices, $value) {
        echo '<div class="acf-checkbox-list">';
        
        foreach ($choices as $key => $choice) {
            $checked = in_array($key, $value) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="' . esc_attr($field['name']) . '[]" value="' . esc_attr($key) . '" ' . $checked . '> ';
            echo esc_html($choice['label']);
            echo '</label>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render radio field
     */
    private function render_radio($field, $choices, $value) {
        echo '<div class="acf-radio-list">';
        
        if ($field['allow_null']) {
            $checked = empty($value) ? 'checked' : '';
            echo '<label>';
            echo '<input type="radio" name="' . esc_attr($field['name']) . '" value="" ' . $checked . '> ';
            echo __('None', 'acf-cpt-taxonomy-select-combined');
            echo '</label>';
        }
        
        foreach ($choices as $key => $choice) {
            $checked = in_array($key, $value) ? 'checked' : '';
            echo '<label>';
            echo '<input type="radio" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' . $checked . '> ';
            echo esc_html($choice['label']);
            echo '</label>';
        }
        
        echo '</div>';
    }
    
    /**
     * Format value for API
     */
    public function format_value($value, $post_id, $field) {
        
        // Return null if empty
        if (empty($value)) {
            return null;
        }
        
        // Normalize to array
        if (!is_array($value)) {
            $value = array($value);
        }
        
        // Initialize return array
        $formatted = array(
            'post_objects'   => array(),
            'taxonomy_terms' => array()
        );
        
        // Parse values
        foreach ($value as $item) {
            if (strpos($item, 'post_') === 0) {
                $formatted['post_objects'][] = (int) str_replace('post_', '', $item);
            } elseif (strpos($item, 'term_') === 0) {
                $formatted['taxonomy_terms'][] = (int) str_replace('term_', '', $item);
            }
        }
        
        return $formatted;
    }
    
    /**
     * Validate value
     */
    public function validate_value($valid, $value, $field, $input) {
        // Add validation if needed
        return $valid;
    }
    
    /**
     * Update value
     */
    public function update_value($value, $post_id, $field) {
        
        // Allow for raw array input from frontend
        if (is_array($value) && isset($value['post_objects']) && isset($value['taxonomy_terms'])) {
            $new_value = array();
            
            if (!empty($value['post_objects'])) {
                foreach ($value['post_objects'] as $post_id_item) {
                    $new_value[] = 'post_' . $post_id_item;
                }
            }
            
            if (!empty($value['taxonomy_terms'])) {
                foreach ($value['taxonomy_terms'] as $term_id) {
                    $new_value[] = 'term_' . $term_id;
                }
            }
            
            return $new_value;
        }
        
        return $value;
    }
}

// Register field type
acf_register_field_type('acf_field_cpt_taxonomy_select');

