<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

class ListingController
{

  protected $db;
  public function __construct()
  {
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  public function index()
  {
    $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC")->fetchAll();
    // $listings = $this->db->query("SELECT * FROM listings LIMIT 6")->fetchAll();
    loadView('listings/index', ['listings' => $listings]);
  }

  public function create()
  {
    loadView('listings/create');
  }


  public function show($params)
  {

    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // check if listing exist
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    loadView('listings/show', ['listing' => $listing]);
  }

  // store / insert listings to database
  public function store()
  {
    $allowedFields = ['title', 'description', 'salary', 'requirements', 'benefits', 'company', 'address', 'city', 'state', 'tags', 'phone', 'email'];

    // array_flip turn key into value and value into key
    // array_intersect_key compare to array key and return the matched
    $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

    $newListingData['user_id'] = Session::get('user')['id'];
    // loop through the $newListingData and sanitize them
    $newListingData = array_map('sanitize', $newListingData);

    $requiredFields = ['title', 'salary', 'description', 'email', 'city', 'state'];

    $errors = [];

    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required';
      }
    }
    if (!empty($errors)) {
      // reload view with errors
      loadView('listings/create', ['errors' => $errors, 'listing' => $newListingData]);
    } else {

      // submit data
      $fields = [];
      foreach ($newListingData as $field => $value) {
        $fields[] = $field;
      }
      $fields = implode(', ', $fields);

      $values = [];

      foreach ($newListingData as $field => $value) {
        // convert empty string to null 
        if ($value === '') {
          $newListingData[$field] = null;
        }

        $values[] = ':' . $field;
      }

      $values = implode(', ', $values);

      // insert into database

      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

      $this->db->query($query, $newListingData);

      Session::setFlashMessage('success_message', 'created successfully');

      // redirect after submission
      redirect('/listings');
    }
  }

  //delete listing
  public function destroy($params)
  {
    $id = $params['id'];
    $params = [
      'id' => $id
    ];

    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // authorization
    if (!Authorization::isOwner($listing->user_id)) {
      // $_SESSION['error_message'] = 'You are not authorized to delete this listing';

      Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');

      redirect("/listings/{$listing->id}");
      return;
    }

    // delete / remove item from database table
    $this->db->query('DELETE FROM listings WHERE id = :id', $params);

    // set flash message
    // $_SESSION['success_message'] = 'Listing deleted successfully';
    Session::setFlashMessage('success_message', 'Listing deleted successfully');

    redirect('/listings');
  }

  public function edit($params)
  {

    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // check if listing exist
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // authorization
    if (!Authorization::isOwner($listing->user_id)) {
      Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
      redirect("/listings/{$listing->id}");
      return;
    }

    // inspectAndDie($listing);

    loadView('listings/edit', ['listing' => $listing]);
  }

  /**
   * Update listing
   * 
   * @param array $params
   * @return void
   */
  public function update($params)
  {
    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // check if listing exist
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    // authorization
    if (!Authorization::isOwner($listing->user_id)) {
      Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
      redirect("/listings/{$listing->id}");
      return;
    }

    $allowedFields = ['title', 'description', 'salary', 'requirements', 'benefits', 'company', 'address', 'city', 'state', 'tags', 'phone', 'email'];

    $updatedValues = [];

    $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));

    $updatedValues = array_map('sanitize', $updatedValues);

    $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

    $errors = [];

    foreach ($requiredFields as $field) {
      if (empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
        $errors[$field] = ucfirst($field) . ' is required';
      }
    }

    if (!empty($errors)) {
      // reload view with errors
      loadView('listings/edit', ['errors' => $errors, 'listing' => $listing]);
      exit;
    } else {
      // submit to db

      $updateFields = [];
      foreach (array_keys($updatedValues) as $field) {
        $updateFields[] = "{$field} = :{$field}";
      }

      $updateFields = implode(', ', $updateFields);


      $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

      // add id to updatedValues
      $updatedValues['id'] = $id;
      // inspectAndDie($updatedValues);
      $this->db->query($updateQuery, $updatedValues);

      Session::setFlashMessage('success_message', 'Listing updated successfully');
      redirect("/listings/{$id}");
    }
  }

  // search function
  /**
   * search listing by keyword/location
   * 
   * @return void
   */
  public function search()
  {
    $keywords = isset($_GET['keywords']) ?  trim($_GET['keywords']) : '';
    $location = isset($_GET['location']) ?  trim($_GET['location']) : '';

    $query = "SELECT * FROM listings 
    WHERE 
    (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords)
    AND
    (city LIKE :location OR state LIKE :location)
    ";

    $params = [
      'keywords' => "%{$keywords}%",
      'location' => "%{$location}%",
    ];

    $listings = $this->db->query($query, $params)->fetchAll();

    loadView('/listings/index', [
      'listings' => $listings,
      'keywords' => $keywords,
      'location' => $location,
    ]);
  }
}
