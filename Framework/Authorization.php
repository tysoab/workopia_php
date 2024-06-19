<?php

namespace Framework;

use Framework\Session;

class Authorization
{
  /**
   * check if current logged in user owns a resource
   * 
   * @param int $resource
   * @return bool
   */
  public static function isOwner($resourceId)
  {
    $sessionUser = Session::get('user');

    if ($sessionUser !== null && isset($sessionUser['id'])) {
      // convert id to int
      $sessionUserId = (int) $sessionUser['id'];
      return $sessionUserId === $resourceId;
    }

    return false;
  }
}
