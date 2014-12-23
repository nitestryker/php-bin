<?php

class email
{
    public $email;
    public function __construct($email)
    {
        $this->email = $email;
        $this->isValidEmail($email);
    }
    function isValidEmail($email)
    {
        //Perform a basic syntax - Check
        //If this check fails, there's no need to continue
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->results = false;
        }

        //extract host
        list($user, $host) = explode("@", $email);
        //check, if host is accessible
        if (!checkdnsrr($host, "MX") && !checkdnsrr($host, "A"))
        {
            $this->results = false;
        }

        $this->results = true;
    }
    public function getResults()
    {
        return $this->results;
    }
}

?>