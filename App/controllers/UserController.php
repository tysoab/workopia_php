<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
  protected $db;

  public function __construct()
  {
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  /**
   * User login
   *
   * @return void
   */
  public function login()
  {
    loadView('users/login');
  }

  /**
   * Create user
   *
   * @return void
   */
  public function create()
  {
    loadView('users/create');
  }

  /**
   * Store user to db
   * 
   * @return void
   */

  public function store()
  {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    $errors = [];

    // validation
    if (!Validation::email($email)) {
      $errors['email'] = 'Please enter a valid email';
    }

    if (!Validation::string($name, 2, 50)) {
      $errors['name'] = 'Name must be between 2 and 50 characters';
    }

    if (!Validation::string($password, 6, 50)) {
      $errors['password'] = 'Password must be at least 6 characters';
    }

    if (!Validation::match($password, $password_confirmation)) {
      $errors['password_confirmation'] = 'Password not matched';
    }



    if (!empty($errors)) {
      loadView('users/create', [
        'errors' => $errors,
        'user' => [
          'name' => $name,
          'email' => $email,
          'city' => $city,
          'state' => $state,
        ],
      ]);

      exit;
    }

    // check if email already exist

    $param = ['email' => $email];

    $user = $this->db->query('SELECT * FROM users WHERE email = :email', $param)->fetch();

    if ($user) {
      $errors['email'] = 'Email already exist';
      loadView('users/create', ['errors' => $errors]);
      exit;
    }

    $params = [
      'name' => $name,
      'email' => $email,
      'city' => $city,
      'state' => $state,
      'password' => password_hash($password, PASSWORD_DEFAULT),
    ];

    $userQuery = $this->db->query("INSERT INTO users (name, email, city, state, password) VALUES(:name, :email, :city, :state, :password)", $params);
    if ($userQuery) {

      // get user id
      $userID = $this->db->conn->lastInsertId();

      // set session
      Session::set('user', [
        'id' => $userID,
        'name' => $name,
        'email' => $email,
        'city' => $city,
        'state' => $state,
      ]);
      redirect('/auth/login');
      exit;
    }
  }

  // logout user

  public function logout()
  {
    Session::clearAll();

    $param = session_get_cookie_params();

    setcookie('PHPSESSID', '', time() - 864000, $param['path'], $param['domain']);

    redirect('/');
  }

  /**
   * authenticate user with email and password
   * 
   * @return void
   */
  public function authenticate()
  {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (!Validation::email($email)) {
      $errors['email'] = 'Please enter a valid email';
    }

    if (!Validation::string($password, 6, 50)) {
      $errors['password'] = 'Password must be at least 6 characters';
    }

    // check if error is not empty
    if (!empty($errors)) {
      loadView('users/login', ['errors' => $errors, 'user' => ['email' => $email]]);
      exit;
    }

    // check for email
    $params = ['email' => $email];

    $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

    if (!$user) {
      $errors['email'] = 'Incorrect credentials';
      loadView('users/login', ['errors' => $errors, 'user' => ['email' => $email]]);
      exit;
    }

    // check if password is correct
    if (!password_verify($password, $user->password)) {
      $errors['email'] = 'Incorrect credentials';
      loadView('users/login', ['errors' => $errors, 'user' => ['email' => $email]]);
      exit;
    }

    // set user session
    Session::set('user', [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'city' => $user->city,
      'state' => $user->state,
    ]);
    redirect('/');
    exit;
  }
}
