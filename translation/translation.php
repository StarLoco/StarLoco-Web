<?php
function translate($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}
?>

