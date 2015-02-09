<?php 
session_start();

include 'connection.php';

if (isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['pass2']) && isset($_POST['email']))
	{ 

//Prevent SQL injections 

$user = mysql_real_escape_string($_POST['user']); 
$pass = mysql_real_escape_string($_POST['pass']);
$pass2 = mysql_real_escape_string($_POST['pass2']);
$email= mysql_real_escape_string($_POST['email']); 


 
//Check to see if username exists 
$sql = mysql_query("SELECT user FROM users WHERE user = '".$user."'");

if (mysql_num_rows($sql)>0) 
		{ 
die ("Username taken."); 
		} 

if ($pass!=$pass2) 
	{
die ("passwords don't match."); 
		} 
if (strlen($user)<5) 
		{ 
die ("username should contain at least 5 characters"); 
		} 

$sql2 = "INSERT INTO `cunhal01_a`.`users` (`ID` ,`user` ,`pass`, `email`, `DP`) VALUES ( NULL, '$user', AES_encrypt('$pass', '$pass'), '$email', 1)";


mysql_query($sql2) or die ('error registering');

echo "Account created for", " ", $user;
 
echo "<SCRIPT>";
echo "setTimeout('self.close()', 5000 ) ";
echo "</SCRIPT>";
echo "</br></br></br></br></br>";
}
?>

<!DOCTYPE HTML>
<html>
<head>    <script>

        function validate()
        {
            if (!document.forms.registration.email.value.match(/.+@.+\.edu$/))
            {
                alert("You must provide a .edu email adddress.");
                return false;
            }
            else if (document.forms.registration.pass.value == "")
            {
                alert("You must provide a password.");
                return false;
            }
            else if (document.forms.registration.pass.value != document.forms.registration.pass2.value)
            {
                alert("You must provide the same password twice.");
                return false;
            }
           
            return true;
        }

    </script>

</head>
<body>
<form action="register.php" method="post" onsubmit="return validate()" target="_blank">


<table>
        <tr>

          <td><input name="user" type="text" value="username" style="color:#ccc;" onfocus="this.value = this.value=='username' ? '' : this.value; this.style.color='#000';" onfocusout="this.value = this.value == '' ? this.value = 'username' : this.value; this.value=='username' ? this.style.color='#ccc' : this.style.color='#000'"/></td>
          <td><input name="email" type="email" style="color:#ccc;" value="email" onfocus="this.value = this.value=='email' ? '' : this.value; this.style.color='#000';" onfocusout="this.value = this.value == '' ? this.value = 'email' : this.value; this.value=='email' ? this.style.color='#ccc' : this.style.color='#000'"/></td>

	</tr>
        <tr>

          <td><input name="pass" type="password" style="color:#ccc;" value="password" onfocus="this.value = this.value=='password' ? '' : this.value; this.style.color='#000';" onfocusout="this.value = this.value == '' ? this.value = 'password' : this.value; this.value=='password' ? this.style.color='#ccc' : this.style.color='#000'"/></td>
	   <td><input name="pass2" type="password" style="color:#ccc;" value="password" onfocus="this.value = this.value=='password' ? '' : this.value; this.style.color='#000';" onfocusout="this.value = this.value == '' ? this.value = 'password' : this.value; this.value=='password' ? this.style.color='#ccc' : this.style.color='#000'"/></td>


        </tr>




        <tr>
          <td><input type="submit" value="Register" /></td>
        </tr>
      </table> 
</form> 
</body>
</html>
