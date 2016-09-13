<?php

//Kapcsolat létrehozása
$MySqliLink = mysqli_connect('localhost', 'proba', 'proba', 'proba');

//Kapcsolat ellenőrzése
if (!$MySqliLink) {
    die('Kapcsolódási hiba (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

//Változók
$wsNev = 'proba';
$CegNev = 'proba';
$CegCim = 'proba';
$CegTel = '11111111111';
?>
