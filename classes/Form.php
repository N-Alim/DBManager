<?php

class Form
{
    private string $form;
    private array $keysToNotUse = array("envoi", "token");
    private $errorHand;
    private array $formValues = array();
    private string $csrfToken;

    public function __construct(string $method, string $action, string $csrfToken, string $enctype = null)
    {
        $this->csrfToken = $csrfToken;
        $this->form = "<form method='$method' action='$action'" . (($enctype === null) ? ">" : "enctype='$enctype'>");
    }

    public function getFormValues()
    {
        foreach ($_POST as $key => $value) 
        {
            $this->formValues[$key][1] = htmlentities(trim($value));
        }
    }

    public function createFormFromCSV($csvPath, $linesOffset = 1)
    {
        $csvInputs = csvReader($csvPath, $linesOffset);

        $formInputs = array();

        for ($cnt=0; $cnt < count($csvInputs); $cnt++) 
        { 
            $formInputs[$csvInputs[$cnt][0]] = 
            [ $this->createInputTypeFromString($csvInputs[$cnt][1]),
            ($csvInputs[$cnt][2] === "true") ? true : false, 
            $csvInputs[$cnt][3], 
            $csvInputs[$cnt][4]];
        }

        return $this->createForm($formInputs);
    }
    
    public function createFormFromSqlInputs($inputs)
    {
        foreach ($inputs as $row) 
        {
            if ($row->COLUMN_NAME !== "id")
            {
                $variableName = $row->COLUMN_NAME;
                $formInputs[$variableName] = 
                [ $this->createInputTypeFromSqlType($row->DATA_TYPE),
                true, 
                $variableName, 
                ""];
            }
        }

        return $this->createForm($formInputs);
    }

    public function createInputTypeFromString(string $InputString)
    {
        switch ($InputString) 
        {
            case "Text":
                return InputType::Text;

            case "LongText":
                return InputType::LongText;

            case "Checkbox":
                return InputType::Checkbox;

            case "Mail":
                return InputType::Mail;

            case "Password":
                return InputType::Password;

            case "PasswordInit":
                return InputType::PasswordInit;

            case "PasswordVerif":
                return InputType::PasswordVerif;

            case "Time":
                return InputType::Time;
            
            case "DateTime":
                return InputType::DateTime;

            case "Date":
                return InputType::Date;

            case "SelectType";
                return InputType::SelectType;
            
            case "Int";
                return InputType::Int;

            case "Float";
                return InputType::Float;
        }
        
    }

    public function createInputTypeFromSqlType(string $InputString)
    {
        switch ($InputString) 
        {
            case "varchar":
                return InputType::Text;

            case "text":
                return InputType::LongText;

            case "int";
                return InputType::Int;

            case "double";
                return InputType::Float;
        }
        
    }

    private function createForm(array $inputs)
    {
        $this->inputs = $inputs;
        foreach ($inputs as $key => $value) 
        {
            $this->createInput($key, $value);
        }

        $this->form .= "<div class='row form-group d-flex justify-content-around'><input class='col-md-3'type='reset' value='Effacer' />
            <input class='col-md-3' type='submit' value='Envoyer' name='envoi' />
            <input type='hidden' name='token' value='" . $this->csrfToken . "' /><div></div></form>";

        return $this->form;
    }

