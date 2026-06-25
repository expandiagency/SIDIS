<?php
header('Content-Type: text/plain');
echo "GD: " . (extension_loaded('gd') ? 'yes' : 'no') . "\n";
echo "Imagick: " . (extension_loaded('imagick') ? 'yes' : 'no') . "\n";
if (extension_loaded('gd')) {
    $info = gd_info();
    echo "WebP support: " . (!empty($info['WebP Support']) ? 'yes' : 'no') . "\n";
}
echo "PHP version: " . PHP_VERSION . "\n";
