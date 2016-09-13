<?php

global $FejlecStr;

// Az oldaltíposok azonosítását szolgáló konstansok és tömb létrehozása
// Oldaltípusok (OTipus)
define("ONincs", -1, TRUE);
define("OKezdolap", 0, TRUE);
$OldalTipusok[0] = 'Kezdolap';

// Törölhető oldalak 1-től 49-ig.
define("OKategoria", 1, TRUE);
define("OAlkategoria", 2, TRUE);
define("OTermek", 3, TRUE);
$OldalTipusok[1]  = 'Kategoria';
$OldalTipusok[2]  = 'Alkategoria';
$OldalTipusok[3]  = 'Termek';

define("OHirkategoria", 10, TRUE);
define("OHirOldal", 11, TRUE);
$OldalTipusok[10] = 'Hirkategoria';
$OldalTipusok[11] = 'HirOldal';

// Nem törölhető oldalak 50-től felfelé
define("ORegisztal", 50, TRUE);
define("OJelszo", 51, TRUE);
define("OKosar", 52, TRUE);
define("OMegrendel", 53, TRUE);
define("ORendelesek", 54, TRUE);
define("OKapcsolat", 55, TRUE);
define("OOldalterkep", 56, TRUE);
define("OHiba", 57, TRUE);
$OldalTipusok[50]  = 'Regisztral';
$OldalTipusok[51]  = 'Jelszo';
$OldalTipusok[52]  = 'Kosar';
$OldalTipusok[53]  = 'Megrendel';
$OldalTipusok[54]  = 'Rendelesek';
$OldalTipusok[55]  = 'Kapcsolat';
$OldalTipusok[56]  = 'Oldalterkep';
$OldalTipusok[57]  = 'Hiba';

// Szerkesztő oldalak
define("OSzerkeszt", 101, TRUE);
define("OKepfeltolt", 102, TRUE);
define("OPDFfeltolt", 103, TRUE);
$OldalTipusok[101] = 'Szerkeszt';
$OldalTipusok[102] = 'Kepfeltolt';
$OldalTipusok[103] = 'PDFfeltolt';

define("FSzerkeszt", "szerk", TRUE);
define("FRegisztral", "reg", TRUE);

$FejlecStr  = '';
$NoIndexStr =  "<meta name='GOOGLEBOT' content='noarchive' />
<meta http-equiv='Cache-Control' content='no-cache' />
<META NAME='ROBOTS' CONTENT='NOINDEX'>\n ";
$NoIndexStr = '';


//------------------------------------------------------------------------------------------------------------------
// ---------- A Webáruház nevének lekérdezése
 $SelectStr = "SELECT ONev FROM oldal WHERE id=1"; 
 $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba F1 ");
 $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);
 $AruhazNev = $row['ONev'];
 mysqli_free_result($result);

//------------------------------------------------------------------------------------------------------------------
//------------Az AKTUÁLIS OLDAL adatainak lekérdezése
 if ($feladat>'') {$SelectStr = "SELECT * FROM oldal WHERE OURL='$feladat'"; }
  else {$SelectStr = "SELECT * FROM oldal WHERE id=1"; }
 $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba F2 ");
 $rowDB  = mysqli_num_rows($result);
 if ($rowDB > 0){
    // Ha az oldal létezik
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    // Az aktuális oldal adatainak $AktOldal globális tömbbe írása
    $AktOldal['id']           = $row['id']; 
    $AktOldal['ONev']         = $row['ONev'];  
    $AktOldal['OURL']         = $row['OURL']; 
    $AktOldal['OKep']         = $row['OKep']; 
    $AktOldal['ORLeiras']     = $row['ORLeiras']; 
    $AktOldal['OKulcszsavak'] = $row['OKulcszsavak']; 
    $AktOldal['OTipus']       = $row['OTipus']; 
    $AktOldal['OSzulo']       = $row['OSzulo']; 
    $AktOldal['OPrioritas']   = $row['OPrioritas']; 
    $AktOldal['ODatum']       = $row['ODatum'];     
  } else {
    // Nem létező oldal - Hibaoldal betöltése és adatainak $AktOldal globális tömbbe írása
    mysqli_free_result($result);
    $SelectStr = "SELECT * FROM oldal WHERE id=9"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba F3 ");
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $AktOldal['id']           = $row['id']; 
    $AktOldal['ONev']         = $row['ONev'];  
    $AktOldal['OURL']         = $row['OURL']; 
    $AktOldal['OKep']         = $row['OKep']; 
    $AktOldal['ORLeiras']     = $row['ORLeiras']; 
    $AktOldal['OKulcszsavak'] = $row['OKulcszsavak']; 
    $AktOldal['OTipus']       = $row['OTipus']; 
    $AktOldal['OSzulo']       = $row['OSzulo']; 
    $AktOldal['OPrioritas']   = $row['OPrioritas']; 
    $AktOldal['ODatum']       = $row['ODatum']; 
    $FejlecStr = $NoIndexStr;
    mysqli_free_result($result);
  }
