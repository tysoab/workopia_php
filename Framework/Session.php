<?php

namespace Framework;

class Session
{

  /**
   * Session Start function
   *
   * @return void
   */
  public static function start()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * set session key and value
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * get session key
   *
   * @param string $key
   * @param mixed $default
   * @return void
   */
  public static function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  /**
   * check if session key exist
   *
   * @param string $key
   * @return boolean
   */
  public static function has($key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Clear session by $key
   * 
   * @param string  $key
   * @return  void
   */

  public static function clear($key)
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }

  /**
   * clear all session data
   * 
   * return void
   */
  public static function clearAll()
  {
    session_unset();
    session_destroy();
  }

  /**
   * Set a flash message
   * 
   * @param string $key
   * @param string $message
   * @return void
   */
  public static function setFlashMessage($key, $message)
  {
    self::set('flash_' . $key, $message);
  }

  /**
   * Get flash message
   * 
   * @param string  $key
   * @param mixed $default
   * @return string
   */
  public static function getFlashMessage($key, $default = null)
  {
    $message = self::get('flash_' . $key, $default);
    self::clear('flash_' . $key);
    return $message;
  }
}
