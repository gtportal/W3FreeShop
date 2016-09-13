

<?php

//------------------------------------------------------------------------------------------------------------------
// Regisztrációs ŰRLAP ÖSSZEÁLLÍTÁSA
//------------------------------------------------------------------------------------------------------------------

function Kiir_RegUrlap($ErrorStr)
{
global $MySqliLink, $mm_felhasznalo, $hozzaferes;
  $Fnev    = $mm_felhasznalo;
  $HTMLkod ='';

  if ($mm_felhasznalo > '') {
    // Bejelentkezett felhasználók adatainak betöltése adatbázisból
    $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev = '$Fnev'";  
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 01 ");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $id   = $row['id'];
    $Fnev = $row['Fnev']; 
    $Fszemnev = $row['Fszemnev'];
    $Fjelszo  = $row['Fjelszo'];
    $Femail   = $row['Femail'];
    $Fszint   = $row['Fszint'];
    $Fhiba    = $row['Fhiba']; 
    ;
    // Cím beolvasása
    $SelectStr = "SELECT * FROM felhasznalo_cim WHERE Fid = $id"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 02 ");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $Forszag = $row['Forszag'];
    $Fvaros  = $row['Fvaros'];
    $Firszam = $row['Firszam'];
    $Fcim    = $row['Fcim'];
    
    //Telefonszámok beolvasása
    $Ftelszam  =''; $Ftelszam1 ='';
    $SelectStr = "SELECT * FROM felhasznalo_telefon WHERE Fid = $id";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 03 ");
    $k = 0;
    while($row = mysqli_fetch_array($result))
    {
      if ($k == 1) {$Ftelszam1 = $row['Ftelszam']; $k = $k + 1;} 
      if ($k == 0) {$Ftelszam = $row['Ftelszam']; $k = $k + 1; }
    }
    mysqli_free_result($result);
  } else {
    // Új felhasználó esetén a változók kezdeti értékének megadása
    $id = '';  $Fnev = ''; $Fszemnev = ''; $Fjelszo = ''; $Femail = ''; $Fszint = 5; $Fhiba = 0; $Forszag = ''; $Fvaros = '';
    $Firszam = ''; $Fcim = ''; $Ftelszam = ''; $Ftelszam = '';
  }
  if ($_POST['form_RegUrlap'] > '')  { 
    //Űrlap adatainak megtisztítása
    if ($_POST['Fnev'] > '')       { $Fnev       = ''.tiszta_szov($_POST['Fnev']); }
    if ($_POST['Fjelszo'] > '')    { $Fjelszo    = ''.tiszta_szov($_POST['Fjelszo']);}
    if ($_POST['Fujjelszo'] > '')  { $Fujjelszo  = ''.tiszta_szov($_POST['Fujjelszo']);}
    if ($_POST['Fujjelszo1'] > '') { $Fujjelszo1 = ''.tiszta_szov($_POST['Fujjelszo1']);}

    if ($_POST['Fszemnev'] > '')  { $Fszemnev  = ''.tiszta_szov($_POST['Fszemnev']);} 
    if ($_POST['Femail'] > '')    { $Femail    = ''.tiszta_szov($_POST['Femail']);}
    if ($_POST['Ftelszam'] > '')  { $Ftelszam  = ''.tiszta_szov($_POST['Ftelszam']);}
    if ($_POST['Ftelszam1'] > '') { $Ftelszam1 = ''.tiszta_szov($_POST['Ftelszam1']);}

    if ($_POST['Forszag'] > '') { $Forszag = ''.tiszta_szov($_POST['Forszag']);}
    if ($_POST['Fvaros'] > '')  { $Fvaros  = ''.tiszta_szov($_POST['Fvaros']);}
    if ($_POST['Firszam'] > '') { $Firszam = ''.tiszta_szov($_POST['Firszam']);}
    if ($_POST['Fcim'] > '')    { $Fcim    = ''.tiszta_szov($_POST['Fcim']);}
  }
  // HTML "űrlapfej" összeállítása
  if ($mm_felhasznalo > '') {$RegUrlapCim='Felhasználói adatok módosítása'; } else {$RegUrlapCim='Felhasználói regisztráció'; }
  $HTMLkod .= "<div id='DIVigazit'> <div id='div_RegUrlap'> <h1>$RegUrlapCim</h1>\n";
  if ($ErrorStr == 'OK') {$HTMLkod .= "<p class='RegInfo'>A felhasználói adatok változtak!</p>\n";}
  if ($hozzaferes==6)    {$HTMLkod .= "<p class='JszInfo'> A demo felhasználó adatai nem módosíthatók. </p>\n";}

  $HTMLkod .= "<form action='#' method='post' id='form_RegUrlap'>\n";
  $HTMLkod .= "<input type='hidden' name='form_RegUrlap' value='form_RegUrlap'>\n";

  // Űrlap összeállítása
  // A korábban hibásan elküldött űrlapelemek az Error osztályba kerülnek
  if ($mm_felhasznalo>'') 
  {  
    // Létező felhasználó felhasználóneve nem változhat
    $HTMLkod .= "<p><label for='Fszemnev' class='label_1'>Név</label> \n";
    if (strpos($ErrorStr,'ERR02')!== false) 
      {$HTMLkod .= "<input type='text' name='Fszemnev' id='Fszemnev' placeholder='Vezetéknév keresztnév' class='Error' value='$Fszemnev' required >\n";} else
      {$HTMLkod .= "<input type='text' name='Fszemnev' id='Fszemnev' placeholder='Vezetéknév keresztnév' value='$Fszemnev' required >\n";}    
    $HTMLkod .= "<fieldset class='Nev'>  <legend> Felhasználónév: </legend>\n";
    $HTMLkod .= "<p><label for='fnev' class='label_1'>Felhasználónév</label> \n";
    $HTMLkod .= "<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value='$Fnev' readonly> \n";
    $HTMLkod .= "<span>Nem változhat!</span></p>\n";
    $HTMLkod .= "</fieldset>\n";
  } else {
    // Új felhasználó felhasználónevét és jelszavát is meg kell adja (az utóbbit kétszer)
    $HTMLkod .= "<p><label for='Fszemnev' class='label_1'>Név</label> \n";
    if (strpos($ErrorStr,'ERR02')!== false) 
      {$HTMLkod .= "<input type='text' name='Fszemnev' id='Fszemnev' placeholder='Vezetéknév keresztnév' class='Error' value='$Fszemnev' required >\n";} else
      {$HTMLkod .= "<input type='text' name='Fszemnev' id='Fszemnev' placeholder='Vezetéknév keresztnév' value='$Fszemnev' required >\n";} 
    $HTMLkod .= "<span>* Minimum 6 karakter</span></p>\n";

    $HTMLkod .= "<fieldset class='jelszo'>  <legend> Felhasználónév és jelszó: </legend>\n";	
    $HTMLkod .= "<p><label for='fnev' class='label_1'>Felhasználónév</label> \n";
    if ((strpos($ErrorStr,'ERR11')!== false) or (strpos($ErrorStr,'ERR14')!== false) )
      {$HTMLkod .= "<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value='$Fnev' class='Error' required >\n";} else
      {$HTMLkod .= "<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value='$Fnev' required >\n";} 
    $HTMLkod .= " <span>Minimum 3 karakter</span></p>\n";  

    if (strpos($ErrorStr,'ERR11')!== false)
      {$HTMLkod .= "<p class='Error'> Ez a név már foglalt. </p>\n";}

    $HTMLkod .= "<p><label for='Fujjelszo' class='label_1'>Jelszó</label>\n"; 
    if ((strpos($ErrorStr,'ERR12')!== false) or (strpos($ErrorStr,'ERR13')!== false) )
      {$HTMLkod .= "<input type='password' name='Fujjelszo' id='Fujjelszo' placeholder='Jelszó' value='' class='Error' required >\n";} else
      {$HTMLkod .= "<input type='password' name='Fujjelszo' id='Fujjelszo' placeholder='Jelszó' value='' required >\n";}
    $HTMLkod .= "<span>* Minimum 6 karakter</span></p>\n";
    $HTMLkod .= "<p><label for='Fujjelszo1' class='label_1'>Jelszó újra</label>\n"; 
    if ((strpos($ErrorStr,'ERR12')!== false) or (strpos($ErrorStr,'ERR13')!== false) )
      {$HTMLkod .= "<input type='password' name='Fujjelszo1' id='Fujjelszo1' placeholder='Jelszó újra' value='' class='Error' required >\n";} else
      {$HTMLkod .= "<input type='password' name='Fujjelszo1' id='Fujjelszo1' placeholder='Jelszó újra' value='' required >\n";} 
    $HTMLkod .= "<span>* </span></p>\n";
    $HTMLkod .= "<p> Mindkét mezőbe írja be a választott jelszót!</p>\n"; 
    $HTMLkod .= "</fieldset>\n";
  } 
  // Az email és egy telefonszám megadása kötelező
  $HTMLkod .= "<fieldset class='Elerhetoseg'>  <legend> Elérhetőség: </legend>\n";  
  $HTMLkod .= "<p><label for='Femail' class='label_1'>Email cím</label> \n";
  if (strpos($ErrorStr,'ERR03')!== false) 
    {$HTMLkod .= "<input type='email' name='Femail' id='Femail' placeholder='Email' value='$Femail' class='Error' required>\n";} else
    {$HTMLkod .= "<input type='email' name='Femail' id='Femail' placeholder='Email' value='$Femail' required>\n";} 
  $HTMLkod .= "<span>* Szabványos Email-cím</span></p>\n";

  $HTMLkod .= "<p><label for='Ftelszam' class='label_1'>Telefonszám 1</label>\n ";
  if (strpos($ErrorStr,'ERR04')!== false) 
    {$HTMLkod .= "<input type='text' name='Ftelszam' id='Ftelszam' placeholder='Telefonszám 1' value='$Ftelszam' class='Error' required>\n";} else
    {$HTMLkod .= "<input type='text' name='Ftelszam' id='Ftelszam' placeholder='Telefonszám 1' value='$Ftelszam' required>\n";} 
  $HTMLkod .= "<span>* Minimum 7 számjegy</span></p>\n";

  $HTMLkod .= "<p><label for='Ftelszam1' class='label_1'>Telefonszám 2</label>\n ";
  if (strpos($ErrorStr,'ERR05')!== false) 
    {$HTMLkod .= "<input type='text' name='Ftelszam1' id='Ftelszam1' placeholder='Telefonszám 2' value='$Ftelszam1' class='Error' >\n";} else
    {$HTMLkod .= "<input type='text' name='Ftelszam1' id='Ftelszam1' placeholder='Telefonszám 2' value='$Ftelszam1' >\n";} 
  $HTMLkod .= "<span></span></p>\n";
  $HTMLkod .= "</fieldset>\n";

  $HTMLkod .= "<fieldset class='Cim'>  <legend> Cím: </legend>\n";
  $HTMLkod .= "<p><label for='Forszag' class='label_1'>Ország</label>\n"; 
  $HTMLkod .= "<input type='text' name='Forszag' id='Forszag' placeholder='Ország' value='$Forszag' >\n";
  $HTMLkod .= "<span></span></p>\n";

  $HTMLkod .= "<p><label for='Fvaros' class='label_1'>Város</label>\n"; 
  $HTMLkod .= "<input type='text' name='Fvaros' id='Fvaros' placeholder='Város' value='$Fvaros' >\n";
  $HTMLkod .= "<span></span></p>\n";

  $HTMLkod .= "<p><label for='Firszam' class='label_1'>Irányító szám</label>\n"; 
  $HTMLkod .= "<input type='text' name='Firszam' id='Firszam' placeholder='Irányító szám' value='$Firszam' >\n";
  $HTMLkod .= "<span></span></p>\n";

  $HTMLkod .= "<p><label for='Fcim' class='label_1'>Cím</label>\n"; 
  $HTMLkod .= "<input type='text' name='Fcim' id='Fcim' placeholder='Cím' value='$Fcim' >\n";
  $HTMLkod .= "<span></span></p>\n";
  $HTMLkod .= "</fieldset>\n";

  if ($mm_felhasznalo=='') {
    //Az új felhasználóknak a felhasználási feltételeket is el kell fogadnia
    $HTMLkod .= "<fieldset class='FFeltetelek'>  <legend> Felhasználási feltételek: </legend>\n";
    if (strpos($ErrorStr,'ERR15')!== false) 
      {$HTMLkod .= "<p><input type='checkbox' name='Feltetelek' id='Feltetelek'  value='Feltetelek' class='Error' required >\n";
       $HTMLkod .= "<label for='Feltetelek' class='Error' >A felhasználási feltételeket elfogadom.</label>\n"; 
      } else
      {$HTMLkod .= "<p><input type='checkbox' name='Feltetelek' id='Feltetelek'  value='Feltetelek' required >\n"; 
      $HTMLkod .= "<label for='Feltetelek' class='label_1' style='width:auto;'>A felhasználási feltételeket elfogadom.</label>\n"; }
    $HTMLkod .= "<span></span></p>\n";
    $HTMLkod .= "</fieldset>\n";
  }
 
  if ($mm_felhasznalo>'') {
    // A régi felhasználó jelszavával azonosítja magát
    $HTMLkod .= "<fieldset class='Adatbiztonsag'>  <legend> Adatbiztonság: </legend>\n";
    $HTMLkod .= "<p> Adja meg az érvényes jelszavát!</p>\n"; 
    $HTMLkod .= "<p><label for='Fjelszo' class='label_1'>Jelszó</label>\n"; 
    if (strpos($ErrorStr,'ERR01')!== false) 
      {$HTMLkod .= "<input type='password' name='Fjelszo' id='Fjelszo' placeholder='Jelszó' value='' class='Error'>\n";} else
      {$HTMLkod .= "<input type='password' name='Fjelszo' id='Fjelszo' placeholder='Jelszó' value='' >\n";}
    $HTMLkod .= "<span>*</span></p>\n";
    $HTMLkod .= "</fieldset>\n";
  } else
  {
    $SelectStr = "SELECT * FROM captcha_kodok"; 
    // Új felhasználó captcha kódot is meg kell adjon
    // A captcha kód mutatója munkamenetváltozóba kerül
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 04 ");
    $captchaDb = mysqli_num_rows($result); mysqli_free_result($result);
    $_SESSION['mm_captchaMut'] = $captchaMut  = rand(1,$captchaDb);
    $SelectStr = "SELECT * FROM captcha_kodok WHERE id=$captchaMut LIMIT 1"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 05 ");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $Kerdes = $row['CKerdes'];

    $HTMLkod .= "<p><label for='captcha' class='label_1'>$Kerdes</label>\n"; 
    if (strpos($ErrorStr,'ERR16')!== false)
      {$HTMLkod .= "<input type='text' name='captcha' id='captcha' placeholder='Mennyi ?' value='' class='Error' required > \n";} else
      {$HTMLkod .= "<input type='text' name='captcha' id='captcha' placeholder='Mennyi ?' value='' required >\n";}
    $HTMLkod .= "<span>* A művelet eredménye (Szám)</span></p>\n";
  }
  $HTMLkod .= "<input type='reset' name='reset' value='Alaphelyzet' >\n";
  $HTMLkod .= "<input type='submit' name='submit' value='Módosítás' >\n";
  $HTMLkod .= "</form></div></div>\n";

  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// REGISZTRÁCIÓS ADATOK MÓDOSÍTÁSA 
//------------------------------------------------------------------------------------------------------------------
function RegAdatModosit()
{
global $MySqliLink, $mm_felhasznalo, $mm_captchaMut, $hozzaferes;
//ERR00=Nincs Fnev; ERR01=Hibás jelszó; ERR02=Rövíd személynév; ERR03=Rövíd Email cím; ERR04=Rövíd 1. telefonszám; ERR04=Rövíd 2. telefonszám
//ERR11=Foglalt Fnev; ERR12=Rövíd új jelszó; ERR13=Különbözó új jelszók; ERR14=Rövíd felhasználónév; ERR15=Feltételek nem lettek elfogadva; ERR16=Hibás Captcha kód;

  $HTMLkod  ='';
  $ErrorStr = '';
  $r_ip     = getip(); 
  // Az űrlap adatainak megtisztítása
  $Fnev       = ''.tiszta_szov($_POST['Fnev']); 
  $Fjelszo    = ''.tiszta_szov($_POST['Fjelszo']);
  $Fujjelszo  = ''.tiszta_szov($_POST['Fujjelszo']);
  $Fujjelszo1 = ''.tiszta_szov($_POST['Fujjelszo1']);

  $Fszemnev  = ''.tiszta_szov($_POST['Fszemnev']); 
  $Femail    = ''.tiszta_szov($_POST['Femail']);
  $Ftelszam  = ''.tiszta_szov($_POST['Ftelszam']);
  $Ftelszam1 = ''.tiszta_szov($_POST['Ftelszam1']);

  $Forszag  = ''.tiszta_szov($_POST['Forszag']);
  $Fvaros   = ''.tiszta_szov($_POST['Fvaros']);
  $Firszam  = ''.tiszta_szov($_POST['Firszam']);
  $Fcim     = ''.tiszta_szov($_POST['Fcim']);
  $Fcaptcha = ''.tiszta_szov($_POST['captcha']);

  if ($Fnev == '') {
    $ErrorStr = $ErrorStr. "ERR00 "; 
  } else {
    if ($mm_felhasznalo > '') {
      // A bejelentkezett felhasználó módosíthatja adatait
      if ($hozzaferes!=6) {
        // A "demo" felhasználó adatai nem módosíthatók
        // Tárolt adatok beolvasása
        $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RG 06 ");
        $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
        $JSZ1 = $row['Fjelszo']; 
        $id   = $row['id']; 
        // A jelszó titkosítása
        $Fjelszo = md5($Fjelszo);
        // Hibaellenőrzés
        if ($Fjelszo != $JSZ1)     {$ErrorStr = $ErrorStr.'ERR01 ';}
        if (5 > strlen($Fszemnev)) {$ErrorStr = $ErrorStr.'ERR02';}
        if (5 > strlen($Femail))   {$ErrorStr = $ErrorStr.'ERR03';}
        if (7 > strlen($Ftelszam)) {$ErrorStr = $ErrorStr.'ERR04';}
        if (strpos($Femail,"@") and strpos($Femail,".") and ((strrpos($Femail,".") > strpos($Femail,"@")))) 
         {$ErrorStr = $ErrorStr;} else {$ErrorStr = $ErrorStr.'ERR03a';}

        if ($ErrorStr == '') { 
          // Ha nincs hiba, akkor kezdődhet a feldolgozás

          // A "felhasznalo_reg" tába frissítése
          $UpdateStr = "UPDATE felhasznalo_reg SET Fszemnev = '$Fszemnev', Femail = '$Femail' WHERE Fnev='$Fnev'";
          if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba RG 07");}
          // A "felhasznalo_cim" tába frissítése
          $UpdateStr = "UPDATE felhasznalo_cim SET Forszag = '$Forszag', Fvaros = '$Fvaros', 
            Firszam = '$Firszam', Fcim = '$Fcim'  WHERE Fid = $id";
          if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba RG 08");}
          // A korábban tárolt telefonszámok törlése, a friss számok beszúrása
          $SelectStr = "Delete FROM felhasznalo_telefon  WHERE Fid = $id";
          if (!mysqli_query($MySqliLink,$SelectStr)) {die("Hiba RG 09");}
          $InsertIntoStr = "INSERT INTO felhasznalo_telefon VALUES ('', $id, '$Ftelszam')";
          if (!mysqli_query($MySqliLink,$InsertIntoStr)) {die("Hiba RG 10");}          
          if ($Ftelszam1>'') {
            $InsertIntoStr = "INSERT INTO felhasznalo_telefon VALUES ('', $id, '$Ftelszam1')";
            if (!mysqli_query($MySqliLink,$InsertIntoStr)) {die("Hiba RG 11");}
          }

          // A felhasználói adatok módosítása tényének és jellemzőinek tárolása
          $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$id.",'".$r_ip."','Adatmódosítás',NOW())";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 12");}
          // Csak az utosó 5 adatmódosítás jellemzőit tároljuk
          $SelectStr = "SELECT * FROM felhasznalo_mod WHERE Fid=$id AND Ftev='Adatmódosítás'"; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 13");
          $DbSzam    = mysqli_num_rows($result); mysqli_free_result($result);
          $DbSzamTorol  = $DbSzam - 5;
          if ($DbSzamTorol>0) {
            $DeleteStr = "Delete FROM felhasznalo_mod WHERE Fid=$id AND Ftev='Adatmódosítás' ORDER BY Datum LIMIT $DbSzamTorol";
            if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba RG 14");}
          }
        }
      }
    } else {
      // Új felhasználó esetén ellenőrizzük, hogy a felhasználói név szabad vagy sem
      $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev' LIMIT 1"; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 15");
      $rowDB = mysqli_num_rows($result); mysqli_free_result($result);
      if ($rowDB > 0) {$ErrorStr = $ErrorStr. "ERR11 ";  }
      // További hibaellenőrzések
      if (6 > strlen($Fujjelszo))    {$ErrorStr = $ErrorStr.'ERR12';}
      if ($Fujjelszo != $Fujjelszo1) {$ErrorStr = $ErrorStr.'ERR13 ';}
      if (3 > strlen($Fnev))     {$ErrorStr = $ErrorStr.'ERR14';}
      if (6 > strlen($Fszemnev)) {$ErrorStr = $ErrorStr.'ERR02';}
      if (6 > strlen($Femail))   {$ErrorStr = $ErrorStr.'ERR03';} 
      if(!isset($_POST['Feltetelek'])) {$ErrorStr = $ErrorStr.'ERR15';} 
      if (strpos($Femail,"@") and strpos($Femail,".") and (strrpos($Femail,".") > strpos($Femail,"@")) ) 
          {$ErrorStr = $ErrorStr;} else {$ErrorStr = $ErrorStr.'ERR03a';}
      //Captcha kód ellenőrzése
      if ($mm_captchaMut>0) {
        $SelectStr = "SELECT * FROM captcha_kodok WHERE id=$mm_captchaMut LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 16");
        $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
        $Valasz = $row['CValasz'];
        if ($Fcaptcha != $Valasz) {$ErrorStr = $ErrorStr.'ERR16';}
      } else {$ErrorStr = $ErrorStr.'ERR16a';}

      if ($ErrorStr=='') {
        // Ha nincs hiba, akkor kezdődhet a feldolgozás

        // A "felhasznalo_reg" tába frissítése
        $Fujjelszo = md5($Fujjelszo);
        $InsertIntoStr = "INSERT INTO felhasznalo_reg VALUES ('', '".$Fnev."','".$Fszemnev."','".$Fujjelszo."','".$Femail."',5,0)";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 17");} else { $ID1= mysqli_insert_id($MySqliLink);} 
        // A "felhasznalo_cim" tába frissítése
        $InsertIntoStr = "INSERT INTO felhasznalo_cim VALUES ('', ".$ID1.",'".$Forszag."' 
        ,'".$Fvaros."','".$Firszam."','".$Fcim."')";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 18");}
        // A korábban tárolt telefonszámok törlése, a friss számok beszúrása
        if ($Ftelszam > '') {
          $InsertIntoStr = "INSERT INTO  felhasznalo_telefon VALUES ('', ".$ID1.",'".$Ftelszam."')";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 19");}
        }
        if ($Ftelszam1 > '') {
          $InsertIntoStr = "INSERT INTO  felhasznalo_telefon VALUES ('', ".$ID1.",'".$Ftelszam1."')";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 20");}
        }
        $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$ID1.",'".$r_ip."','Létrehozás',NOW())";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 21");}
        // Beléptetjük újdonsűlt felhasználónkat
        $hozzaferes     = $_SESSION[hozzaferesi_szint]=5;    
        $mm_felhasznalo = $_SESSION[munkamenet_felhasznalo] = $Fnev;
      }
    }
  } 
  if ($ErrorStr == '') {$ErrorStr = 'OK';}
  return $ErrorStr;
}

