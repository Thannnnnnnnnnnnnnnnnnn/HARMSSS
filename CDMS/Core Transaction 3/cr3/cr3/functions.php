<?php
  // Prevent redeclaration of dd()
  if (!function_exists('dd')) {
      function dd($value)
      {
          echo '<pre>';
          var_dump($value);
          echo '</pre>';
          die();
      }
  }

  // Prevent redeclaration of validation()
  if (!function_exists('validation')) {
      function validation($validate, &$errors)
      {
          if (empty(trim($validate))) {
              $errors[$validate] = "{$validate} is required.";
          }
      }
  }
  ?>