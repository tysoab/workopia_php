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
  $viewPath = basePath("views/{$name}.view.php");

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
function loadPartial($name)
{
  $partialPath = basePath("views/partials/{$name}.php");

  if ($partialPath) {
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
