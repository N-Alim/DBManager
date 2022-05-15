<?php

class ErrorHandler
{
    private array $errors = array();

    public function addError(string $error) : void
    {
        array_push($this->errors, $error);
    }

    public function getErrorsCount() : int
    {
        return count($this->errors);
    }

    public function getErrorMessage() : string
    {
        $errorMessage = "<ul>";
        $i = 0;
        do {
            $errorMessage .= "<li>";
            $errorMessage .= $this->errors[$i];
            $errorMessage .= "</li>";
            $i++;
        } while ($i < count($this->errors));

        $errorMessage .= "</ul>";

        return $errorMessage;
    }
}
