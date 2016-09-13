<?php


$wsNev =''; $CegNev =''; $CegCim =''; $CegTel =''; $DBNev =''; $DBJelszo =''; $AdNev =''; $AdfNev =''; $AdJelszo =''; $ErrStr='';
$Err=0;

//------------------------------------------------------------------------------------------------------------------
//  A TARTALOM MEGJELENÍTÉSE
//------------------------------------------------------------------------------------------------------------------

function Kiir_Tartalom()
{
  echo  "\n\n<div id='tartalom'>\n ";
  if ($_POST['submit1'] == 'Mehet') { Kezel_SetupUrlap(); } 
  Kiir_SetupUrlap();
  echo  "\n\n</div>";
}

//------------------------------------------------------------------------------------------------------------------
//  A SETUP ŰRLAP ADATAINAK KEZELÉSE
//------------------------------------------------------------------------------------------------------------------

function Kezel_SetupUrlap()
{
global $wsNev, $CegNev, $CegCim, $CegTel, $DBNev, $DBJelszo, $AdNev, $AdfNev, $DBfNev, $AdJelszo, $ErrStr, $Err, $MySqliLink;
  $HTMLkod =''; $ErrStr='';

  if ($_POST['wsNev'] > '')  {$wsNev = tiszta_szov($_POST['wsNev']);} else {$ErrStr.='ERR00 ';}

  if ($_POST['CegNev'] > '') {$CegNev = tiszta_szov($_POST['CegNev']);} else {$ErrStr.='ERR01 ';}
  if ($_POST['CegCim'] > '') {$CegCim = tiszta_szov($_POST['CegCim']);} else {$ErrStr.='ERR02 ';}
  if ($_POST['CegTel'] > '') {$CegTel = tiszta_szov($_POST['CegTel']);} else {$ErrStr.='ERR03 ';}

  if ($_POST['DBNev'] > '')    {$DBNev = tiszta_szov($_POST['DBNev']);} else {$ErrStr.='ERR04 ';}
  if ($_POST['DBfNev'] > '') {$DBfNev = tiszta_szov($_POST['DBfNev']);} else {$ErrStr.='ERR05 ';}
  if (($_POST['DBJelszo'] > '') and ($_POST['DBJelszo1'] > '') and ($_POST['DBJelszo'] == $_POST['DBJelszo1']))
         {$DBJelszo = tiszta_szov($_POST['DBJelszo']);} else {$ErrStr.='ERR06 ';}

  if ($_POST['AdNev'] > '')    {$AdNev = tiszta_szov($_POST['AdNev']);} else {$ErrStr.='ERR07 ';}
  if ($_POST['AdfNev'] > '')   {$AdfNev = tiszta_szov($_POST['AdfNev']);} else {$ErrStr.='ERR08 ';}
  if (($_POST['AdJelszo'] > '') and ($_POST['AdJelszo1'] > '') and ($_POST['AdJelszo'] == $_POST['AdJelszo1']))
         {$AdJelszo = tiszta_szov($_POST['AdJelszo']);} else {$ErrStr.='ERR09 ';}

  $AdJSZ = md5($AdJelszo);
  $TartalomStr ='';


  if ($ErrStr>'')  {$Err=1;} else {
    // Fájl beolvasása
    $FileNev ="set/start_f.php";
    $FSorok = array(); 
    $handle = @fopen($FileNev, "r");
    if ($handle) {
      while (($buffer = fgets($handle, 4096)) !== false) {$TartalomStr .= $buffer; }
      if (!feof($handle)) { $ErrorStr = "ERR10"; $Err=1;}
      fclose($handle);
    }
  
    $arr = array( "#_db" => $DBNev, "#_user" => $DBfNev, "#_password" => $DBJelszo);  
    $TartalomStr  = strtr($TartalomStr ,$arr);

    $TartalomStr  .= '$wsNev = '."'$wsNev';\n";
    $TartalomStr  .= '$CegNev = '."'$CegNev';\n";
    $TartalomStr  .= '$CegCim = '."'$CegCim';\n";
    $TartalomStr  .= '$CegTel = '."'$CegTel';\n";
    $TartalomStr  .= "?>\n";
  } 
  if ($Err==0) {
    // Fájl mentése
    $FileNev ="set/start.php";
    $all = fopen($FileNev, "w") or die("A $FileNev állományt nem lehet megnyitni!");
    fwrite($all, $TartalomStr);
    fclose($all);
  }

  // A fájl meghívásával megnyitjuk az adatbázist
  require_once("set/start.php"); 

  $HTMLkod .= "<div id='Visszajelzes'>";
  
  if ($Err==0) {require_once("oldalak/w3_DB_init.php"); }
    
  if ($Err==0) {$HTMLkod .= Letrehoz_FelhasznaloRegTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_FelhasznaloTelefonTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_FelhasznaloCimTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_FelhasznaloModTabla();}

  if ($Err==0) {$HTMLkod .= Letrehoz_CaptchaTabla();}
  if ($Err==0) {$HTMLkod .= LoadCsv_captcha_kodok('csv/captcha_kodok.csv');}

  if ($Err==0) {$HTMLkod .= Letrehoz_OldalTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_OldalTartalomTabla();}

  if ($Err==0) {$HTMLkod .= Letrehoz_TermekTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_TermekjellemzoTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_TermekLeirasTabla();}

  if ($Err==0) {$HTMLkod .= Letrehoz_KepTabla();}

  if ($Err==0) {$HTMLkod .= Letrehoz_KocsiTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_MegrendelesTabla();}
  if ($Err==0) {$HTMLkod .= Letrehoz_MegrendeltTermekTabla();}

  if ($Err==0) {$HTMLkod .= Letrehoz_LatogatoTablak();}


  // Az adminisztrátor adatainak felvétele
  $InsertIntoStr = "INSERT INTO felhasznalo_reg VALUES ('', '$AdfNev','$AdNev','$AdJSZ',' ',10,0)";
  if (!mysqli_query($MySqliLink,$InsertIntoStr))  { 
    $Err=1;  $ErrStr .= "MySqli hiba ";    
  } else { 
    $ID1= mysqli_insert_id($MySqliLink); 
    $InsertIntoStr = "INSERT INTO felhasznalo_cim VALUES ('', ".$ID1.",' ',' ',' ',' ')";
    if (!mysqli_query($MySqliLink,$InsertIntoStr))  { 
      $Err=1;  $ErrStr .= "MySqli hiba ";    
    }
    $r_ip     = getip(); 
    $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$ID1.",'".$r_ip."','Létrehozás',NOW())";
    if (!mysqli_query($MySqliLink,$InsertIntoStr))  { 
      $Err=1;  $ErrStr .= "MySqli hiba ";}
  }

  // Az demo felhasználó adatainak felvétele
  $JSZ = md5('demo');
  $InsertIntoStr = "INSERT INTO felhasznalo_reg VALUES ('', 'demo','demo','$JSZ',' ',6,0)";
  if (!mysqli_query($MySqliLink,$InsertIntoStr))  { $Err=1;
      $ErrStr .= "MySqli hiba a <b>'felhasznalo_reg'</b> tábla írásánál 
                   (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);    
  } else { 
    $ID1= mysqli_insert_id($MySqliLink); 
    $InsertIntoStr = "INSERT INTO felhasznalo_cim VALUES ('', ".$ID1.",' ',' ',' ',' ')";
    if (!mysqli_query($MySqliLink,$InsertIntoStr))  { $Err=1;
       $ErrStr .= "MySqli hiba a <b>'felhasznalo_cim'</b> tábla írásánál 
                  (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);    
    }
    $r_ip     = getip(); 
    $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$ID1.",'".$r_ip."','Létrehozás',NOW())";
    if (!mysqli_query($MySqliLink,$InsertIntoStr))  { $Err=1;
      $ErrStr .= "MySqli hiba a <b>'felhasznalo_mod'</b> tábla írásánál 
                 (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);}
  }

  $HTMLkod .= "</div>";
  echo $HTMLkod;
}


