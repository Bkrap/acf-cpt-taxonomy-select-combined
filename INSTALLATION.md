# Installation & Activation Guide

## Quick Start 

### Step 1: Install the Plugin

1. Navigate to WordPress admin dashboard
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the `acf-cpt-taxonomy-select-combined` folder (or zip file)
4. Click **Install Now**
5. Click **Activate Plugin**

**OR** if you already have access to the server:

1. Copy the entire `acf-cpt-taxonomy-select-combined` folder to `/wp-content/plugins/`
2. Go to WordPress admin dashboard
3. Navigate to **Plugins**
4. Find "ACF CPT & Taxonomy Select Combined" and click **Activate**

### Step 2: Verify Installation

After activation, you should see:
- No error messages (if ACF is installed)
- The new field type "CPT & Taxonomy Select" available in ACF field groups

### Step 3: Create Your First Field

1. Go to **Custom Fields → Field Groups**
2. Create a new field group or edit an existing one
3. Add a new field
4. Under **Field Type**, select "CPT & Taxonomy Select"
5. Configure the field settings:
   - **Filter by Post Type**: Choose which post types to include (e.g., Posts, Pages)
   - **Allow Taxonomy**: Choose which taxonomies to include (e.g., Categories, Tags)
   - **Appearance**: Select how you want the field to display (Select, Checkbox, or Radio)
   - **Allow Null**: Enable if you want to allow empty selections
   - **Select Multiple**: Enable for multiple selections (Select dropdown only)
   - **Show Hierarchy in Label**: Enable to show whether items are posts or terms

6. Set the location rules for where this field should appear
7. Click **Publish** or **Update**

### Step 4: Test the Field

1. Go to a post/page where the field is configured to appear
2. You should see your combined field with both posts and taxonomy terms
3. Make some selections and save
4. View the post/page to verify selections are saved

## Requirements

- ✅ WordPress 5.0 or higher
- ✅ Advanced Custom Fields (ACF) 5.0 or higher  
- ✅ PHP 7.4 or higher

## Troubleshooting

### "Plugin requires Advanced Custom Fields" error

**Problem**: You see an error message that ACF is required

**Solution**: 
1. Install and activate Advanced Custom Fields plugin first
2. You can download ACF from: https://wordpress.org/plugins/advanced-custom-fields/
3. Or install ACF PRO if you have a license

### Field type not showing up

**Problem**: The "CPT & Taxonomy Select" field type doesn't appear in the field type dropdown

**Solution**:
1. Verify ACF is installed and activated
2. Deactivate and reactivate the plugin
3. Clear any caching plugins you might have
4. Check for JavaScript errors in browser console
5. Verify file permissions are correct (644 for files, 755 for directories)

### Selected values not saving

**Problem**: When you select items and save, they don't persist

**Solution**:
1. Check browser console for JavaScript errors
2. Verify you have proper permissions to save the post/page
3. Check if any other plugins are conflicting
4. Try disabling other plugins temporarily to isolate the issue

### Return value is empty or null

**Problem**: When using `get_field()`, it returns empty or null

**Solution**:
1. Make sure you've saved the post with selections
2. Verify you're using the correct field name
3. Check if you're in the correct context (proper post ID)
4. Try using `get_field('field_name', $post_id)` with explicit post ID

## Usage in Code

Once the field is set up, you can retrieve values in your theme:

```php
$selected = get_field('your_field_name');

if ($selected) {
    // Access post IDs
    foreach ($selected['post_objects'] as $post_id) {
        echo get_the_title($post_id);
    }
    
    // Access term IDs
    foreach ($selected['taxonomy_terms'] as $term_id) {
        $term = get_term($term_id);
        echo $term->name;
    }
}
```

See `README.md` for more detailed usage examples.

## Uninstallation

If you need to remove the plugin:

1. Go to **Plugins** in WordPress admin
2. Deactivate "ACF CPT & Taxonomy Select Combined"
3. Click **Delete**
4. Confirm deletion

**Note**: Field values will remain in the database but won't be accessible without the plugin. If you reinstall later, the values will be available again.

## Support

For questions, issues, or feature requests, please contact bruno.krapljan@rise2.studio or refer to the README.md file.

## File Structure

```
acf-cpt-taxonomy-select-combined/
├── acf-cpt-taxonomy-select-combined.php  (Main plugin file)
├── includes/
│   └── class-acf-field-cpt-taxonomy-select.php  (Field type class)
├── assets/
│   └── css/
│       └── admin.css  (Admin styling)
├── README.md  (Documentation)
├── INSTALLATION.md  (This file)
└── example-usage.php  (Usage examples)
```

