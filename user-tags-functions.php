<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Register "User Tags" taxonomy for users
 */
function register_user_tags_taxonomy() {
    register_taxonomy('user_tags', 'user', array(
        'labels' => array(
            'name'          => 'User Tags',
            'singular_name' => 'User Tag',
            'menu_name'     => 'User Tags',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => false,
        'rewrite'           => false,
        'capabilities'      => array(
            'manage_terms' => 'edit_users',
            'edit_terms'   => 'edit_users',
            'delete_terms' => 'edit_users',
            'assign_terms' => 'edit_users'
        )
    ));
}
add_action('init', 'register_user_tags_taxonomy');

/**
 * Add "User Tags" management under Users menu
 */
function add_user_tags_admin_menu() {
    add_users_page('User Tags', 'User Tags', 'manage_options', 'edit-tags.php?taxonomy=user_tags');
}
add_action('admin_menu', 'add_user_tags_admin_menu');

/**
 * Add "User Tags" section in user profile
 */
function add_user_tags_metabox($user) {
    $terms = get_terms(array('taxonomy' => 'user_tags', 'hide_empty' => false));
    $user_tags = wp_get_object_terms($user->ID, 'user_tags', array('fields' => 'ids'));
    ?>
    <h3>User Tags</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_tags">Tags</label></th>
            <td>
                <select name="user_tags[]" multiple="multiple" style="width: 100%;">
                    <?php foreach ($terms as $term) { ?>
                        <option value="<?php echo $term->term_id; ?>" <?php selected(in_array($term->term_id, $user_tags)); ?>>
                            <?php echo $term->name; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_user_tags_metabox');
add_action('edit_user_profile', 'add_user_tags_metabox');

/**
 * Save User Tags when user profile is updated
 */
function save_user_tags($user_id) {
    if (!empty($_POST['user_tags'])) {
        wp_set_object_terms($user_id, $_POST['user_tags'], 'user_tags', false);
    }
}
add_action('personal_options_update', 'save_user_tags');
add_action('edit_user_profile_update', 'save_user_tags');

/**
 * Add "Filter by User Tags" dropdown in Users list
 */
function add_user_tags_filter() {
    global $pagenow;
    if ($pagenow === 'users.php') {
        $selected_tag = $_GET['user_tags'] ?? '';
        $terms = get_terms(array('taxonomy' => 'user_tags', 'hide_empty' => false));

        echo '<select name="user_tags">';
        echo '<option value="">Filter by user tags...</option>';
        foreach ($terms as $term) {
            echo '<option value="' . $term->term_id . '" ' . selected($selected_tag, $term->term_id, false) . '>' . $term->name . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" class="button" value="Filter">';
    }
}
add_action('restrict_manage_users', 'add_user_tags_filter');

/**
 * Modify users query to filter by selected User Tag
 */
function filter_users_by_tags($query) {
    if (is_admin() && !empty($_GET['user_tags'])) {
        $query->set('tax_query', array(array(
            'taxonomy' => 'user_tags',
            'field'    => 'term_id',
            'terms'    => $_GET['user_tags'],
        )));
    }
}
add_action('pre_get_users', 'filter_users_by_tags');

/**
 * Add "User Tags" column in Users table
 */
function add_user_tags_column($columns) {
    $columns['user_tags'] = __('User Tags');
    return $columns;
}
add_filter('manage_users_columns', 'add_user_tags_column');

/**
 * Display User Tags in Users table
 */
function show_user_tags_column_content($value, $column_name, $user_id) {
    if ($column_name === 'user_tags') {
        $terms = get_the_terms($user_id, 'user_tags');
        if (!empty($terms)) {
            return implode(', ', wp_list_pluck($terms, 'name'));
        }
        return '—';
    }
    return $value;
}
add_filter('manage_users_custom_column', 'show_user_tags_column_content', 10, 3);
?>
