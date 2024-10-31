<?php

/**
 * Curl http request.
 */

namespace WasaioCurl;

/**
 * Generic http request.
 * Class WasaioCurl
 * @package WasaioCurl
 */
if (!class_exists('WasaioCurl')) {

    class WasaioCurl
    {

        /**
         * @param satring $url
         * @param array $post_data
         * @return array|encoded
         */
        static public function wasaio_http_request($url, $post_data)
        {
            $field_string = http_build_query($post_data);
            $response = wp_remote_post($url, array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'sslverify' => false,
                    'body' => $field_string,
                )
            );

            return wp_remote_retrieve_body($response);
        }

    }

}