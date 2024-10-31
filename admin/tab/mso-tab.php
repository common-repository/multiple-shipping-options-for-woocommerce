<?php

//namespace MsoTab;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use MsoSettings\MsoSettings;

if (!class_exists('MsoTab')) {
    /**
     * Tabs show on admin side.
     * Class MsoTab
     */
    class MsoTab extends WC_Settings_Page
    {
        public function mso_tab_init()
        {
            $this->id = 'mso';
            add_filter('woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50);
            add_action('woocommerce_sections_' . $this->id, [$this, 'output_sections']);
            add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
            add_action('woocommerce_settings_save_' . $this->id, [$this, 'save']);
        }

        /**
         * Setting Tab For Woocommerce
         * @param $settings_tabs
         * @return string
         */
        public function add_settings_tab($settings_tabs)
        {
            $settings_tabs[$this->id] = __('Multiple Shipping Options for WooCommerce', 'woocommerce-settings-mso');
            return $settings_tabs;
        }

        /**
         * Setting Sections
         * @return array
         */
        public function get_sections()
        {
            $sections = array(
                '' => __('Settings', 'woocommerce-settings-mso'),
                'mso-packaging' => __('Pallets & Boxes', 'woocommerce-settings-mso'),
                'mso-logs' => __('Logs', 'woocommerce-settings-mso'),
            );

            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }


        /**
         * Display all pages on wc settings tabs
         * @param $section
         * @return array
         */
        public function get_settings($section = null)
        {
            switch ($section) {
                case 'mso-logs':
                    \MsoLogs\MsoLogs::mso_settings();
                    $settings = [];
                    break;
                case 'mso-packaging':
                    \MsoPackaging\MsoPackaging::mso_settings();
                    $settings = [];
                    break;
                default:
                    $settings = MsoSettings::mso_settings();
                    break;

            }
            return apply_filters('woocommerce-settings-mso', $settings, $section);
        }

        /**
         * WooCommerce Settings Tabs
         * @global $current_section
         */
        public function output()
        {
            global $current_section;
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::output_fields($settings);
        }

        /**
         * Woocommerce Save Settings
         * @global $current_section
         */
        public function save()
        {
            global $current_section;
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::save_fields($settings);
        }
    }

    $tab = new MsoTab();
    return $tab->mso_tab_init();
}
