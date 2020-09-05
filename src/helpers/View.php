<?php

namespace helpers;

/**
 * Helper class for rendering template
 *
 * @copyright  Copyright (c) Tobias Zeising (http://www.aditu.de)
 * @license    GPLv3 (https://www.gnu.org/licenses/gpl-3.0.html)
 * @author     Tobias Zeising <tobias.zeising@aditu.de>
 */
class View {
    /** @var string current base url */
    public $base = '';

    /**
     * set global view vars
     */
    public function __construct() {
        $this->base = $this->getBaseUrl();
    }

    /**
     * Returns the base url of the page. If a base url was configured in the
     * config.ini this will be used. Otherwise base url will be generated by
     * globale server variables ($_SERVER).
     */
    public static function getBaseUrl() {
        $base = '';

        // base url in config.ini file
        if (strlen(trim(\F3::get('base_url'))) > 0) {
            $base = \F3::get('base_url');
            $length = strlen($base);
            if ($length > 0 && substr($base, $length - 1, 1) !== '/') {
                $base .= '/';
            }
        } else { // auto generate base url
            $protocol = 'http';
            if ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (isset($_SERVER['HTTP_HTTPS']) && $_SERVER['HTTP_HTTPS'] === 'https')) {
                $protocol = 'https';
            }

            // check for SSL proxy
            if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && isset($_SERVER['HTTP_X_FORWARDED_HOST'])
            && ($_SERVER['HTTP_X_FORWARDED_SERVER'] === $_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $subdir = '/' . preg_replace('/\/[^\/]+$/', '', $_SERVER['PHP_SELF']);
                $host = $_SERVER['HTTP_X_FORWARDED_SERVER'];
            } else {
                $subdir = \F3::get('BASE');
                $host = $_SERVER['SERVER_NAME'];
            }

            $port = '';
            if (($protocol === 'http' && $_SERVER['SERVER_PORT'] != '80') ||
                ($protocol === 'https' && $_SERVER['SERVER_PORT'] != '443')) {
                $port = ':' . $_SERVER['SERVER_PORT'];
            }
            //Override the port if nginx is the front end and the traffic is being forwarded
            if (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                $port = ':' . $_SERVER['HTTP_X_FORWARDED_PORT'];
            }

            $base = $protocol . '://' . $host . $port . $subdir . '/';
        }

        return $base;
    }

    /**
     * send error message
     *
     * @param string $message
     *
     * @return void
     */
    public function error($message) {
        header('HTTP/1.0 400 Bad Request');
        die($message);
    }

    /**
     * send error message as json string
     *
     * @param mixed $data
     *
     * @return void
     */
    public function jsonError($data) {
        header('Content-type: application/json');
        $this->error(json_encode($data));
    }

    /**
     * send success message as json string
     *
     * @param mixed $data
     *
     * @return void
     */
    public function jsonSuccess($data) {
        header('Content-type: application/json');
        die(json_encode($data));
    }
}
