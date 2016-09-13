

<?php

/******************* FELHASZNÁLÓI TÁBLÁK LÁTREHOZÁSA HA MÉG NINCSENEK  *************************/
//------------------------------------------------------------------------------------------------------------------
// ----------- reg_felhasznalo TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_FelhasznaloRegTabla()
{
global $MySqliLink, $Err; 

  $DropTableStr = "DROP TABLE IF EXISTS felhasznalo_reg";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod = "A <b>'felhasznalo_reg'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1;  $HTMLkod = "MySqli hiba "; 
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS felhasznalo_reg (
   id int NOT NULL AUTO_INCREMENT,  
   Fnev  VARCHAR(20) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
   Fszemnev  VARCHAR(50) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
   Fjelszo VARCHAR(50) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '', 
   Femail VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',  
   Fszint TINYINT(3) NOT NULL DEFAULT '0',
   Fhiba TINYINT(2) NOT NULL DEFAULT '0',
   PRIMARY KEY (id),
   UNIQUE INDEX Fnev (Fnev)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_reg'</b> tábla elkészült.<br>";
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// ----------- felhasznalo_telefon TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
function Letrehoz_FelhasznaloTelefonTabla()
{
  global $MySqliLink, $Err;
  $DropTableStr = "DROP TABLE IF EXISTS felhasznalo_telefon";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_telefon'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS felhasznalo_telefon (
   id int NOT NULL AUTO_INCREMENT,  
   Fid int NOT NULL,
   Ftelszam  VARCHAR(20) NOT NULL DEFAULT '',
   PRIMARY KEY (id),
   INDEX Fid (Fid)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_telefon'</b> tábla elkészült.<br>";
  } else   { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// ----------- felhasznalo_cim TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
function Letrehoz_FelhasznaloCimTabla()
{
  global $MySqliLink, $Err;

  $DropTableStr = "DROP TABLE IF EXISTS felhasznalo_cim";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_cim'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1;  $HTMLkod .= "MySqli hiba ";
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS felhasznalo_cim (
   id int NOT NULL AUTO_INCREMENT,  
   Fid int NOT NULL,
   Forszag VARCHAR(30) NOT NULL DEFAULT '',
   Fvaros  VARCHAR(40) NOT NULL DEFAULT '',
   Firszam  VARCHAR(10) NOT NULL DEFAULT '',
   Fcim  VARCHAR(255) NOT NULL DEFAULT '',  
   PRIMARY KEY (id),
   INDEX Fid (Fid)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_cim'</b> tábla elkészült.<br>";
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
//----------- felhasznalo_mod TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
function Letrehoz_FelhasznaloModTabla()
{
  global $MySqliLink, $Err; 

  $DropTableStr = "DROP TABLE IF EXISTS felhasznalo_mod";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_mod'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS felhasznalo_mod (
   id int NOT NULL AUTO_INCREMENT,  
   Fid int NOT NULL,
   Fip  VARCHAR(20) NOT NULL DEFAULT '',
   Ftev VARCHAR(20) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',  
   Datum DATETIME DEFAULT NULL,
   PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'felhasznalo_mod'</b> tábla elkészült.<br>";
  } else { 
    $Err=1;  $HTMLkod .= "MySqli hiba ";
  }
  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
//----------- CAPTCHA kód TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
function Letrehoz_CaptchaTabla()
{
  global $MySqliLink, $Err; 

  $DropTableStr = "DROP TABLE IF EXISTS captcha_kodok";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod .= "A <b>'captcha_kodok'</b> tábla törlődött.<br>"; 
  } else { $Err=1;
    $HTMLkod .= "MySqli hiba (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS captcha_kodok (
   id int NOT NULL AUTO_INCREMENT,   
   CKerdes VARCHAR(40) NOT NULL DEFAULT '',
   CValasz VARCHAR(10) NOT NULL DEFAULT '',
   PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
  $HTMLkod .= "A <b>'captcha_kodok'</b> tábla elkészült.<br>";
  } else { $Err=1;
  $HTMLkod .= "MySqli hiba a <b>'captcha_kodok'</b> tábla létrehozásánál 
             (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);
  }
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
//----------- CAPTCHA kódtábla feltöltése  CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function LoadCsv_captcha_kodok($FileNev)
{
global $MySqliLink, $Err; 
$ErrorStr ='';
$i = 0;
$FSorok = array(); 
$HTMLkod = '';
$FejlecStr ='';

  // Fájl beolvasása
  $handle = @fopen($FileNev, "r");
  if ($handle) {
     while (($buffer = fgets($handle, 4096)) !== false) {
         $FSorok[$i++] = $buffer;  
     }
     if (!feof($handle)) {
        $ErrorStr .= "<b>A $FileNev fájlt nem lett teljesen betöltve!!!</b><br>\n";
     }
     fclose($handle);
  } else {$ErrorStr .= "<b>A $FileNev fájlt nem lehet megnyitni!!!</b><br>\n";}

  $i=0; $FejlecStr='';
  foreach ($FSorok as $FSor) {
    // Fejléc ellenőrzése
    if ($i==0) {
      $FejlecStrTmb = explode('|', $FSor); 
      foreach ($FejlecStrTmb as $v) { $v=trim($v); 
        if($v=='Kerdes') {$FejlecStr.=' Kerdes ';}
        if($v=='Valasz') {$FejlecStr.=' Valasz ';}
      } $i=1; 
      if (strpos($FejlecStr,'Valasz') == false) {$ErrorStr .= "A $FileNev fejléce hibás!";}
      if (strpos($FejlecStr,'Kerdes') == false) {$ErrorStr .= "A $FileNev fejléce hibás!";}
      if (strpos($FejlecStr,'Kerdes') > strpos($FejlecStr,'Valasz')) {$ErrorStr .= "A $FileNev fejléce hibás!";}
      $HTMLkod .= "<br>FejlecStr: $FejlecStr</br>";
    } else 
    // Adattábla feltöltése
    { 
      if ($ErrorStr=='') {
        $captchaStrTMB = explode('|', $FSor);
        $j=0; 
        foreach ($captchaStrTMB as $v) { $v=trim($v); 
          if ($j == 0) { $Kerdes = $v;} 
          if ($j == 1) { $Valasz = $v;} 
          $j++;
        }
        if ((strlen($Valasz) > 0) and (strlen($Valasz) > 0)) {
          $InsertIntoStr = "INSERT INTO captcha_kodok VALUES ('', '".$Kerdes."','".$Valasz."')"; 
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  
            {$ErrorStr = "MySqli hiba captcha kódok feltöltésénél(" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink); }  
        }
      }
    }
  }
  if ($ErrorStr=='') {$HTMLkod = "A captcha kódok feltöltve<br>";} else {$HTMLkod .= $ErrorStr; $Err=1;}
    return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// AZ OLDAL TÁBLA LÉTREHOZÁSA	
//------------------------------------------------------------------------------------------------------------------

  function Letrehoz_OldalTabla()
  {
    global $MySqliLink, $wsNev, $Err; 
    $HTMLkod = '';
    $DropTableStr = "DROP TABLE IF EXISTS oldal"; 
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .= "Az <b>'oldal'</b> tábla törlődött.<br>"; 
    } else {
      $Err=1;  $HTMLkod .= "MySqli hiba" ;
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS oldal (
     id int NOT NULL AUTO_INCREMENT,  
     ONev  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     OURL  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     OKep  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     ORLeiras  VARCHAR(255) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     OKulcszsavak VARCHAR(100) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '', 
     OTipus TINYINT(2) NOT NULL DEFAULT '0',
     OSzulo int(11) NOT NULL DEFAULT '0',
     OPrioritas TINYINT(2) NOT NULL DEFAULT '0',
     ODatum DATETIME DEFAULT NULL,
     PRIMARY KEY (id),
     UNIQUE INDEX ONev (ONev)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .= "Az <b>'oldal'</b> tábla elkészült.<br>";
    } else {
      $Err=1; $HTMLkod .= "MySqli hiba ";
    }

// Alapértelmezett oldalak feltöltése
   $InsertIntoStr = "INSERT INTO oldal VALUES";
   $InsertIntoStr .= "('','".$wsNev."','','','Egyedi webáruház készítésére alkalmas szabadon letölthető webáruház script.','webáruház, ingyen webáruház',0,0,1, NOW()), ";
   $InsertIntoStr .= "('','Oldaltérkép','oldalterkep','reg.jpg','A webáruház oldalainak listája','oldallista',56,1,1, NOW()),";
   $InsertIntoStr .= "('','Regisztáció','regisztracio','reg.jpg','Felhasználók regisztációja és regisztrációs adatok módosítása','vásárlói regisztráció',50,1,1, NOW()),";
   $InsertIntoStr .= "('','Jelszó módosítás','jelszo_modositas','jsz.jpg','Felhasználói jelszó módosítása','módosítás',51,1,1, NOW()),";
   $InsertIntoStr .= "('','Kosar','kosar','kosar.jpg','A kosár tartalma','kosár',52,1,1, NOW()),";
   $InsertIntoStr .= "('','Megrendelés','megrendel','rendel.jpg','Termékek megrendelése','megrendelés',53,1,1, NOW()),";
   $InsertIntoStr .= "('','Rendelések litája','rendelesek','rlista.jpg','Megrendelt termékek listája','Rendelések',54,1,1, NOW()),";
   $InsertIntoStr .= "('','Kapcsolat','kapcsolat','kapcs.jpg','Kapcsolat leírása','Kapcsolat',55,1,1, NOW()),";
   $InsertIntoStr .= "('','Hiba','hiba','kapcs.jpg','Nem létehő vagy hibás oldal.','',57,1,1, NOW()),";
   $InsertIntoStr .= "('','Szerkeszt','szerkeszt','szerkeszt.jpg','','',101,1,1, NOW());";
     if (!mysqli_query($MySqliLink,$InsertIntoStr))  {
        $HTMLkod .=  " MySqli hiba (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);   $Err=1; 
     }

  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
//  Oldal_tartalom TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_OldalTartalomTabla()
{
  global $MySqliLink, $Err; 

    $DropTableStr = "DROP TABLE IF EXISTS oldal_tartalom";
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .=  "A <b>'oldal_tartalom'</b> tábla törlődött.<br>"; 
    } else { 
      $Err=1;
      $HTMLkod .=  "MySqli hiba ";
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS oldal_tartalom (
     id int NOT NULL AUTO_INCREMENT,  
     Oid int(11) NOT NULL DEFAULT '0',
     OTartalom TEXT COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     PRIMARY KEY (id),
     UNIQUE INDEX Oid (Oid)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .=  "Az <b>'oldal_tartalom'</b> tábla elkészült.<br>";
    } else { 
      $Err=1;
      $HTMLkod .=  "MySqli hiba ";
    }

   $InsertIntoStr = "INSERT INTO oldal_tartalom VALUES";
   $InsertIntoStr .= "('',1,'Webáruház neve'), ";
   $InsertIntoStr .= "('',2,'Oldaltérkép '),";
   $InsertIntoStr .= "('',3,'Regisztáció'), ";
   $InsertIntoStr .= "('',4,'Jelszó módosítás'), ";
   $InsertIntoStr .= "('',5,'Kosar'), ";
   $InsertIntoStr .= "('',6,'Megrendelés'), ";
   $InsertIntoStr .= "('',7,'Rendelések litája'), ";
   $InsertIntoStr .= "('',8,'Kapcsolat'), ";
   $InsertIntoStr .= "('',9,'Ön a webhely nem létező oldalát próbálta megnyitni.'), ";

   $InsertIntoStr .= "('',10,'Szerkeszt');";
     if (!mysqli_query($MySqliLink,$InsertIntoStr))  {
        $HTMLkod .=  " MySqli hiba (" .mysqli_errno($MySqliLink). "): " . mysqli_error($MySqliLink);  $Err=1;  
     }
  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
//  TERMÉK TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
//  Egy termékoldal neve azonos a termék nevével, leírása pedig a termék leírásával
//  Egy termékoldalhoz a termék több alváltozata (L, XL, XXL...) tartozhat egyedi árral, kóddal... és egy egyedi tulajdonsággal  
//  Oid = Oldal azonosító; TAr = Termék ára...
function Letrehoz_TermekTabla()
{
  global $MySqliLink, $Err; 

    $DropTableStr = "DROP TABLE IF EXISTS termek";
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .=  "A <b>'termek'</b> tábla törlődött.<br>"; 
    } else { 
      $Err=1;  $HTMLkod .=  "MySqli hiba ";
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS termek (
     id int NOT NULL AUTO_INCREMENT,  
     Oid int(11) NOT NULL DEFAULT '0',
     TAr FLOAT(11,2) NOT NULL DEFAULT '0',
     TSzorzo FLOAT(3,2) NOT NULL DEFAULT '0',
     TKod  VARCHAR(30) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',

     TtulNev  VARCHAR(30) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     TtulErt  VARCHAR(30) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     TSzalKlts  VARCHAR(120) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     TSzallit TINYINT(2) NOT NULL DEFAULT '0',
     PRIMARY KEY (id),
     INDEX Oid (Oid)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .=  "Az <b>'termek'</b> tábla elkészült.<br>";
    } else { 
      $Err=1; $HTMLkod .=  "MySqli hiba ";
    }

  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
//  TERMÉK_jellemzők TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
//  Egy termékoldalhoz több jellemző is tartozhat, sőt az alváltozatoknak (L, XL, XXL...) egyedi jellemzőik is lehetnek
//  JTipus = 0 -> Általános jellemző
//  JTipus = 1 -> Egyedi jellemző
function Letrehoz_TermekjellemzoTabla()
{
  global $MySqliLink, $Err; 

    $DropTableStr = "DROP TABLE IF EXISTS termek_jellemzo";
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .=  "A <b>'termek_jellemzo'</b> tábla törlődött.<br>"; 
    } else {
      $Err=1; $HTMLkod .=  "MySqli hiba ";
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS termek_jellemzo (
     id int NOT NULL AUTO_INCREMENT,  
     Oid int(11) NOT NULL DEFAULT '0',
     JNev  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     JErtek  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     JSorszam TINYINT(2) NOT NULL DEFAULT '0',
     PRIMARY KEY (id),
     INDEX Oid (Oid)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .=  "Az <b>'termek_jellemzo'</b> tábla elkészült.<br>";
    } else {
      $Err=1; $HTMLkod .=  "MySqli hiba ";
    }
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
//  TERMÉK_leiras TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------
// Egy leírás egy termékoldalhoz köthető, az azon található valamennyi alváltozatra (L, XL, XXL...) igaz

function Letrehoz_TermekLeirasTabla()
{
  global $MySqliLink, $Err; 

    $DropTableStr = "DROP TABLE IF EXISTS termek_leiras";
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .=  "A <b>'termek_leiras'</b> tábla törlődött.<br>"; 
    } else { 
      $Err=1; $HTMLkod .=  "MySqli hiba ";
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS termek_leiras (
     id int NOT NULL AUTO_INCREMENT,  
     Oid int(11) NOT NULL DEFAULT '0',
     TLeiras  TEXT COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     PRIMARY KEY (id),
     UNIQUE INDEX Oid (Oid)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .=  "Az <b>'termek_leiras'</b> tábla elkészült.<br>";
    } else { $Err=1;
      $HTMLkod .=  "MySqli hiba ";
    }
  return $HTMLkod;
}
//------------------------------------------------------------------------------------------------------------------
// KÉP TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_KepTabla()
{
  global $MySqliLink, $Err; 
    $DropTableStr = "DROP TABLE IF EXISTS kep";
    if (mysqli_query($MySqliLink,$DropTableStr))
    {
      $HTMLkod .=  "A <b>'kep'</b> tábla törlődött.<br>"; 
    } else { $Err=1;
      $HTMLkod .=  "MySqli hiba ";
    }
    $CreateTableStr="CREATE TABLE IF NOT EXISTS kep (
     id int NOT NULL AUTO_INCREMENT,  
     Oid int(11) NOT NULL DEFAULT '0',
     KNev  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     KURL  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     KLeiras  VARCHAR(200) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     KSorszam TINYINT(2) NOT NULL DEFAULT '0',
     PRIMARY KEY (id),
     INDEX Oid (Oid)
    )";
    if (mysqli_query($MySqliLink,$CreateTableStr))
    {
      $HTMLkod .=  "A <b>'kep'</b> tábla elkészült.<br>";
    } else { $Err=1;
      $HTMLkod .=  "MySqli hiba ";
    }

  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// KOCSI TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_KocsiTabla()
{
  global $MySqliLink, $Err; 

  $DropTableStr = "DROP TABLE IF EXISTS kocsi"; 
  if (mysqli_query($MySqliLink,$DropTableStr)) {$HTMLkod .= "A <b>'kocsi'</b> tábla törlődött.<br>"; 
    } else {$Err=1; $HTMLkod .= "MySqli hiba ";}

  $CreateTableStr="CREATE TABLE IF NOT EXISTS kocsi (
     id int NOT NULL AUTO_INCREMENT,  
     mmAzon  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     TKod  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     DB int(6) NOT NULL DEFAULT '0',
     ODatum DATETIME DEFAULT NULL,
     PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr)) {$HTMLkod .= "A <b>'kocsi'</b> tábla elkészült.<br>";
    } else {$Err=1; $HTMLkod .= "MySqli hiba ";}

  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// MEGRENDELÉS TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_MegrendelesTabla()
{
  global $MySqliLink, $Err ; 
  $DropTableStr = "DROP TABLE IF EXISTS megrendeles"; 
  if (mysqli_query($MySqliLink,$DropTableStr)) {
    $HTMLkod .= "A <b>'megrendeles'</b> tábla törlődött.<br>"; 
  } else {
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS megrendeles (
     id int NOT NULL AUTO_INCREMENT,  
     mmAzon  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     Fnev  VARCHAR(30) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     Rszemnev  VARCHAR(50) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     Remail VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',  
     Rtelszam1  VARCHAR(20) NOT NULL DEFAULT '',
     Rtelszam2  VARCHAR(20) NOT NULL DEFAULT '',
     Rorszag VARCHAR(30) NOT NULL DEFAULT '',
     Rvaros  VARCHAR(40) NOT NULL DEFAULT '',
     Rirszam  VARCHAR(10) NOT NULL DEFAULT '',
     Rcim  VARCHAR(255) NOT NULL DEFAULT '',  
     SZorszag VARCHAR(30) NOT NULL DEFAULT '',
     SZvaros  VARCHAR(40) NOT NULL DEFAULT '',
     SZirszam  VARCHAR(10) NOT NULL DEFAULT '',
     SZcim  VARCHAR(255) NOT NULL DEFAULT '',  
     RStatus TINYINT(2) NOT NULL DEFAULT '0',
     Rip  VARCHAR(20) NOT NULL DEFAULT '',
     RDatum DATETIME DEFAULT NULL,
     PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr)) {
    $HTMLkod .= "A <b>'megrendeles'</b> tábla elkészült.<br>";
  } else {
     $Err=1; $HTMLkod .= "MySqli hiba ";
  }

  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// MEGRENDELT TERMÉK TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_MegrendeltTermekTabla()
{
  global $MySqliLink, $Err; 
  $DropTableStr = "DROP TABLE IF EXISTS megrendelt_termek"; 
  if (mysqli_query($MySqliLink,$DropTableStr)) {
    $HTMLkod .= "A <b>'megrendelt_termek'</b> tábla törlődött.<br>"; 
  } else {
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS megrendelt_termek (
     id int NOT NULL AUTO_INCREMENT,  
     RAzon   int NOT NULL,
     RTNev  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     RTKod  VARCHAR(40) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     RDB int(6) NOT NULL DEFAULT '0',

     RTAr FLOAT(11,2) NOT NULL DEFAULT '0',
     RTSzorzo FLOAT(3,2) NOT NULL DEFAULT '0',
     RTSzalKlts  VARCHAR(120) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
     RTSzallit TINYINT(2) NOT NULL DEFAULT '0',

     PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr)) {
    $HTMLkod .= "A <b>'megrendelt_termek'</b> tábla elkészült.<br>";
  } else {
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// ----------- reg_felhasznalo TÁBLA LÉTREHOZÁSA
//------------------------------------------------------------------------------------------------------------------

function Letrehoz_LatogatoTablak()
{
global $MySqliLink, $Err; 

  $DropTableStr = "DROP TABLE IF EXISTS latogato_szamlalo";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod = "A <b>'latogato_szamlalo'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1;  $HTMLkod = "MySqli hiba "; 
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS latogato_szamlalo (
   id int NOT NULL AUTO_INCREMENT,  
   latogatasok int NOT NULL,
   PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'latogato_szamlalo'</b> tábla elkészült.<br>";
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }

  $DropTableStr = "DROP TABLE IF EXISTS online";
  if (mysqli_query($MySqliLink,$DropTableStr))
  {
    $HTMLkod = "A <b>'online'</b> tábla törlődött.<br>"; 
  } else { 
    $Err=1;  $HTMLkod = "MySqli hiba "; 
  }
  $CreateTableStr="CREATE TABLE IF NOT EXISTS online (
   id int NOT NULL AUTO_INCREMENT,  
   ip  VARCHAR(20) NOT NULL DEFAULT '',
   datum DATETIME DEFAULT NULL,
   PRIMARY KEY (id)
  )";
  if (mysqli_query($MySqliLink,$CreateTableStr))
  {
    $HTMLkod .= "A <b>'online'</b> tábla elkészült.<br>";
  } else { 
    $Err=1; $HTMLkod .= "MySqli hiba ";
  }

   $InsertIntoStr = "INSERT INTO latogato_szamlalo VALUES ('',0);";
     if (!mysqli_query($MySqliLink,$InsertIntoStr))  {
        $HTMLkod .=  " MySqli hiba ";   $Err=1; 
     }

  return $HTMLkod;
}


?>
