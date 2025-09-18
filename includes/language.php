<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Language Configuration ---
$available_langs = ['en', 'es'];
$default_lang = 'en';

// --- Language Detection & Switching ---
$lang_to_use = $default_lang;

// 1. Check for language change request
if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// 2. Use language from session if available
if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $available_langs)) {
    $lang_to_use = $_SESSION['lang'];
}

// --- Load Language File ---
$lang_file = __DIR__ . '/../languages/' . $lang_to_use . '.php';

if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    // Fallback to default language if file is missing for some reason
    require_once __DIR__ . '/../languages/' . $default_lang . '.php';
}


// --- Translation Function ---

/**
 * Translates a given key into the currently selected language.
 *
 * @param string $key The key to translate.
 * @return string The translated string, or the key itself if not found.
 */
function trans($key) {
    global $lang;
    if (isset($lang[$key])) {
        return $lang[$key];
    } else {
        // Return the key itself as a fallback
        return $key;
    }
}
?>
