<?php

// Helper function to escape any HTML special characters in a string
function html($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Wrapper to echo text whilst escaping any HTML special characters
function htmlout($text) {
  echo html($text);
}