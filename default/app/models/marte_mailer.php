<?php
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

require_once '../../vendor/autoload.php';
    class MarteMailer extends PHPMailer{

       // public $mail ;

        public function __construct(string $from_user_name, string $from_pass, $exceptions = true, $body ='') {
            parent::__construct($exceptions);
            //$this->mail =  new PHPMailer();
            $this->isSMTP();
            $this->IsHTML();
            $this->SMTPDebug = 0;
            $this->SMTPAuth = true;
            $this->SMTPKeepAlive = true;

            $this->SMTPSecure = 'ssl';
            $this->Host = 'smtp.gmail.com';
            $this->Port = 465;
            $this->Username = $from_user_name;
            $this->Password = $from_pass;           
            $this->setFrom($from_user_name);

        }


        



    }