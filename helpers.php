<?php

/**
 * Get basePath
 *
 * @param string $path
 * @return $path
 */
function basePath($path = '')
{
  return __DIR__ . '/' . $path;
}

/**
 * Load a View
 *
 * @param string $name
 * @return void
 */
function loadView($name, $data = [])
{
  $viewPath = basePath("App/views/{$name}.view.php");

  if ($viewPath) {
    extract($data);
    require $viewPath;
  } else {
    echo "View {$name} not found!";
  }
}


/**
 * load a partial
 *
 * @param string $name
 * @return void
 */
function loadPartial($name, $data = [])
{
  $partialPath = basePath("App/views/partials/{$name}.php");

  if ($partialPath) {
    extract($data);
    require $partialPath;
  } else {
    echo "Partial {$name} not found!";
  }
}


/**
 * inspect function
 *
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

/**
 * inspect and die
 *
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
  echo '<pre>';
  die(var_dump($value));
  echo '</pre>';
}

/**
 * format salary
 *
 * @param string $salary
 * @return string formatted salary
 */
function formatSalary($salary)
{
  return '$' . number_format(floatval($salary));
}


/**
 * Sanitize data
 * 
 * @param string $dirty
 * @return string
 */

function sanitize($dirty)
{
  return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * redirect to given url
 *
 * @param string $url
 * @return void
 */
function redirect($url)
{
  header("Location: {$url}");
  exit;
}
