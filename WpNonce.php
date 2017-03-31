<?php

namespace mndev\WpNonce;

/**
 * Class WpNonce
 * Ein Objektorientierter Ansatz um Wordpress Nonces zu nutzen.
 *
 * @package mndev\WpNonce
 * @version 0.0.1
 **/

class WpNonce
{
    protected static $_instance = null;

    private $action;

    private function __construct() {}

    /**
     * __clone verboten
     */
    private function __clone() {}

    /**
     * Wakeup unterbinden
     *
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Singleton
     *
     * Einzige Instanz die einen Zugriff auf die Methoden ermöglicht.
     *
     * @return WpNonce|null
     */
    public static function getInstance($action)
    {
        self::setAction($action);

        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * @return string
     */
    public function setAction($action)
    {
        return $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Einen Nonce für die weitere Verwendung erzeugen
     *
     * Codex-Dokumentation:
     * @see  https://codex.wordpress.org/Function_Reference/wp_create_nonce
     *
     * @return bool|string Nonce
     */
    public function createNonce()
    {
        if (!function_exists('wp_create_nonce')) {
            return false;
        }

        return wp_create_nonce($this->action);
    }

    /**
     * Überprüfung eines Nonce
     *
     * Codex-Dokumentation:
     * @see  https://codex.wordpress.org/Function_Reference/wp_verify_nonce
     *
     * @param $nonce
     * @return bool
     */
    public function verifyNonce($nonce)
    {
        if (!function_exists('wp_verify_nonce')
                || empty($nonce)
                || !is_string($nonce)
            ) {
                return false;
            }

        return wp_verify_nonce($nonce, $this->action);
    }

    /**
     * Erzeugt ein Nonce HTML Feld für die Nutzung im Formular
     *
     * Codex-Dokumentation:
     * @see  https://codex.wordpress.org/Function_Reference/wp_nonce_field
     *
     * @param $name
     * @param bool $referer
     *
     * @return bool | html
     */
    public function createNonceField($name, $referer = true)
    {
        if (!function_exists('wp_nonce_field')
                || empty($name)
                || !is_string($name)
            ) {
                return false;
            }

        return wp_nonce_field($this->action, $name, $referer, false);
    }

    /**
     * Erzeugt aus einer "normalen" URL eine Nonce enthaltende URL
     *
     * Codex-Dokumentation:
     * @see  https://codex.wordpress.org/Function_Reference/wp_nonce_url
     *
     * @param $actionUrl
     *
     * @param string $name
     * @return bool
     */
    public function createNonceUrl($actionUrl, $name = '_wpnonce')
    {
        if (!function_exists('wp_nonce_url')
                || empty($actionUrl)
                || !is_string($actionUrl)
            ) {
                return false;
            }

        return wp_nonce_url($actionUrl, $this->action, $name);
    }

    /**
     * Überprüft ob die übergebene URL eine valide URL mit Nounce ist (Admin)
     *
     * Codex-Dokumentation:
     * @see https://codex.wordpress.org/Function_Reference/check_admin_referer
     *
     * @param string $query_arg
     * @return bool
     */
    public function checkAdminReferer($query_arg = '_wpnonce')
    {
        if (!function_exists('check_admin_referer')) {
            return false;
        }

        return check_admin_referer($this->action, $query_arg);
    }

    /**
     * Überprüft ob die übergebene URL eine valide URL mit Nounce ist (Ajax)
     *
     * Codex-Dokumentation:
     * @see https://codex.wordpress.org/Function_Reference/check_admin_referer
     *
     * @param string $query_arg
     * @param $die (whether to die if the nonce is invalid)
     *
     * @return bool
     */
    public function checkAjaxReferer($query_arg = '_wpnonce', $die)
    {
        if (!function_exists('check_ajax_referer')) {
            return false;
        }

        return check_ajax_referer($this->action, $query_arg, $die);
    }
}