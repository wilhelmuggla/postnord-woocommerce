<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://owlhub.se
 * @since      1.0.0
 *
 * @package    Postnord_Woocommerce
 * @subpackage Postnord_Woocommerce/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="inside">
    <ul class="order_actions">
        <?php
        if ($tracking_id == '') :
            echo 'No shipping label generated';
        else :
        ?>
            <li class="wide">
                <a href="<?php echo wp_upload_dir()['baseurl'] . '/labels/' . $tracking_id; ?>.pdf" target="_blank">PDF</a>
            </li>
            <li class="wide">
                <a href="https://attracking.postnord.com/se/?id=<?php echo $tracking_id; ?>" target="_blank"><?php echo $tracking_id; ?></a>
            </li>
        <?php endif; ?>
    </ul>
</div>