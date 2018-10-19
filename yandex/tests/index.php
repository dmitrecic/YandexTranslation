<?php

require ("../src/YandexTranslate.class.php");

/*

 set Yandex API key
 you can get one here (currently free)
 https://translate.yandex.com/developers/keys

 YandexTranslate is a class for translating words and sentences 
 from one language to another using Yandex API for translator.
 
 Yandex Translation service is limited to 1.000.000 characters per day with the 
 maximum of 10.000.000 characters monthly (using free service)
 You can read more at: https://yandex.com/legal/translate_api/

 This is basic class to use Yandex translation API service.

 Methods description:
    - setKey = set Yandex API key for translation service
    - setLangFrom = set the language of the text to be translated (language of original text sent to Yandex to translate)
    - setLangTo = set to which language you want translate your original text
    - setFormat = in what format would you like to receive the response with translated text - text or json
                  - text will return only translated text
                  - json will return json object with the keys: "translation", "err", "errmessage"
   
    - translate = translate text, in returned format set by setFormat method


    
*/

$apikey="_INSERT_YANDEX_TRANSLATION_API_KEY_";

$translation=new YandexTranslate();
$translation->setKey($apikey);
$translation->setLangFrom("en");
$translation->setLangTo("de");

echo "This is text translated from english to german, return format is json:<br/>";
$translation->setFormat("json");
$translated=$translation->translate("Apples and oranges.");
echo json_decode($translated)->translation;

echo "<hr>";
echo "This is the same text, but returning format is text:<br/>";

$translation->setFormat("text");
$translated=$translation->translate("Apples and oranges.");
echo $translated;
?>