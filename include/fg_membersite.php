<?PHP
/*
    Registration/Login script from HTML Form Guide
    V1.0

    This program is free software published under the
    terms of the GNU Lesser General Public License.
    http://www.gnu.org/copyleft/lesser.html
    

This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html

*/
require_once("class.phpmailer.php");
require_once("formvalidator.php");

class FGMembersite
{
    var $admin_email;
    var $from_address;
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;

    var $error_message;

    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'wordmoment.com';
        $this->rand_key = '0iQx5oBk66oVZep';
    }

    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;

    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }

    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }

    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
            return false;
        }

        $formvars = array();
        //echo "test1";
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        //echo "test2";
        $this->CollectRegistrationSubmission($formvars);
        //echo "test3";
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        //echo "test4";
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }
        //echo "test5";
        $this->SendAdminIntimationEmail($formvars);

        return true;
    }

    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }

        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");

        $validator->addValidation("password","req","Please fill in Password");


        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }
        return true;
    }

    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }

    function HandleError($err)
    {
        $this->error_message .= $err." ";
    }

    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['password'] = $this->Sanitize($_POST['password']);

    }

    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
            );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }

    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }

    function SaveToDatabase(&$formvars)
    {
        //echo "save1";
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        //echo "save2";
        if(!$this->Ensuretable())
        {
            return false;
        }
        //echo "save3";
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        //echo "save4";
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This UserName is already used. Please try another username");
            return false;
        }

        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }

    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }

    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }

    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }

    function CreateTable()
    {

        $qry = "Create Table $this->tablename (".
            "id_user INT NOT NULL AUTO_INCREMENT ,".
            "name VARCHAR( 128 ) NOT NULL ,".
            "email VARCHAR( 64 ) NOT NULL ,".
            "phone_number VARCHAR( 16 ) NOT NULL ,".
            "username VARCHAR( 16 ) NOT NULL ,".
            "salt VARCHAR( 50 ) NOT NULL ,".
            "password VARCHAR( 80 ) NOT NULL ,".
            "confirmcode VARCHAR(32) ,".
            "PRIMARY KEY ( id_user )".
            ")";


        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }

    //-------Public Helper functions -------------

    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }

    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
            $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
            $ret_str = addslashes( $str );
        }
        return $ret_str;
    }

    function InsertIntoDB(&$formvars)
    {

        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);

        $formvars['confirmcode'] = $confirmcode;

        $hash = $this->hashSSHA($formvars['password']);

        $encrypted_password = $hash["encrypted"];



        $salt = $hash["salt"];




        $insert_query = 'insert into '.$this->tablename.'(
		name,
		email,
		username,	
		password,
		salt,
		confirmcode
		)
		values
		(
		"' . $this->SanitizeForSQL($formvars['name']) . '",
		"' . $this->SanitizeForSQL($formvars['email']) . '",
		"' . $this->SanitizeForSQL($formvars['username']) . '",
		"' . $encrypted_password . '",
		"' . $salt . '",
		"' . $confirmcode . '"
		)';


        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }
        return true;
    }

    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }

    function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
    //-------Private Helper functions-----------

    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($formvars['email'],$formvars['name']);

        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $confirmcode = $formvars['confirmcode'];

        $confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;

        $mailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
            "Thanks for your registration with ".$this->sitename."\r\n".
            "Please click the link below to confirm your registration.\r\n".
            "$confirm_url\r\n".
            "\r\n".
            "Regards,\r\n".
            "Webmaster\r\n".
            $this->sitename;

        if(!$mailer->Send())
        {
            echo 'Mailer Error: ' . $mailer->ErrorInfo;
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }

    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    }

    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';

        $urldir ='';
        $pos = strrpos($_SERVER['REQUEST_URI'],'/');
        if(false !==$pos)
        {
            $urldir = substr($_SERVER['REQUEST_URI'],0,$pos);
        }

        $scriptFolder .= $_SERVER['HTTP_HOST'].$urldir;

        return $scriptFolder;
    }

    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($this->admin_email);

        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
            "Name: ".$formvars['name']."\r\n".
            "Email address: ".$formvars['email']."\r\n".
            "UserName: ".$formvars['username'];

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }

        $this->SendUserWelcomeEmail($user_rec);

        $this->SendAdminIntimationOnRegComplete($user_rec);

        echo "Before creating dir";
        /*if (!$this->createDir($user_rec)){
            echo "Creating dir error";
            return false;
        }*/

        return true;
    }

    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $confirmcode = $this->SanitizeForSQL($_GET['code']);

        $result = mysql_query("Select name, email, username, id_user from $this->tablename where confirmcode='$confirmcode'",$this->connection);
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        $user_rec['username'] = $row['username'];
        $user_rec['id_user']= $row['id_user'];

        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }
        return true;
    }

    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($user_rec['email'],$user_rec['name']);

        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
            "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
            "\r\n".
            "Regards,\r\n".
            "Webmaster\r\n".
            $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }

    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();



        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($this->admin_email);

        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
            "Name: ".$user_rec['name']."\r\n".
            "Email address: ".$user_rec['email']."\r\n";

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function createDir($user_rec){

        // Create directory with name "username_user_id"
        $path = "../../user_database/".$user_rec['username']."_".$user_rec['id_user'];
        $share_path = "../../user_database/global_share/".$user_rec['username']."_".$user_rec['id_user'];
        if (!is_dir("../../user_database/")){
            $this->HandleError("User_database is not dir");
            return false;
        }

        if (!mkdir($path, 0777, true)) {
            $this->HandleError("Failed to create User Database folders...");
            return false;
        }

        if (!mkdir($share_path, 0777, true)) {
            $this->HandleError("Failed to create Share Database folders...");
            return false;
        }



        // Create file "index.html" inside that directory
        if (!$fp = fopen($path."/index.html","wb")){
            $this->HandleError("Cannot create file index.html");
            return false;
        }

        // Write empty content to "index.html"
        if (fwrite($fp, "") === FALSE) {
            $this->HandleError("Cannot write to file index.html");
            return false;
        }
        if (!$share_fp = fopen($share_path."/index.html","wb")){
            $this->HandleError("Cannot create file index.html in Share Directory");
            return false;
        }

        // Write empty content to "index.html"
        if (fwrite($share_fp, "") === FALSE) {
            $this->HandleError("Cannot write to file index.html in Share Directory");
            return false;
        }

        fclose($share_fp);

        return true;
    }

    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }

        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }

        $_SESSION[$this->GetLoginSessionVar()] = $username;

        return true;
    }

    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $username = $this->SanitizeForSQL($username);

        $nresult = mysql_query("SELECT * FROM $this->tablename WHERE username = '$username'", $this->connection) or die(mysql_error());
        // check for result
        $no_of_rows = mysql_num_rows($nresult);
        if ($no_of_rows > 0) {
            $nresult = mysql_fetch_array($nresult);
            $salt = $nresult['salt'];
            $encrypted_password = $nresult['password'];
            $hash = $this->checkhashSSHA($salt, $password);


        }


        $qry = "Select name, email, username from $this->tablename where username='$username' and password='$hash' and confirmcode='y'";

        $result = mysql_query($qry,$this->connection);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }

        $row = mysql_fetch_assoc($result);


        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        $_SESSION['username_of_user'] = $row['username'];

        return true;
    }

    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }

    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }

    function UserUsername()
    {
        return isset($_SESSION['username_of_user'])?$_SESSION['username_of_user']:'We are doomed';
    }

    function LogOut()
    {
        session_start();

        $sessionvar = $this->GetLoginSessionVar();

        $_SESSION[$sessionvar]=NULL;

        unset($_SESSION[$sessionvar]);
    }

    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }

    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        $email = $this->SanitizeForSQL($email);

        $result = mysql_query("Select * from $this->tablename where email='$email'",$this->connection);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with this email.");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);


        return true;
    }

    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];

        $mailer = new PHPMailer();



        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($email,$user_rec['name']);

        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $link = $this->GetAbsoluteURLFolder().
            '/resetpwd.php?email='.
            urlencode($email).'&code='.
            urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
            "There was a request to reset your password at ".$this->sitename."\r\n".
            "Please click the link below to complete the request: \r\n".$link."\r\n".
            "Regards,\r\n".
            "Webmaster\r\n".
            $this->sitename;

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function GetResetPasswordCode($email)
    {
        return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }

    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);

        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }

        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }

        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }

        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }

    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);

        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }

    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);

        $hash = $this->hashSSHA($newpwd);

        $new_password = $hash["encrypted"];

        $salt = $hash["salt"];

        $qry = "Update $this->tablename Set password='".$new_password."', salt='".$salt."' Where  id_user=".$user_rec['id_user']."";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }
        return true;
    }

    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];

        $mailer = new PHPMailer();

        $mailer->CharSet = 'utf-8';

        $mailer->AddAddress($email,$user_rec['name']);

        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
            "Your password is reset successfully. ".
            "Here is your updated login:\r\n".
            "username:".$user_rec['username']."\r\n".
            "password:$new_password\r\n".
            "\r\n".
            "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
            "\r\n".
            "Regards,\r\n".
            "Webmaster\r\n".
            $this->sitename;

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }

        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }

        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }

        $pwd = trim($_POST['oldpwd']);

        $salt = $user_rec['salt'];
        $hash = $this->checkhashSSHA($salt, $pwd);

        if($user_rec['password'] != $hash)
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);

        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }

    function CheckLogin()
    {
        if(!isset($_SESSION)){ session_start(); }

        $sessionvar = $this->GetLoginSessionVar();

        if(empty($_SESSION[$sessionvar]))
        {
            return false;
        }
        return true;
    }

    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }

    function ChangeName()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }

        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }

        $newname = trim($_POST['name']);


        if(!$this->ChangeNameInDB($user_rec, $newname))
        {
            return false;
        }
        $_SESSION['name_of_user'] = $newname;
        return true;
    }

    function ChangeNameInDB($user_rec, $newname)
    {
        $newpwd = $this->SanitizeForSQL($newname);

        $qry = "Update $this->tablename Set name='".$newname."' Where  id_user=".$user_rec['id_user']."";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }
        return true;
    }

    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }

    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }

    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }

    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }

    public function  getUserID($username){

        $nresult = mysql_query("SELECT id_user FROM $this->tablename WHERE username = '$username'", $this->connection) or die(mysql_error());
        return $nresult;
    }

    function acceptFriend($friend_email){

        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            return false;
        }

        $friend_rec = array();
        if(!$this->GetUserFromEmail($friend_email,$friend_rec))
        {
            return false;
        }

        $qry = "SELECT * FROM `friendsrelation` WHERE `user_request`=".$friend_rec['id_user']." AND `user_accepted`=".$my_rec['id_user'];

        $result = mysql_query($qry,$this->connection);

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("You have no friend quested");
            return false;
        }

        $qry = "UPDATE `friendsrelation` SET `friend_status`= 2 WHERE `user_request`=".$friend_rec['id_user']." AND `user_accepted`=".$my_rec['id_user'];

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }

        $qry = "UPDATE `friendsrelation` SET `friend_status`= 2 WHERE `user_request`=".$my_rec['id_user']." AND `user_accepted`=".$friend_rec['id_user'];

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }

        return true;

    }

    function addFriend($friendemail){
        $friendemail = trim($friendemail);
        if ($friendemail == ""){
            $this->HandleError("Please enter your friend email address");
            return false;
        }

        if ($friendemail == $this->UserEmail()){
            $this->HandleError("Please enter your friend email address");
            return false;
        }


        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleError("Something wrong! Please logout and login again");
            return false;
        }

        $friend_rec = array();
        if(!$this->GetUserFromEmail($friendemail,$friend_rec))
        {
            $this->HandleError("Please check your friend email address");
            return false;
        }

        $qry = "SELECT * FROM `friendsrelation` WHERE `user_request`=".$my_rec['id_user']." AND `user_accepted`=".$friend_rec['id_user']." AND `friend_status`=1";

        $result = mysql_query($qry,$this->connection);

        if($result && mysql_num_rows($result) > 0)
        {
            $this->HandleError("You have requested to connect with this friend");
            return false;
        }

        $qry = "SELECT * FROM `friendsrelation` WHERE `user_request`=".$friend_rec['id_user']." AND `user_accepted`=".$my_rec['id_user']." AND `friend_status`=1";

        $result = mysql_query($qry,$this->connection);

        if($result && mysql_num_rows($result) > 0)
        {
            $this->HandleError("Your friend have sent you friend request. Accept his/her friend request");
            return false;
        }

        $qry = "SELECT * FROM `friendsrelation` WHERE `user_request`=".$my_rec['id_user']." AND `user_accepted`=".$friend_rec['id_user'];

        $result = mysql_query($qry,$this->connection);

        if($result && mysql_num_rows($result) > 0)
        {
            $this->HandleError("You have connected with this friend");
            return false;
        }

        $qry = "SELECT * FROM `friendsrelation` WHERE `user_request`=".$friend_rec['id_user']." AND `user_accepted`=".$my_rec['id_user'];

        $result = mysql_query($qry,$this->connection);

        if($result && mysql_num_rows($result) > 0)
        {
            $this->HandleError("You have connected with that friend");
            return false;
        }

        $qry = "INSERT INTO `friendsrelation`(`user_request`, `user_accepted`, `friend_status`) VALUES (".$my_rec['id_user'].",".$friend_rec['id_user'].",1);";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error adding friend relation");
            return false;
        }
        return true;
    }

    function deleteFriend($friendemail){
        $friendemail = trim($friendemail);
        if ($friendemail == ""){
            $this->HandleError("Please enter your friend email address");
            return false;
        }

        if ($friendemail == $this->UserEmail()){
            $this->HandleError("Please enter your friend email address");
            return false;
        }


        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleError("Something wrong! Please logout and login again");
            return false;
        }

        $friend_rec = array();
        if(!$this->GetUserFromEmail($friendemail,$friend_rec))
        {
            $this->HandleError("Please check your friend email address");
            return false;
        }

        $qry = "DELETE FROM `friendsrelation` WHERE (`user_request` =".$my_rec['id_user']." AND `user_accepted` =".$friend_rec['id_user'].") OR (`user_request` =".$friend_rec['id_user']." AND `user_accepted` =".$my_rec['id_user'].")";

        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error removing friend relation");
            return false;
        }
        return true;
    }

    function getRequestedFriend($useremail){

        ini_set('memory_limit', '128M');

        $my_rec = array();
        if(!$this->GetUserFromEmail($useremail,$my_rec))
        {
            return false;
        }

        $qry = "
            SELECT *
            FROM fgusers3
            INNER JOIN friendsrelation
            ON fgusers3.id_user = friendsrelation.user_request
            WHERE (friendsrelation.user_accepted = ".$my_rec['id_user']." AND friendsrelation.friend_status = 1)";

        $result = mysql_query($qry,$this->connection);

        if(!$result)
        {
            $this->HandleDBError("Error getting friends' list");
            echo "Error";
            return false;
        }
        return $result;
    }

    function getSharingFriends($useremail, $file_id){

        ini_set('memory_limit','1G');


        $my_rec = array();
        if(!$this->GetUserFromEmail($useremail,$my_rec))
        {
            return false;
        }

        $qry = "SELECT tmp.name, tmp.id_user,
(CASE WHEN tmp.id_user=(
SELECT to_user from sharerelation WHERE sharerelation.file_id=".$file_id.") 
THEN 1 ELSE 0 END) sharing 
FROM (SELECT fgusers3.name, fgusers3.id_user
            FROM fgusers3
            INNER JOIN friendsrelation
            ON fgusers3.id_user = friendsrelation.user_accepted
            WHERE friendsrelation.user_request = ".$my_rec['id_user']." AND friendsrelation.friend_status = 2
UNION
            SELECT fgusers3.name, fgusers3.id_user
            FROM fgusers3
            INNER JOIN friendsrelation
            ON fgusers3.id_user = friendsrelation.user_request
            WHERE friendsrelation.user_accepted = ".$my_rec['id_user']." AND friendsrelation.friend_status = 2) as tmp";

        $result = mysql_query($qry,$this->connection);

        if(!$result)
        {
            $this->HandleDBError("Error getting friends' list");
            echo "Error";
            return false;
        }
        return $result;
    }

    function getFriends($useremail){

        $my_rec = array();
        if(!$this->GetUserFromEmail($useremail,$my_rec))
        {
            return false;
        }

        $qry = "SELECT *
            FROM fgusers3
            INNER JOIN friendsrelation
            ON fgusers3.id_user = friendsrelation.user_accepted
            WHERE friendsrelation.user_request = ".$my_rec['id_user']." AND friendsrelation.friend_status = 2 UNION
            SELECT *
            FROM fgusers3
            INNER JOIN friendsrelation
            ON fgusers3.id_user = friendsrelation.user_request
            WHERE friendsrelation.user_accepted = ".$my_rec['id_user']." AND friendsrelation.friend_status = 2";

        $result = mysql_query($qry,$this->connection);

        if(!$result)
        {
            $this->HandleDBError("Error getting friends' list");
            echo "Error";
            return false;
        }
        return $result;
    }

    function shareByMeFiles(){

        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleDBError("Error getting yourself record");
            return false;
        }

        $qry="SELECT filename, name, sharefiles.file_id, COUNT( * ) as number
            FROM sharerelation
            INNER JOIN fgusers3
            ON fgusers3.id_user = sharerelation.to_user
			INNER JOIN sharefiles
			ON sharerelation.file_id = sharefiles.file_id
            WHERE sharefiles.from_user =".$my_rec['id_user']."
            GROUP BY filename";

        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Error: Getting file name and #of shared people from database");
            return false;
        }
        return $result;
    }

    function rejectFile($file_id){
        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleDBError("Error getting yourself record");
            return "Error getting yourself record";
            return false;
        }
        $qry="DELETE FROM sharerelation 
          WHERE `file_id` = ". $file_id.
          " AND `to_user` =".$my_rec["id_user"];

        $result = mysql_query($qry, $this->connection);
        if(!$result)
        {
            $this->HandleDBError("Error rejecting");
            return "Error rejecting";
            return false;
        }

        return true;
    }

    function getNewSharedWithMe(){
        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleDBError("Error getting yourself record");
            return false;
        }
        $qry="SELECT filename
            FROM sharerelation
			INNER JOIN sharefiles
			ON sharerelation.file_id = sharefiles.file_id
            WHERE sharerelation.sharing_status=1
            AND sharerelation.to_user =".$my_rec['id_user'];

        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Error: Getting new shared file to me");
            return false;
        }
        $qry="UPDATE sharerelation
            SET sharerelation.sharing_status=2
            WHERE sharerelation.sharing_status=1
            AND sharerelation.to_user=".$my_rec['id_user'];

        $result2 = mysql_query($qry, $this->connection);

        if (!$result2) {
            $this->HandleDBError("Error: Getting new shared file to me");
            return false;
        }
        return $result;
    }

    function shareWithMe(){

        $my_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$my_rec))
        {
            $this->HandleDBError("Error getting yourself record");
            return false;
        }

        $qry="SELECT filename, sharefiles.file_id, name, fgusers3.id_user, fgusers3.username
            FROM sharerelation
            INNER JOIN sharefiles
			ON sharerelation.file_id = sharefiles.file_id
            INNER JOIN fgusers3
            ON fgusers3.id_user = sharefiles.from_user
            WHERE  sharerelation.sharing_status > 0
            AND sharerelation.to_user =".$my_rec['id_user'];


        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Inserting sharing info to database");
            return false;
        }
        return $result;
    }

    function revokeAllRecievers($file_id){

        $mov = "SELECT * FROM `testdb`.`sharefiles` WHERE `sharefiles`.`file_id` = ".$file_id;
        $qry = "DELETE FROM `testdb`.`sharefiles` WHERE `sharefiles`.`file_id` = ".$file_id;

        $this->DBLogin();
        $move_data = mysql_query($mov, $this->connection);
        if(!$move_data){
            $this->HandleDBError("Cannot Move File Back");
            echo "<script>alert('Cannot Move File Back')</script>";
        }
        else{
            $data_link = mysql_fetch_assoc($move_data);
            $move_to = dirname( dirname(__FILE__) )."/user_database".$this->getPath()."/";
            $move_to = $move_to.$data_link["original_directory"].$data_link["filename"];
            $move_from = dirname( dirname(__FILE__) )."/user_database/global_share".$this->getPath()."/".$data_link["original_directory"].$data_link["filename"];
            echo "<script>alert('move from: ".$move_from."')</script>";
            echo "<script>alert('move to : ".$move_to."')</script>";
            copy($move_from, $move_to);
            unlink($move_from);
        }

        $result = mysql_query($qry, $this->connection);
        if (!$result) {
            $this->HandleDBError("Cannot revoke");
            return false;
        }

        return true;
    }

    function modifyRecievers($receivers, $file_id){

        if (sizeof($receivers) == 0)
            return revokeAllRecievers($file_id);

        $qry="UPDATE sharerelation
            SET `sharing_status` = `sharing_status`+100
            WHERE `sharing_status` > 0
            AND `sharing_status` < 100
            AND `file_id`=".$file_id;

        $this->DBLogin();

        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Error: Changing current sharing status to temp values");
            return false;
        }

        foreach($receivers as $receiver) {
            $qry="INSERT INTO sharerelation (`file_id`, `to_user`, `sharing_status`)
    VALUES (".$file_id.", ".$receiver.", 1)
        ON DUPLICATE KEY UPDATE `sharing_status`=`sharing_status`-100";
            $result = mysql_query($qry, $this->connection);

            if (!$result) {
                $this->HandleDBError("Error: Adding new sharing info");
                return false;
            }
        }

        $qry="DELETE FROM sharerelation