//------------------------------------------------------------------------------------------------------------------
// JELSZÓ MÓDOSÍTÁSA ŰRLAP ÖSSZEÁLLÍTÁSA
//------------------------------------------------------------------------------------------------------------------

function Kiir_JelszoModosit($ErrorStr)
{
global $MySqliLink, $mm_felhasznalo, $hozzaferes;

  $Fnev = $mm_felhasznalo;

  $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev'"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 22");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);

  $id   = $row['id'];
  $Fnev = $row['Fnev']; 
  $Fszemnev = $row['Fszemnev'];
  $Fjelszo  = $row['Fjelszo'];
  $Femail   = $row['Femail'];
  $Fszint   = $row['Fszint'];
  $Fhiba    = $row['Fhiba']; 

  $HTMLkod = $HTMLkod."<div id='DIVigazit'> <div id='div_JelszModosit'> <h1>Felhasználói jelszó módosítása</h1>\n";
  if ($ErrorStr == 'OK') {$HTMLkod .= "<p class='JszInfo'> A felhasználói jelszó változott. </p>\n";}
  if ($hozzaferes==6)    {$HTMLkod .= "<p class='JszInfo'> A demo felhasználó adatai nem módosíthatók. </p>\n";}

  $HTMLkod .= "<form action='#' method='post' id='form_JelszModosit'>\n";
    $HTMLkod .= "<fieldset class='LelszoMod'>  <legend> Felhasználónév, jelszó </legend>";

  $HTMLkod .= "<input type='hidden' name='form_JelszModosit' value='form_JelszModosit'>\n";
  $HTMLkod .= "<p><label for='fnev' class='label_1'>Felhasználónév</label> \n";
  $HTMLkod .= "<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value='$Fnev' readonly> <span></span></p>\n"; 
  $HTMLkod .= "<p> Adja meg az érvényes jelszavát!</p>\n"; 
  $HTMLkod .= "<p><label for='Fjelszo' class='label_1'>Régi jelszó</label>\n"; 
  if (strpos($ErrorStr,'ERR01')!== false) 
    {$HTMLkod .= "<input type='password' name='Fjelszo' id='Fjelszo' placeholder='Régi jelszó' value='' class='Error' required >\n";} else
    {$HTMLkod .= "<input type='password' name='Fjelszo' id='Fjelszo' placeholder='Régi jelszó' value='' required >\n";}
  $HTMLkod .= "<span>*</span></p>\n";
  $HTMLkod .= "<p> Mindkét mezőbe írja be az új jelszót!</p>\n"; 
  $HTMLkod .= "<p><label for='Fujjelszo' class='label_1'>Új jelszó</label>\n"; 
  if ((strpos($ErrorStr,'ERR12')!== false) or (strpos($ErrorStr,'ERR13')!== false))
    {$HTMLkod .= "<input type='password' name='Fujjelszo' id='Fujjelszo' placeholder='Új jelszó' value='' class='Error' required >\n";} else
    {$HTMLkod .= "<input type='password' name='Fujjelszo' id='Fujjelszo' placeholder='Új jelszó' value='' required >\n";}
  $HTMLkod .= "<span>* Minimum 6 karakter</span></p>\n";
  $HTMLkod .= "<p><label for='Fujjelszo1' class='label_1'>Új jelszó újra</label>\n"; 
  if ((strpos($ErrorStr,'ERR12')!== false) or (strpos($ErrorStr,'ERR13')!== false)) 
    {$HTMLkod .= "<input type='password' name='Fujjelszo1' id='Fujjelszo1' placeholder='Új jelszó újra' value='' class='Error' required >\n";} else
    {$HTMLkod .= "<input type='password' name='Fujjelszo1' id='Fujjelszo1' placeholder='Új jelszó újra' value='' required >\n";} 
  $HTMLkod .= "<span>* Minimum 6 karakter</span></p>\n";

  $HTMLkod .= "</fieldset>";
  $HTMLkod .= "<input type='submit' name='submit' value='Módosítás' >\n";
  $HTMLkod .= "</form></div></div>\n";

  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// JELSZÓ MÓDOSÍTÁSA
//------------------------------------------------------------------------------------------------------------------
//ERR00=Nincs Fnev; ERR01=Hibás jelszó; ERR02=Rövíd személynév; ERR03=Rövíd Email cím; ERR04=Rövíd 1. telefonszám; ERR04=Rövíd 2. telefonszám
//ERR11=Foglalt Fnev; ERR12=Rövíd új jelszó; ERR13=Különbözó új jelszók; ERR14=Rövíd felhasználónév;
function JelszoModosit()
{
  global $MySqliLink, $hozzaferes; 
  
  if (($hozzaferes > 4) and ($hozzaferes != 6)) {
    // Csak a bejelentkezett felhasználók módosíthatnak a "demo" felhasználó kivételével
    $HTMLkod  = '';
    $ErrorStr = '';
    $r_ip     = getip(); 
    // Az űrlap adatainak megtisztítása
    $Fnev       = tiszta_szov($_POST['Fnev']); 
    $Fjelszo    = tiszta_szov($_POST['Fjelszo']);
    $Fujjelszo  = tiszta_szov($_POST['Fujjelszo']);
    $Fujjelszo1 = tiszta_szov($_POST['Fujjelszo1']);
    // Az aktuális felhasználó rekordjának beolvasása
    $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev'"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 23");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $JSZ1 = $row['Fjelszo'];
    $ID1  = $row['id'];
    // A jelszó titkosítása
    $Fjelszo = md5($Fjelszo);
    // A jelszó ellenőrzése
    if ($Fjelszo   != $JSZ1) {$ErrorStr = $ErrorStr.'ERR01';}
    // Az új jelszó ellenőrzése
    if ($Fujjelszo != $Fujjelszo1) {$ErrorStr = $ErrorStr.'ERR13';}
    if (6 > strlen($Fujjelszo))    {$ErrorStr = $ErrorStr.'ERR12';}

    if ($ErrorStr == '') { 
      // Az új jelszó titkosítása és tárolása
      $Fjelszo    = md5($Fujjelszo);
      $UpdateStr  = "UPDATE felhasznalo_reg SET Fjelszo = '$Fjelszo' WHERE Fnev='$Fnev'";
      if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba RG 24");}
      // A jelszómódosítás jellemzőinek tárolása a "felhasznalo_mod" táblába
      $InsertIntoStr = "INSERT INTO  felhasznalo_mod VALUES ('', ".$ID1.",'".$r_ip."','Jelszó módosítás',NOW())";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba RG 25");}

      // A felhasználó korábbi jelszómódosításai számának lekérdezése
      // Az utolsó 5 bejegyzés marad a többit törőljük
      $SelectStr = "SELECT * FROM felhasznalo_mod WHERE Fid=$ID1 AND Ftev='Jelszó módosítás'"; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba RG 26");
      $DbSzam    = mysqli_num_rows($result); mysqli_free_result($result);
      $DbSzamTorol  = $DbSzam - 5;
      if ($DbSzamTorol>0) {$DeleteStr = "Delete FROM felhasznalo_mod WHERE Ftev='Jelszó módosítás' ORDER BY Datum LIMIT $DbSzamTorol";
        if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba RG 27");}
      }
    }
    if ($ErrorStr == '') {$ErrorStr = 'OK';}
  }
  return $ErrorStr;
}



?>
