<?php

namespace MsoPrerequisites;

use MsoConnection\MsoConnection;
use MsoProductAjax\MsoProductAjax;

/**
 * Versions compatibility.
 * Class MsoPrerequisites
 * @package MsoPrerequisites
 */
class MsoPrerequisites
{
    private $plugin_name;
    private $php_version;
    private $wp_version;
    private $wc_version;
    private $errors;
    private $warnings;

    /**
     * @param string $plugin_name
     * @param string $php_version
     * @param string $wp_version
     * @param string|null $wc_version
     * @return void
     */
    private function __construct($plugin_name, $php_version, $wp_version, $wc_version)
    {
        $this->plugin_name = $plugin_name;
        $this->php_version = $php_version;
        $this->wp_version = $wp_version;
        $this->wc_version = $wc_version;

        // Hook admin_notices always since errors can be added lately
        add_action('admin_notices', array($this, '_mso_show_notices'));
    }

    /**
     * @param string $plugin_name
     * @param string $php_version
     * @param string $wp_version
     * @param string|null $wc_version
     * @return void
     */
    public static function mso_check_prerequisites($plugin_name,
                                                      $php_version,
                                                      $wp_version,
                                                      $wc_version)
    {
        $instance = new self($plugin_name, $php_version, $wp_version, $wc_version);
        return $instance->mso_check_prerequisites_();
    }

    /**
     * Show notices
     */
    public function _mso_show_notices()
    {
        $this->mso_show_notices($this->errors, 'error');
        $this->mso_show_notices($this->warnings, 'warning');
    }

    /**
     * Check for errors.
     */
    public function _mso_check_woocommerce_version()
    {
        $wc_version = defined('WC_VERSION') ? WC_VERSION : null;

        if (!isset($wc_version) || version_compare($wc_version, $this->wc_version, '<')) {
            $this->errors[] =
                'You are running an outdated WooCommerce version".(isset($wc_version) ? " ".$wc_version : null).".
                 {plugin_name} requires WooCommerce {wc_version}+.
                 Consider updating to a modern WooCommerce version.';
            return;
        }
    }

    /**
     * @return void
     */
    public function mso_check_prerequisites_()
    {
        global $wp_version;
        $this->errors = [];
        $this->warnings = [];

        if (version_compare($phpv = PHP_VERSION, $this->php_version, '<')) {
            $this->errors[] =
                "You are running an outdated PHP version {$phpv}. 
                 {plugin_name} requires PHP {php_version}+. 
                 Contact your hosting support to switch to a newer PHP version.";

        }

        if (isset($wp_version) && version_compare($wp_version, $this->wp_version, '<')) {
            $this->errors[] =
                "You are running an outdated WordPress version {$wp_version}.
                 {plugin_name} is tested with WordPress {wp_version}+.
                 Consider updating to a modern WordPress version.";
        }

        if (isset($this->wc_version)) {
            if (!self::is_woocommerce_active()) {
                $this->errors[] =
                    'WooCommerce is not active. 
                     {plugin_name} requires WooCommerce to be installed and activated.';
            } else {
                if (defined('WC_VERSION') || did_action('woocommerce_loaded')) {
                    $this->_mso_check_woocommerce_version();
                } else {
                    add_action('woocommerce_loaded', array($this, '_mso_check_woocommerce_version'));
                }
            }
        }

        if (empty($this->errors)) {
            // TODO Start to load all classes
            new MsoConnection();
            new MsoProductAjax();
        }

        return $this->errors;
    }

    /**
     * Show notices
     * @param array $notices
     * @param $kind
     */
    public function mso_show_notices($notices, $kind)
    {
        if ($notices) {
            foreach ($notices as $mso_dismiss_id => $notice): ?>
                <?php
                $mso_dismiss_class = null;
                $mso_dismiss_attr = null;
                if (is_string($mso_dismiss_id) && !empty($mso_dismiss_id)) {
                    $mso_dismiss_class = 'is-dismissible';
                    $mso_dismiss_attr = 'data-dismissible=' . esc_html($mso_dismiss_id);
                }
                ?>
                <div class="notice notice-<?php echo esc_html($kind) ?> <?php echo esc_attr($mso_dismiss_class) ?>"
                    <?php echo esc_attr($mso_dismiss_attr) ?>
                >
                    <?php
                    $notice = strtr($notice, array(
                        '{plugin_name}' => $this->plugin_name,
                        '{php_version}' => $this->php_version,
                        '{wp_version}' => $this->wp_version,
                        '{wc_version}' => $this->wc_version,
                    ));
                    ?>
                    <p><?php echo esc_html($notice) ?></p>
                </div>
            <?php endforeach; ?>
            <?php
        }
    }

    /**
     * Condition check WooCommerce is active or not.
     * @return bool
     */
    public static function is_woocommerce_active()
    {
        static $active_plugins;

        if (!isset($active_plugins)) {
            $active_plugins = (array)get_option('active_plugins', []);
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_site_wide_plugins', []));
            }
        }

        return
            in_array('woocommerce/woocommerce.php', $active_plugins) ||
            array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }
}