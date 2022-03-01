<?php

// global $woocities_cities;

$woocities_cities = 'San Pedro Sula, Tegucigalpa, Danlí,  Santa Bárbara, Santa Rosa de Copán, Choluteca, Olanchito, Tela, Tocoa';


$city_name = explode(',', $woocities_cities);
$city_options = array();
foreach ($city_name as $city) {
    $slug = slugify($city);
    $city_options[$slug] = $city;
    echo $slug . ' => ' . $city . '<br>';
}

echo $woocities_cities . '<br>';

echo $city_options;


/**
 * Return the slug of a string to be used in a URL.
 * by: https://ourcodeworld.com/articles/read/253/creating-url-slugs-properly-in-php-including-transliteration-support-for-utf-8
 * @param $text
 * @return String
 */
function slugify($text)
{
    // transliterate
    setlocale(LC_CTYPE, 'es_ES');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicated - symbols
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}
