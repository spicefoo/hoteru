<?php
//get the form elements and store them in variables
$name=$_POST["name"];
$email=$_POST["email"];

echo $name;
echo $email;

//Redirects to the specified page
header("Location: ".get_site_url()."/contact-form");