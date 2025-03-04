<?php
/**
 * EasyCopyright
 *
 * A MODX snippet that generates a copyright notice with dynamic years.
 *
 * @version 1.2.1
 * @author  Cameron Gilroy <http://www.camerongilroy.com>
 * @link    http://github.com/camgill/EasyCopyright
 * @updated Updated for PHP 8+ and modern MODX by eydolan
 * @date    March 2025
 *
 * Usage:
 * [[!EasyCopyright]] - © [Site Name] 2025 - Powered by MODx
 * [[!EasyCopyright? &startYear=`2009`]] - © [Site Name] 2009-2025 - Powered by MODx
 *
 * Properties:
 * @property string $name Site name (defaults to site_name setting)
 * @property string $startYear Starting year (defaults to current year)
 * @property string $powered Powered by text/link (set to 'false' to disable)
 * @property string $yearSeparator Separator between years (default: "-")
 * @property string $poweredBySeparator Separator before powered by text (default: "-")
 */

namespace EasyCopyright;

// Type declarations and null coalescing for better PHP 8+ compatibility
$name = isset($scriptProperties['name']) 
    ? filter_var($scriptProperties['name'], FILTER_SANITIZE_STRING)
    : $modx->getOption('site_name');

// Default values with proper sanitization
$yearSeparator = filter_var(
    $scriptProperties['yearSeparator'] ?? '-', 
    FILTER_SANITIZE_STRING
);
$poweredBySeparator = filter_var(
    $scriptProperties['poweredBySeparator'] ?? '-', 
    FILTER_SANITIZE_STRING
);
$poweredDefault = 'Powered by <a href="https://modx.com" rel="nofollow">MODX</a>';
$powered = isset($scriptProperties['powered'])
    ? $scriptProperties['powered']
    : $poweredDefault;

// Current year using MODX's built-in method if available
$currentYear = $modx->getOption('current_year') ?: date('Y');
// Start year validation - ensure it's a valid year
$startYear = isset($scriptProperties['startYear']) 
    ? (int)$scriptProperties['startYear'] 
    : $currentYear;

// Validate startYear is reasonable (between 1900 and current year)
$startYear = max(1900, min($currentYear, $startYear));

// Generate year string
$years = ($currentYear > $startYear) 
    ? sprintf('%d%s%d', $startYear, $yearSeparator, $currentYear) 
    : (string)$currentYear;

// Handle powered by section
$poweredBy = ($powered !== 'false' && $powered !== '')
    ? " {$poweredBySeparator} {$powered}"
    : '';

// Build and return the copyright string with HTML escaping
$output = sprintf(
    '© %s %s%s',
    htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    htmlspecialchars($years, ENT_QUOTES, 'UTF-8'),
    $poweredBy
);

return $output;
