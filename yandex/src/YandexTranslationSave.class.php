<?php

/*
|    Class YandexTranslationSave is extention to the class YandexTranslate
|    it is just an example how to save translations to your database
*/


class YandexTranslationSave extends YandexTranslate
{
    private $DB_host="";
    private $DB_name="";
    private $DB_user="";
    private $DB_pass="";
    private $DB_table="";
    private $inserttxt="";
    private $showmessage=0;
    private $connection;

    public function __construct($DB_host, $DB_name, $DB_user, $DB_pass)
    {
        $this->DB_host=$DB_host;
        $this->DB_name=$DB_name;
        $this->DB_user=$DB_user;
        $this->DB_pass=$DB_pass;
        $this->connectToDb();
    }

    public function setSaveTable($options)
    {
        $this->DB_table=$options["table"];
        $this->transfield=$options["translation_field"];
        $this->orgfield=(isset($options["original_field"]) ? $options["original_field"] :"");
    }

    public function save()
    {
        if (!$this->err && $this->txt!=""){
            $this->insertToTable();
            if ($this->insertstatus==1){
                $this->showMessage($this->txt."<br/>Translation saved to database!");
            } else {
                $this->showMessage("Error: Translation not saved! <br/>Something went wrong with table and table fields, please check setSaveTable settings");
            }
        }
        else
        {
            $this->showMessage($this->errmessage."<br/>Nothing to save");
        }
    }

    public function setShowMessages($state)
    {
        $this->showmessage=$state;
    }

    private function connectToDb()
    {
        $this->connection=@new mysqli($this->DB_host, $this->DB_user, $this->DB_pass, $this->DB_name);
        if ($this->connection->connect_errno) {
            exit("Error while trying to connect to database!<br/>Please check your database connection details.<br/>Error No:".$this->connection->connect_errno);
        } else {
            $this->connection->set_charset("utf8");
        }
    }

    private function insertToTable()
    {
        $db_conn=$this->connection;
        
        $insert_original_text=($this->orgfield!="" ?  ", `".$this->orgfield."` ='".mysqli_real_escape_string($db_conn,$this->org_text)."'":"");

        $query="insert into `".$this->DB_table."` set `".$this->transfield."` = '".mysqli_real_escape_string($db_conn,$this->txt)."' ".$insert_original_text;

        if ($db_conn->query($query)){
            $this->insertstatus=1;
        } else {
            $this->insertstatus=0;
        }
    }

    private function showMessage($message)
    {
        if ($this->showmessage==1)
        {
            echo $message;
        }
    }

    public function __destruct()
    {
        //
    }
}

?>