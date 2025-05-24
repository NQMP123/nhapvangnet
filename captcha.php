<?php
session_start();

// Sinh mã captcha gồm 5 số
$code = '';
for ($i = 0; $i < 5; $i++) {
    $code .= rand(0, 9);
}
$_SESSION['captcha_code'] = $code;

// Tạo ảnh
$width = 120;
$height = 44;
$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 30, 30, 30);
$border = imagecolorallocate($image, 200, 200, 200);

imagefilledrectangle($image, 0, 0, $width, $height, $bg);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

// Font
$font_size = 26;
$font_file = __DIR__ . '/arial.ttf'; // Đảm bảo có file arial.ttf hoặc đổi sang font có sẵn
$x = 14;
$y = 34;
if (file_exists($font_file)) {
    imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $code);
} else {
    imagestring($image, 5, 28, 14, $code, $text_color);
}

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);