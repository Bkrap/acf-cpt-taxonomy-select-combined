# ACF CPT & Taxonomy Select Combined

A custom Advanced Custom Fields (ACF) field type that allows you to combine post object selection and taxonomy term selection in a single field.

## Features

- **Combined Selection**: Select both posts and taxonomy terms in a single field
- **Multiple Appearance Options**: Choose from select dropdown, checkboxes, or radio buttons
- **Filter by Post Type**: Limit which post types are available for selection
- **Filter by Taxonomy**: Limit which taxonomies are available for selection
- **Show Hierarchy**: Optionally display whether an item is a post or taxonomy term in the label
- **Structured Return Value**: Returns organized array with separate post IDs and term IDs

## Requirements

- WordPress 5.0 or higher
- Advanced Custom Fields (ACF) 5.0 or higher
- PHP 7.4 or higher

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The field type will be available when creating/editing ACF field groups

## Field Settings

### Filter by Post Type
Select which post types should be available for selection. Leave empty to include all post types.

### Allow Taxonomy
Select which taxonomies should be available for selection. Leave empty to include all taxonomies.

### Appearance
Choose how the field appears:
- **Select**: Dropdown menu (supports multiple selection)
- **Checkbox**: Checkboxes for multiple selection
- **Radio Buttons**: Radio buttons for single selection

### Allow Null
(Select & Radio only) Allow an empty value to be selected.

### Select Multiple
(Select only) Allow multiple values to be selected.

### Show Hierarchy in Label
Display whether the item is a post or taxonomy term in the label. For example:
- `[Post: Page] About Us`
- `[Term: Category] News`

## Return Value

The field returns a structured array with separate arrays for post objects and taxonomy terms:

```php
array(
    'post_objects' => array(1, 2, 3),      // Array of post IDs
    'taxonomy_terms' => array(10, 11, 12)  // Array of term IDs
)
```

## Usage Examples

### Basic Usage

```php
// Get the field value
$value = get_field('my_combined_field');

// Check if there are any selected posts
if (!empty($value['post_objects'])) {
    foreach ($value['post_objects'] as $post_id) {
        $post = get_post($post_id);
        echo $post->post_title . '<br>';
    }
}

// Check if there are any selected terms
if (!empty($value['taxonomy_terms'])) {
    foreach ($value['taxonomy_terms'] as $term_id) {
        $term = get_term($term_id);
        echo $term->name . '<br>';
    }
}
```

### Display Selected Items with Links

```php
$value = get_field('my_combined_field');

if ($value) {
    echo '<ul>';
    
    // Display posts
    if (!empty($value['post_objects'])) {
        foreach ($value['post_objects'] as $post_id) {
            echo '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
        }
    }
    
    // Display terms
    if (!empty($value['taxonomy_terms'])) {
        foreach ($value['taxonomy_terms'] as $term_id) {
            $term = get_term($term_id);
            if ($term && !is_wp_error($term)) {
                echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
            }
        }
    }
    
    echo '</ul>';
}
```

### Conditional Logic Based on Selection

```php
$value = get_field('my_combined_field');

// Check if specific post is selected
if (!empty($value['post_objects']) && in_array(123, $value['post_objects'])) {
    echo 'Post with ID 123 is selected!';
}

// Check if specific term is selected
if (!empty($value['taxonomy_terms']) && in_array(456, $value['taxonomy_terms'])) {
    echo 'Term with ID 456 is selected!';
}

// Count total selections
$total_count = count($value['post_objects']) + count($value['taxonomy_terms']);
echo 'Total items selected: ' . $total_count;
```

### WP_Query with Selected Posts

```php
$value = get_field('my_combined_field');

if (!empty($value['post_objects'])) {
    $args = array(
        'post__in' => $value['post_objects'],
        'post_type' => 'any',
        'orderby' => 'post__in',
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Display post content
        }
        wp_reset_postdata();
    }
}
```

### Get Posts by Selected Terms

```php
$value = get_field('my_combined_field');

if (!empty($value['taxonomy_terms'])) {
    $args = array(
        'post_type' => 'post',
        'tax_query' => array(
            array(
                'taxonomy' => 'category', // Change to your taxonomy
                'field'    => 'term_id',
                'terms'    => $value['taxonomy_terms'],
            ),
        ),
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Display post content
        }
        wp_reset_postdata();
    }
}
```

## Programmatic Field Creation

```php
acf_add_local_field_group(array(
    'key' => 'group_combined_field',
    'title' => 'Combined Field Example',
    'fields' => array(
        array(
            'key' => 'field_combined',
            'label' => 'Select Posts and Terms',
            'name' => 'my_combined_field',
            'type' => 'cpt_taxonomy_select',
            'post_type' => array('post', 'page'),
            'taxonomy' => array('category', 'post_tag'),
            'field_type' => 'select',
            'multiple' => 1,
            'allow_null' => 1,
            'show_hierarchy' => 1,
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page',
            ),
        ),
    ),
));
```

## Support

For issues, questions, or feature requests, please contact Hence Creative.

## Changelog

### 1.0.0
- Initial release
- Combined post object and taxonomy term selection
- Support for select, checkbox, and radio button appearances
- Structured return value with separate arrays for posts and terms

