<?php

//Kapcsolat létrehozása
$MySqliLink = mysqli_connect('localhost', '#_user', '#_password', '#_db');

//Kapcsolat ellenőrzése
if (!$MySqliLink) {
    die('Kapcsolódási hiba (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

//Változók