    private function createInput($name, $inputDetails)
    {
        $this->form .= "<div class='row form-group'><label class='col-md-4' for='$name'> $inputDetails[2] </label>";

        switch ($inputDetails[0]) 
        {
            case InputType::Text:
            case InputType::Mail:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='texte' id='$name' name='$name' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case InputType::Password:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='password' id='$name' name='$name' value=''></div></div>"; 
                break;

            case InputType::PasswordInit:
                {
                    $this->form .= "<div class='col-md-6'><input class='form-control' type='password' id='$name' name='$name' value=''></div></div>"
                    . "<div class='row form-group'><label class='col-md-4' for='$name'Verif> Vérification $inputDetails[2] : </label>"
                    . "<div class='col-md-6'><input class='form-control' type='password' id='$name" . "Verif' name='$name" . "Verif' value=''></div></div>"; 
                    $this->formValues[$name . "Verif"][0] = InputType::PasswordVerif;
                    break;
                }

            case InputType::LongText:
                $this->form .= "<div class='col-md-6'><textarea class='form-control' id='$name' name='$name' value='" 
                . (($inputDetails[1]) ? "<?php echo \$$name;?>" : $inputDetails[3]) . "'></textarea></div></div>";          
                break;

            case InputType::Time:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='time' id='$name' name='$name' step='1' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case InputType::DateTime:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='datetime-local' id='$name' name='$name' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case InputType::Date:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='date' id='$name' name='$name' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case InputType::Checkbox:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='checkbox' id='$name' name='$name' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case InputType::Int:
                $this->form .= "<div class='col-md-6'><input class='form-control' type='number' id='$name' name='$name' value=" 
                . (($inputDetails[1]) ? ($this->formValues[$name][1] ?? $inputDetails[3]) : $inputDetails[3]) . "></div></div>"; 
                break;

            case "Float";
                return InputType::Float;

            case InputType::SelectType:
                $this->form .= "<div class='col-md-6'><select class='form-control' step='any' range='$name' id='$name' name='$name'>
                    <option value='varchar'>Varchar</option>
                    <option value='text'>Text</option>
                    <option value='int'>Int</option>
                    <option value='double'>Double</option>
                    </select></div></div>"; 
                break;

            default:
        }

        $this->formValues[$name][0] = $inputDetails[0];
        $this->formValues[$name][2] = $inputDetails[3];
    }

    public function setValuesChecker(ErrorHandler $errorHand)
    {
        $this->errorHand = $errorHand;
    }

    public function checkValues()
    {
        foreach ($this->formValues as $key => $value) 
        {
            if (! in_array($key, $this->keysToNotUse))
            {
                $this->checkValue($key, $value);
            }
        }
    }

    private function checkValue($inputName, $inputDetails)
    {
        switch ($inputDetails[0]) 
        {
            case InputType::Text:
            case InputType::LongText:
                {
                    if (preg_match('/(*UTF8)^[[:alnum:]]+$/', html_entity_decode($inputDetails[1])) !== 1)
                        $this->errorHand->addError("Champ \"$inputName\" manquant");

                    else
                        $this->formValues[$inputName][1] = html_entity_decode($this->formValues[$inputName][1]);
                    break;
                }


            case InputType::Mail:
                if (!filter_var($this->formValues[$inputName][1], FILTER_VALIDATE_EMAIL))
                    $this->errorHand->addError("Veuillez saisir un e-mail valide");
                break;

            case InputType::Password:
            case InputType::Time:
            case InputType::DateTime:
            case InputType::Date:
            case InputType::Int:
            case InputType::Float:
                if (strlen($this->formValues[$inputName][1]) === 0)
                    $this->errorHand->addError("Champ \"$inputName\" manquant");
                break;

            case InputType::SelectType:
                if (strlen($this->formValues[$inputName][1]) === 0)
                    $this->errorHand->addError("Champ \"$inputName\" non selectionné");
                break;

            case InputType::PasswordInit:
                {
                    if (strlen($this->formValues[$inputName][1]) === 0)
                        $this->errorHand->addError("Veuillez saisir un mot de passe");
                
                    else if (strlen($this->formValues[$inputName . "Verif"][1]) === 0)
                        $this->errorHand->addError("Veuillez saisir la vérification de votre mot de passe");
                
                    else if ($this->formValues[$inputName][1] !== $this->formValues[$inputName ."Verif"][1])
                        $this->errorHand->addError("Vos mots de passe ne correspondent pas");
                    break;
                }

            default:
        }
    }

    public function getValue($key)
    {
        return $this->formValues[$key][1];
    }

    public function getErrorsCount()
    {
        return $this->errorHand->getErrorsCount();
    }

    public function checkErrors()
    {
        return $this->errorHand->getErrorMessage();
    }
}