WHERE sharerelation.sharing_status > 100
AND sharerelation.file_id=".$file_id;

        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Error: Removing new unshared users");
            return false;
        }
        return true;
    }

    function shareFile($useremail, $receivers, $filename, $file_path){

        $my_rec = array();


        if(!$this->GetUserFromEmail($useremail,$my_rec))
        {
            $this->HandleDBError("Error getting yourself record");
            return false;
        }

        $path_to_share_file = "{$_SERVER['DOCUMENT_ROOT']}/vshar/user_database/".$my_rec['username']."_".$my_rec['id_user']."/".$file_path.'/'.$filename;

        $my_share_dir = "{$_SERVER['DOCUMENT_ROOT']}/vshar/user_database/global_share/".$my_rec['username']."_".$my_rec['id_user']."/".$filename;

        $qry = "INSERT INTO  `testdb`.`sharefiles` (
        `file_id` ,
`filename` ,
`original_directory` ,
`from_user`
)
VALUES (
    NULL ,  '".$filename."',  '".$file_path."',  '".$my_rec['id_user']."'
)";
        $result = mysql_query($qry, $this->connection);

        if (!$result) {
            $this->HandleDBError("Error: Cannot add new file sharing");
            return false;
        }
        foreach($receivers as $receiver) {
            $qry = "INSERT INTO  `testdb`.`sharerelation` (
                `file_id` ,
                `to_user` ,
                `sharing_status`
                )
                VALUES (
                '".mysql_insert_id()."',  '".$receiver."',  '1'
                )";

            $result = mysql_query($qry, $this->connection);

            if (!$result) {
                $this->HandleDBError("Error: Inserting file relations");
                return false;
            }
        }
        if(!copy($path_to_share_file, $my_share_dir)){
            echo '<script>alert("Wrong Directory")</script>';
            $this->HandleDBError("Error: Copy File. Path to share file: ".$path_to_share_file." Path to share_dir: ".$my_share_dir);
        }
        if(!unlink($path_to_share_file)){
            $this->HandleDBError("Error: Ulink File");
        }
        return $path_to_share_file;
    }

    function getPath(){
        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        $path = "/".$user_rec['username']."_".$user_rec['id_user'];
        return $path;
    }
}
?>
