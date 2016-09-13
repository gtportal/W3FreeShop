
<?php


//------------------------------------------------------------------------------------------------------------------
// Bejelentkezés ŰRLAP ÖSSZEÁLLÍTÁSA
//------------------------------------------------------------------------------------------------------------------


function Kiir_Bejelentkezes($ErrorStr)
{
global $MySqliLink; 
  $HTMLkod = "<div id='div_Bejelentkezes'>\n";
  $HTMLkod .= "<form action='#' method='post' id='form_Bejelentkezes'>\n";
  $HTMLkod .= "<fieldset class='Adatbiztonsag'>  <legend> Bejelentkezés </legend>\n";

  if ($ErrorStr>'') {$HTMLkod .= "<p class='ErrorStr'> $ErrorStr  </p>\n";}

  $HTMLkod .= "<p><label for='fnev' class='label_1'>Felhasználónév</label><br> \n";
  $HTMLkod .= "<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value=' '><span></span></p>\n"; 
 
  $HTMLkod .= "<p><label for='Fjelszo' class='label_1'>Jelszó</label> <br>\n"; 
  $HTMLkod .= "<input type='password' name='Fjelszo' id='Fjelszo' placeholder='Jelszó' value='' >\n";
  $HTMLkod .= "<span></span></p>\n";
  $HTMLkod .= "<input type='submit' name='submit_Bejelentkezes' value='Bejelentkezés' >\n";
  $HTMLkod .= "</fieldset>\n";
  $HTMLkod .= "</form></div>\n";
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// Kijelentkezés ŰRLAP ÖSSZEÁLLÍTÁSA
//------------------------------------------------------------------------------------------------------------------


function Kiir_Kijelentkezes($ErrorStr)
{
  global $mm_felhasznalo; 
  $HTMLkod = "<div id='div_Kijelentkezes'> \n";
  $HTMLkod .= "<form action='#' method='post' id='form_Kijelentkezes'>\n";
  $HTMLkod .= "<fieldset class='Adatbiztonsag'>  <legend> Üdv.:$mm_felhasznalo  </legend>";
  $HTMLkod .= "<input type='submit' name='submit_Kijelentkezes' value='Kejelentkezés' >";
  $HTMLkod .= "</fieldset>";
  $HTMLkod .= "</form></div>";
  return $HTMLkod;
}



//------------------------------------------------------------------------------------------------------------------
// BEJELENTKEZÉS
//------------------------------------------------------------------------------------------------------------------

if ($_POST['submit_Bejelentkezes'] == 'Bejelentkezés') {
  $Fnev    = tiszta_szo($_POST['Fnev']);
  $Fjelszo = tiszta_szo($_POST['Fjelszo']); 

  $Bejelentkezes_UZ = "";
  $r_ip = getip(); 
  $JSZ  = $Fjelszo = md5($Fjelszo); 

  $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev'"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba BE 01 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $JSZ1   = $row['Fjelszo']; //echo "<h1>$JSZ----$JSZ1 -- $Fnev </h1>";
  $Fszint = $row['Fszint'];
  $Fhiba  = $row['Fhiba']; 
  $FID    = $row['id'];  
  if ($Fhiba > 4) {
    $SelectStr = "SELECT DATE_ADD(Datum,INTERVAL 1 HOUR) AS Ido_Ujraelerheto, NOW() AS Ido_aktualis 
                  FROM felhasznalo_mod WHERE Fid=$FID ORDER by Datum DESC"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba BE 02 ");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $IDO       = $row['Ido_Ujraelerheto']; 
    $AktIDO    = $row['Ido_aktualis']; 
    if ($IDO > $AktIDO) { 
      $Bejelentkezes_UZ = "5 hibás bejelenkezés miatt $IDO -ig a fiók felfüggesztésre került. <br>($AktIDO)"; 
    } else {
      $UpdateStr = "UPDATE felhasznalo_reg SET Fhiba = 0 WHERE Fnev='$Fnev'" ; 
      if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba BE 03 ");}
      $Fhiba = 0;
    }
  }
  if ($Fhiba < 5) {
    if ($JSZ == $JSZ1) {
      $UpdateStr = "UPDATE felhasznalo_reg SET Fhiba = 0 WHERE Fnev='$Fnev'";
      if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba BE 04 ");}

      $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$FID.",'".$r_ip."','Bejelntkezés',NOW())";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba BE 05 ");}

      $SelectStr = "SELECT * FROM felhasznalo_mod WHERE Fid=$FID AND Ftev='Bejelntkezés'"; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba BE 06 ");
      $DbSzam    = mysqli_num_rows($result);
      $DbSzamTorol  = $DbSzam - 5;
      if ($DbSzamTorol>0) {
        $DeleteStr  = "Delete FROM felhasznalo_mod WHERE Ftev='Bejelntkezés' ORDER BY Datum LIMIT $DbSzamTorol";
        if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba BE 07 ");}
      }
      $hozzaferes     = $_SESSION['hozzaferesi_szint']=$Fszint;    
      $mm_felhasznalo = $_SESSION['munkamenet_felhasznalo'] = $Fnev;
    } else {
      $Fhiba++; 
      $Bejelentkezes_UZ .= "Hibás felhasználónév vagy jelszó!<br> $Fhiba. rossz próbálkozás.<br> 
         Az ötödik után a felhasználó 1 órára fel lesz függesztve.";
      $UpdateStr = "UPDATE felhasznalo_reg SET Fhiba = '$Fhiba' WHERE Fnev='$Fnev'"; 
      if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba BE 08 ");}
    
      if ($FID > 0) {
        $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$FID.",'".$r_ip."','Hibás jelszó',NOW())";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba BE 09 ");}
        $SelectStr = "SELECT * FROM felhasznalo_mod WHERE Fid=$FID AND Ftev='Hibás jelszó'"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba BE 10 ");
        $DbSzam    = mysqli_num_rows($result);
        $DbSzamTorol  = $DbSzam - 5;
        if ($DbSzamTorol>0) {
           $DeleteStr = "Delete FROM felhasznalo_mod WHERE Fid=$FID AND Ftev='Hibás jelszó' 
             ORDER BY Datum LIMIT $DbSzamTorol";
           if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba BE 09 ");}
        }
      } 
      $hozzaferes     = $_SESSION['hozzaferesi_szint']      = 1;    
      $mm_felhasznalo = $_SESSION['munkamenet_felhasznalo'] = "";
    }
  }
}

//------------------------------------------------------------------------------------------------------------------
// KIJELENTKEZÉS
//------------------------------------------------------------------------------------------------------------------

if ($_POST['submit_Kijelentkezes'] == 'Kejelentkezés') { 
  $hozzaferes = $_SESSION['hozzaferesi_szint']=1;    
  $mm_felhasznalo = $_SESSION['munkamenet_felhasznalo'] = '';
}

$AktTitle = '';
$Descript = '';
$o_latszik = 1;

//------------------------------------------------------------------------------------------------------------------
// MUNKAMENET UTOLSÓ HOZZÁFÉRÉS     ???????????????????????????????????????
//------------------------------------------------------------------------------------------------------------------

$akt_munkamenet = session_id ();
$mm_utolso_click_time = $_SESSION['munkamenet_utolso_click_time'];
if (time() > ($mm_utolso_click_time + 3600 )) {   
  if (1 < ($_SESSION[hozzaferesi_szint])) { 
    $feladat = "START"; 
    // require_once("oldalak/kosar_torol.php");  
    $_SESSION['hozzaferesi_szint']      = 1;
    $_SESSION['munkamenet_felhasznalo'] = '';
  }
}
$_SESSION['munkamenet_utolso_click_time'] = time();



?>
