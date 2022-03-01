<?php

/*
Plugin Name: WooCities
Plugin URI: https://ncdigital.net
Description: Custom cities in dropdown for WooCommerce Shipping/Billing Address Form
Version: 1.0
Author: Adalberto Hernandez Vega
Author URI: https://ncdigital.net
License: GPL2
*/

defined('ABSPATH') || exit;

add_action('admin_menu', 'ahv_woocities_admin_settings');

function ahv_woocities_admin_settings()
{
    add_menu_page(
        'Custom Cities Configuration',
        'WooCities',
        'manage_options',
        'woocities-admin-menu',
        'ahv_woocities_config_page',
        'dashicons-location-alt',
        200
    );
}

/* Render Admin configuration page */
function ahv_woocities_config_page()
{
    global $woocities_enabled, $woocities_cities, $woocities_billing;

    $woocities_enabled = get_option('ahv_woocities_enabled', 'No');
    $woocities_cities = get_option('ahv_woocities_cities', 'none');
    $woocities_billing = get_option('ahv_woocities_billing', 'No');

    $woocities_enabled_checked = trim($woocities_enabled) === 'Yes' ? 'checked' : 'class';
    $woocities_billing_checked = trim($woocities_billing) === 'Yes' ? 'checked' : 'class';

    if (array_key_exists('submit', $_POST)) {
        update_option('ahv_woocities_cities', $_POST['woocities_cities']);

        if (isset($_POST['woocities_enabled'])) {
            update_option('ahv_woocities_enabled', $_POST['woocities_enabled']);
        } else {
            update_option('ahv_woocities_enabled', 'No');
        }
        if (isset($_POST['woocities_billing'])) {
            update_option('ahv_woocities_billing', $_POST['woocities_billing']);
        } else {
            update_option('ahv_woocities_billing', 'No');
        }

        ?>
        <div id="setting-error-settings_updated"
             class="updated settings-error notice is-dismissible"
             style="background-color: #ddd;">
            <p><strong><?= __('Settings have been saved.', 'woocities') ?></strong></p>
        </div>
        <?php
    }
    ?>
    <div class="wrap">
        <h2><?= __('Update Info — WooCommerce Custom Cities', 'woocities') ?></h2>
        <form method="post" action="">
            <fieldset id="woocities" name="woocities"
                      style="display: block;
                      margin-inside: 2px;
                      margin-right: 2px;
                      padding: 0.35em 0.75em 0.625em;
                      border: 1px solid #202020;">
                <legend><?= __('Parameters', 'woocities') ?></legend>
                <p>
                    <label for="woocities_enabled"><strong><?= __('Enabled', 'woocities') ?></strong>&nbsp;
                        <input type="checkbox"
                               name="woocities_enabled"
                               id="woocities_enabled"
                               value="Yes"
                                <?= $woocities_enabled_checked ?>><br>
                    </label>
                </p>
                <p>
                    <label for="woocities_cities"><strong><?= __('Cities', 'woocities') ?></strong><br>
                        <textarea name="woocities_cities"
                                  id="woocities_cities"
                                  cols="80" rows="3"
                                  style="resize: both;"><?= $woocities_cities ?></textarea>
                        <br><?= __('Comma separated list of cities to show.', 'woocommerce') ?>
                    </label>
                </p>
                <p>
                    <label for="woocities_billing"><strong><?= __('Also Replace in Billing Address', 'woocities') ?></strong>&nbsp;
                        <input type="checkbox"
                               name="woocities_billing"
                               id="woocities_billing"
                               value="Yes"
                                <?= $woocities_billing_checked ?>><br>
                    </label>
                </p>
            </fieldset>
            <p>
                <input type="submit" name="submit"
                       id="submit" value="<?= __('Save', 'woocities') ?>"
                       class="button button-primary"
                >
            </p>
        </form>
    </div>
    <?php
}


/**
 * Change the checkout city field to a dropdown field.
 * @param $fields
 * @return array
 */
function ahv_woocities_change_fields($fields)
{
    $woocities_cities = get_option('ahv_woocities_cities', 'none');
    $woocities_billing = get_option('ahv_woocities_billing', 'No');

    $city_name = explode(',', $woocities_cities);
    asort($city_name);
    $city_options = array();
    foreach ($city_name as $city) {
        $city = trim($city);
        $city_options[$city] = $city;
    }

    $city_args = wp_parse_args(array(
            'type' => 'select',
            'options' => $city_options,
    ), $fields['shipping']['shipping_city']);

    $fields['shipping']['shipping_city'] = $city_args;
    if ($woocities_billing === 'Yes') {
        // Also change for billing field
        $fields['billing']['billing_city'] = $city_args;
    }

    return $fields;

}


$woocities_enabled = get_option('ahv_woocities_enabled', 'No');

if ($woocities_enabled === 'Yes') {
    add_filter('woocommerce_checkout_fields', 'ahv_woocities_change_fields');
}