//------------------------------------------------------------------------------------------------------------------
//------------Az ELSŐ SZÜLŐ oldal adatainak lekérdezése
 if ($AktOldal['OSzulo']>0) 
  {$SelectStr = "SELECT * FROM oldal WHERE id=".$AktOldal['OSzulo']; 
   $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba F4 ");
   $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    // Az első szülő adatainak $SZOldal globális tömbbe írása
    $SZOldal['id']           = $row['id']; 
    $SZOldal['ONev']         = $row['ONev'];  
    $SZOldal['OURL']         = $row['OURL']; 
    $SZOldal['OKep']         = $row['OKep']; 
    $SZOldal['ORLeiras']     = $row['ORLeiras']; 
    $SZOldal['OKulcszsavak'] = $row['OKulcszsavak']; 
    $SZOldal['OTipus']       = $row['OTipus']; 
    $SZOldal['OSzulo']       = $row['OSzulo']; 
    $SZOldal['OPrioritas']   = $row['OPrioritas']; 
    $SZOldal['ODatum']       = $row['ODatum'];    
  }

//  Második szülő GLOBÁLIS változója
$AktOldal['OSZSzulo'] = $SZOldal['OSzulo'];
//------------------------------------------------------------------------------------------------------------------
//------------A MÁSODIK SZÜLŐ oldal adatainak lekérdezése
 if ($SZOldal['OSzulo']>0) 
  {$SelectStr = "SELECT * FROM oldal WHERE id=".$SZOldal['OSzulo']; 
   $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba F5 ");
   $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
   // A második szülő adatainak $SZ2Oldal globális tömbbe írása 
   $SZ2Oldal['id']           = $row['id']; 
   $SZ2Oldal['ONev']         = $row['ONev'];  
   $SZ2Oldal['OURL']         = $row['OURL']; 
   $SZ2Oldal['OKep']         = $row['OKep']; 
   $SZ2Oldal['ORLeiras']     = $row['ORLeiras']; 
   $SZ2Oldal['OKulcszsavak'] = $row['OKulcszsavak']; 
   $SZ2Oldal['OTipus']       = $row['OTipus']; 
   $SZ2Oldal['OSzulo']       = $row['OSzulo']; 
   $SZ2Oldal['OPrioritas']   = $row['OPrioritas']; 
   $SZ2Oldal['ODatum']       = $row['ODatum']; 
 }

// Az oldal címének összeállítása
if ($AktOldal['id']>1) {$TitleStr = $AktOldal['ONev']." - ".$AruhazNev;} else {$TitleStr = $AktOldal['ONev'];}

// Az oldalfüggő fejléctartalom összeállítása
$FejlecStr .= "<title>$TitleStr</title>";
$FejlecStr .= "<meta name='description' content='".$AktOldal['ORLeiras']."'>";
$FejlecStr .= "<meta name='keywords' content='".$AktOldal['OKulcszsavak']."'>";

// Az útvonal linkjeinek összeállítása
$UtvonalStr = '';
if ($SZOldal['OSzulo']>0) {
  // Van második szülő
  if ($SZ2Oldal['OURL']>'') {
      $UtvonalStr .= "<a href='?f0=".$SZ2Oldal['OURL']."'><div style='float:left;margin: 0 5px 0 5px;font-weight:normal;'>
                     ".$SZ2Oldal['ONev']."</div><img src='kepek/ikonok/nyilbal28p.png' alt='kisnyil' style='float:left; '>  </a>";
  }
}
if ($AktOldal['OSzulo']>0) {
  // Van első szülő
  if ($SZOldal['OURL']>'') {
     $UtvonalStr .= "<a href='?f0=".$SZOldal['OURL']."'><div style='float:left;margin: 0 5px 0 5px;font-weight:normal;'>
                     ".$SZOldal['ONev']."</div><img src='kepek/ikonok/nyilbal28p.png' alt='kisnyil' style='float:left;'>  </a>";
  }
}
// Útvonal DIV összeállítása
if ($UtvonalStr>'') {$UtvonalStr = "<div class='utvonal' id='utvonal'> ".$UtvonalStr."</div>"; }

?>
