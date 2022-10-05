<?php
class PostNordWoocommerceMetabox
{

    function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
        add_action('add_meta_boxes', [$this, 'wporg_add_custom_box']);
    }

    function wporg_add_custom_box()
    {
        $screens = ['shop_order'];
        foreach ($screens as $screen) {
            add_meta_box(
                'wporg_box_id',                 // Unique ID
                'PostNord Shipping Tracking',      // Box title
                [$this, 'wporg_custom_box_html'],  // Content callback, must be of type callable
                $screen                            // Post type
            );
        }
    }
    function wporg_custom_box_html($post)
    {
        if (metadata_exists('post', $post->ID, 'postnord_tracking_id'))
            $tracking_id = get_post_meta($post->ID, 'postnord_tracking_id')[0];
        else
            $tracking_id = '';

        require_once 'partials/' . $this->plugin_name . '-admin-meta-box.php';
    }
}
