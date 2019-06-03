<?php
namespace Orbis;

use Exception;

/**
 * https://secure.php.net/manual/en/function.password-hash.php
 * https://secure.php.net/manual/en/function.password-verify.php
 *
 * Class Password
 * @package Orbis
 */
class Password
{
    /**
     * @param string $password
     *
     * @return string
     */
    public static function encrypt(string $password) : string {
        return password_hash($password, PASSWORD_DEFAULT );
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function verify(string $password, string $hash) : bool {
        return password_verify($password, $hash);
    }

    /**
     * @param int $nbBytes
     *
     * @return string
     */
    private static function getRandomBytes($nbBytes = 32) : string {
        try {
            $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
            return $bytes;
        } catch (Exception $e) {
            JsonResponse::error('Could not generate password.');
        }

        return '';
    }

    /**
     * @param $length
     *
     * @return string
     */
    public static function generate($length) : string {
        return substr(preg_replace("/[^a-zA-Z0-9]/", "", base64_encode(self::getRandomBytes($length+1))),0,$length);
    }
}