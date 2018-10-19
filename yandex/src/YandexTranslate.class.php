<?php

/*
|    YandexTranslate.class.php 
|
|    Author:  Dragutin Mitrecic
|    Contact: dmitrecic@gmail.com
|
|    YandexTranslate is a class for translating words and sentences 
|    from one language to another using Yandex API for translator.
| 
|    Yandex Translation service is limited to 1.000.000 characters per day with the 
|    maximum of 10.000.000 characters monthly (using free service)
|    You can read more at: https://yandex.com/legal/translate_api/
|
|    This is basic class to use Yandex translation API service.
|
|    Method description:
|    - setKey = set Yandex API key for translation service
|    - setLangFrom = set the language of the text to be translated (language of original text sent to Yandex to translate)
|    - setLangTo = set to which language you want translate your original text
|    - setFormat = in what format would you like to receive the response from class with translated text - text or json
|                  - "text" will return only translated text
|                  - "json" will return json object with the keys: "translation", "err", "errmessage"
|   
|    - translate = translate text, in returned format set by setFormat method
*/




class YandexTranslate
{
    /*
      =====================================================================
             Setting up Yandex URL and yandex translatation API key
      =====================================================================
    */

    private $yandexurl = "https://translate.yandex.net/api/v1.5/tr/translate?key=";
    private $yandex_key = "";

    /*
      =====================================================================
            Initializing and setting up default values for variables
      =====================================================================
    */

    private $txt = "";
    private $lang_from = "";
    private $lang_to = "";
    private $response_format = "plain";
    private $err = 0;
    private $httpresponse = "";
    private $errmessage = "";
    private $response = "";
    private $translated;
    private $format = "json";  
    
    /*
       =====================================================================
            Setting up response errors and messages received from Yandex
       =====================================================================
    */

    private $responsestatus = array(
        "0" => ["err" => 1, "errmessage" => "Unknown error - check URL and internet connection"],
        "200" => ["err" => 0, "errmessage" => "OK"],
        "401" => ["err" => 1, "errmessage" => "Invalid API key"],
        "402" => ["err" => 1, "errmessage" => "API key blocked"],
        "403" => ["err" => 1, "errmessage" => "Invalid API key"],
        "404" => ["err" => 1, "errmessage" => "Exceeded the daily limit on the amount of translated text"],
        "413" => ["err" => 1, "errmessage" => "Exceeded the maximum text size"],
        "422" => ["err" => 1, "errmessage" => "The text cannot be translated"],
        "501" => ["err" => 1, "errmessage" => "The specified translation direction is not supported"]
    );



    /*
      =======================
          Public methods
      =======================
    */

    public function translate($text)
    {
        $this->txt = $text;
        $this->prepareText();
        $this->requestYandexTranslation();
        $this->checkResponse();
        $this->returnFormatted();
        return $this->translated;
    }

    public function setKey($key)
    {
        $this->yandex_key = $key;
    }

    public function setFormat($format)  
    {
        $this->format = $format;
    }

    public function setLangFrom($lang)  
    {
        $this->lang_from = trim($lang);
    }

    public function setLangTo($lang)    
    {
        $this->lang_to = trim($lang);
    }


    /*
       =======================
          Private functions
       =======================
    */

    private function requestYandexTranslation()
    {
        $url = $this->yandexurl.$this->yandex_key;
        $url .='&lang='.$this->lang_from.'-'.$this->lang_to;
        $url .='&format='.$this->response_format;
        $url .='&text='.$this->txt;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => array(
            "accept: */*",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $this->response = trim(curl_exec($curl));
        $this->httpresponse = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->err = curl_error($curl);
        curl_close($curl);
    }

    private function prepareText()
    {
        $this->txt = urlencode($this->txt);
    }

    private function checkResponse()
    {
        $httpresponse = $this->httpresponse;
        $this->err = $this->responsestatus[$httpresponse]["err"];
        $this->errmessage = $this->responsestatus[$httpresponse]["errmessage"];
        $this->errorCheck();
    }

    private function errorCheck()
    {
        if ($this->err){
            $response="Error occured! ".$this->errmessage;
        } else {
            if ($this->response_format == "plain"){
                $this->response = strip_tags($this->response);
                $this->response = trim($this->response);
            } 
        }
    }

    private function returnFormatted()
    {
        if ($this->format == "text"){
            $this->responseWithText();
        }
        if ($this->format == "json"){
            $this->responseWithJson();
        }
        
    }

    private function responseWithText()
    {
        $this->translated = $this->response;
    }

    private function responseWithJson()
    {
        $this->translated = json_encode( 
            array(
                "translation" => $this->response, 
                "err" => "$this->err", 
                "errmessage" => $this->errmessage
            )
        );
    }
}
?>