function Kiir_SetupUrlap()
{
global $wsNev, $CegNev, $CegCim, $CegTel, $DBNev, $DBfNev, $DBJelszo, $AdNev, $AdfNev, $AdJelszo, $ErrStr, $Err;


if ($_POST['submit1'] != 'Mehet') {$HTMLkod .= "<h1>Webáruház telepítése</h1>";} 
else {
  if ($Err == 0) {$HTMLkod .= "<h1>Webáruház telepítése megtörtént</h1> 
    <strong>Ne feledkezzen meg a setup.php törléséről! </strong><br> Az adotok módosítása esetén a 'Mehet' gombra kattintva újratelepítheti a szoftvert.";} 
  else {$HTMLkod .= "<h1>A telepítés során hiba történt</h1> ";} 
}

$HTMLkod .= "<div id='SetupFormDiv'><form method='post' action='#'>";

$HTMLkod .= "<fieldset class='WSAdatok'><legend> A webáruház adatai: </legend>";
if (strpos($ErrStr,'ERR00')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='wsNev' class='label_1'>A webáruház neve</label> 
       <input type='text' name='wsNev' id='wsNev' placeholder='A webáruház neve' value='$wsNev' $ErrClass >
       <span>*</span></p>";
$HTMLkod .= "</fieldset>";

$HTMLkod .= "<fieldset class='CegAdatok'><legend> Az üzemeltető (cég) adatai: </legend>";
if (strpos($ErrStr,'ERR01')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='CegNev' class='label_1'>Az üzemeltető neve</label> 
       <input type='text' name='CegNev' id='CegNev' placeholder='Felhasználónév' value='$CegNev'  $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR02')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='CegCim' class='label_1'>Az üzemeltető címe</label> 
       <input type='text' name='CegCim' id='CegCim' placeholder='Az üzemeltető címe' value='$CegCim' size='50'  $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR03')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='CegTel' class='label_1'>Az üzemeltető telefonszáma</label> 
       <input type='text' name='CegTel' id='CegTel' placeholder='Az üzemeltető telefonszáma' value='$CegTel' $ErrClass >
       <span>*</span></p>";
$HTMLkod .= "<small> Az üzemeltető adatai a webáruház láblécében lesznek olvashatók. <i>Később csak a start.php-ben módisíthatók.</i></small>";
$HTMLkod .= "</fieldset>";

$HTMLkod .= "<fieldset class='DBAdatok'><legend> Az adatbázis adatai: </legend>";
if (strpos($ErrStr,'ERR04')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='DBNev' class='label_1'>Az adatbázis neve</label> 
       <input type='text' name='DBNev' id='DBNev' placeholder='Az adatbázis neve' value='$DBNev' $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR05')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='DBfNev' class='label_1'>Az adatbázis felhasználó neve</label> 
       <input type='text' name='DBfNev' id='DBfNev' placeholder='Az adatbázis felhasználó neve' value='$DBfNev' $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR06')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='DBJelszo' class='label_1'>Az adatbázis jelszava</label> 
       <input type='password' name='DBJelszo' id='DBJelszo' placeholder='Az adatbázis jelszava' value='$DBJelszo' $ErrClass>
       <span>*</span></p>";
$HTMLkod .= "<p><label for='DBJelszo1' class='label_1'>Az adatbázis jelszava</label> 
       <input type='password' name='DBJelszo1' id='DBJelszo1' placeholder='Az adatbázis jelszó újra' value='$DBJelszo1' $ErrClass>
       <span>*</span></p>";
$HTMLkod .= "<small>A webáruház telepítéséhez egy MySQL adatbázisra van szükség, amelyet a tárhely adminisztrátora hozhat létre. <i>Az adatbázis adatai később csak a start.php-ben módisíthatók.</i></small>";
$HTMLkod .= "</fieldset>";

$HTMLkod .= "<fieldset class='AdAdatok'><legend> A adminisztrátor adatai: </legend>";
if (strpos($ErrStr,'ERR07')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='AdNev' class='label_1'>Az adminisztrátor neve</label> 
       <input type='text' name='AdNev' id='AdNev' placeholder='Az adminisztrátor Neve' value='$AdNev' $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR08')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='AdfNev' class='label_1'>Az adminisztrátor felhasználói neve</label> 
       <input type='text' name='AdfNev' id='AdfNev' placeholder='Az adminisztrátor felhasználói neve' value='$AdfNev' $ErrClass>
       <span>*</span></p>";
if (strpos($ErrStr,'ERR09')!== false) { $ErrClass="class='Error'"; } else { $ErrClass=""; }
$HTMLkod .= "<p><label for='AdJelszo' class='label_1'>Az adminisztrátor jelszava</label> 
       <input type='password' name='AdJelszo' id='AdJelszo' placeholder='Az adminisztrátor jelszava' value='$AdJelszo' $ErrClass>
       <span>*</span></p>";
$HTMLkod .= "<p><label for='AdJelszo1' class='label_1'>Az admin jelszó újra</label> 
       <input type='password' name='AdJelszo1' id='AdJelszo1' placeholder='Az adminisztrátor jelszava újra' value='$AdJelszo1' $ErrClass>
       <span>*</span></p>";

$HTMLkod .= "<small>Az adminisztrátor szerkesztheti az oldalak tartalmát, felveheti a termékeket és  módosíthatja a termékek jellemzőit. Kezelheti a megrendeléseket. <i>Adatai a felhasználói név kivételével a szerkesztés menüpontban módosíthatók.</i></small>";
$HTMLkod .= "</fieldset>";

$HTMLkod .= "<input type='submit' name='submit1' value='Mehet' >";

$HTMLkod .= "</form> </div>";

  $HTMLkod = "<div id='FormDiv'>".$HTMLkod."<div>";

echo $HTMLkod;

}


?>
