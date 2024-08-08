<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode to display the form
function blog_post_submission_form()
{
    ob_start();

    // Check if form was submitted successfully
    if (isset($_GET['submission']) && $_GET['submission'] == 'success') {
        echo '<div class="blog-post-form-success">' . __('Your blog post submission was successful!', 'blog_post_submission') . '</div>';
    }
?>

    <!-- frontend form -->
    <div class="blog-post-form-container">

        <!-- Form title -->
        <h2 class="form-title"><?php _e('Post your Blog', 'blog_post_submission'); ?></h2>

        <form action="" method="post" id="blog_post_form" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="blog_post_form">

            <label for="post_title"><?php _e('Title:', 'blog_post_submission'); ?></label>
            <input type="text" id="post_title" name="post_title" required>

            <label for="post_content"><?php _e('Content:', 'blog_post_submission'); ?></label>
            <textarea id="post_content" name="post_content" rows="10" cols="50" required></textarea>

            <label for="post_image"><?php _e('Featured Image:', 'blog_post_submission'); ?></label>
            <input type="file" id="post_image" name="post_image" accept="image/*" required>


            <input type="submit" name="submit_post" value="<?php _e('Submit', 'blog_post_submission'); ?>">
        </form>

    </div>

<?php

    return ob_get_clean();
}

add_shortcode('blog_post_submission_form', 'blog_post_submission_form');

function blog_post_handle_form_submission()
{
    if (isset($_POST['form_type'])) {
        if ($_POST['form_type'] == 'blog_post_form') {
            if (isset($_POST['submit_post']) && isset($_POST['post_title']) && isset($_POST['post_content'])) {
                $post_title = sanitize_text_field($_POST['post_title']);
                $post_content = sanitize_textarea_field($_POST['post_content']);

                // Create a new post of type 'post'
                $new_post = array(
                    'post_title'   => $post_title,
                    'post_content' => $post_content,
                    'post_status'  => 'publish',
                    'post_type'    => 'post'
                );

                // Insert the post into the database
                $post_id = wp_insert_post($new_post);

                // Handle the image upload and set it as the featured image
                if (!is_wp_error($post_id) && !empty($_FILES['post_image']['name'])) {
                    // Ensure the function is available
                    if (!function_exists('wp_handle_upload')) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                    }

                    $file = $_FILES['post_image'];
                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if ($upload && !isset($upload['error'])) {
                        $attachment = array(
                            'post_mime_type' => $upload['type'],
                            'post_title'     => sanitize_file_name($upload['file']),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                            set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                }

                // Redirect to the same page with a success parameter
                wp_redirect(add_query_arg('submission', 'success', wp_get_referer()));
                exit;
            } else {
                echo '<div class="blog-post-form-error">' . __('There was an error creating the post. Please try again.', 'blog_post_submission') . '</div>';
            }
        }
    }
}
add_action('template_redirect', 'blog_post_handle_form_submission');
