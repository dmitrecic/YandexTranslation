<?php

require ("src/YandexTranslate.class.php");
require ("src/YandexTranslationSave.class.php");

/*

 This class is extension of the YandexTranslate class.

 This assumes that you are familiar with YandexTranslate class and  already have a created, or will use existing, 
 table with the fields where you want to save translation.

 Setting data table and fields where the original and translated text will be saved.
 You can omit third field when executing saveTranslation method if you dont want to save original text.

 Explanation:
 setSaveTable ($table, $translated_field, $original_field)
 
 setSaveTable method has 2 required parameters and 3-th is optional, as follows:
      - "table" = MySQL database table where the translation will be saved
      - "translation_field" = field within the $table where the translation will be saved 
      - "original_field" = optional, field within the $table where the original text will be saved

 Other methods:
 setShowMessages = show (1) or hide(0) class messages
 save = saving translation to database

*/

/* Before class initiation, set database connection data */

$db_host="";
$db_name="";
$db_user="";
$db_pass="";

/* Initiate class */

$translation=new YandexTranslationSave($db_host, $db_name, $db_user, $db_pass);

/*

 Set Yandex API key
 you can get one here (September 2018. - Currently free)
 https://translate.yandex.com/developers/keys
 
*/

$apikey="_insert_yandex_api_key_";

/* Set text for translation */

$text="I love to eat apples and oranges.";

$translation->setSaveTable( array(
    "table"=>"translations", 
    "translation_field"=>"translated"
));

$translation->setKey($apikey);
$translation->setLangFrom("en");
$translation->setLangTo("hr");
$translation->setFormat("plain");
$translation->setShowMessages(1);
$translation->translate($text);
$translation->save();

?>