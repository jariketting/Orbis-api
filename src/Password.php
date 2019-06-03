<?php
namespace Orbis;

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
}