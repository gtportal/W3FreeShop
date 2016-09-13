<?php
    

function Oldal_Modosit($Oid,$funkcio)
{
  global $hozzaferes;
  global $MySqliLink, $f1, $f2, $f3, $f4, $f5; 
  global $AktOldal, $VisszaHidden, $OldalTipusok; 

  $AktUrlap = 'Alap';
  if ($hozzaferes<7) {$tiltottSubmit =" disabled ";} else {$tiltottSubmit ="";}

//------------------------------------------------------------------------------------------------------------------
// ÚJ OLDAL LÉTREHOZÁSA 
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitAltalanos1 gomb értéke Létrehozás, és a jogosúltság is megfelelő

if (($_POST['submitAltalanos1'] == 'Létrehozás') and ($hozzaferes>6)) {
  $UjOErr = '';
  // Az oldal nevének beolvasása az űrlapadatokból
  $UjONev = tiszta_szov($_POST['ONev']);

  // Egy néven csak egy oldal lehet
  $SelectStr = "SELECT id FROM oldal WHERE ONev='$UjONev' LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 01 ");
  $rowDB     = mysqli_num_rows($result); mysqli_free_result($result);
  if ($rowDB > 0) {
     //Ha már létezik adott néven oldal, akkor nem hozzuk létre ismét     
     $UjOErr = "Már létezik oldal $UjONev néven!"; $funkcio='UjOldal';
  } else {
     // OURL megtisztítása
     $tiszta_OURL = strtolower(trim($UjONev));
     $tiszta_OURL = URLTisztit($tiszta_OURL);
     //Ha már létezik az OURL, akkor nem hozzuk létre ismét
     $SelectStr   = "SELECT id FROM oldal WHERE OURL='$tiszta_OURL' LIMIT 1"; 
     $result      = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 02 ");
     $rowDB       = mysqli_num_rows($result); mysqli_free_result($result);
     if ($rowDB > 0) {
       //Már létezik az URL visszatérünk az oldal létrehozásához
       $UjOErr = "Már létezik oldal $tiszta_OURL néven! "; $funkcio='UjOldal';
     } else {
       $OTipus=1;
       //OldalTipusok tömbből kikeressük az oldaltípúshoz tartozó kódot
       if ($_POST['TipValszt'] > '') {
           $ValTipus = tiszta_szov($_POST['TipValszt']);
           $OTipus   = array_search($ValTipus, $OldalTipusok);
       }
       // Az oldal adatainak beolvasása az űrlapadatokból
       $ORLeiras     = ''; if ($_POST['ORLeiras'] > '')     {$ORLeiras     = tiszta_szov($_POST['ORLeiras']);}
       $OKulcszsavak = ''; if ($_POST['OKulcszsavak'] > '') {$OKulcszsavak = tiszta_szov($_POST['OKulcszsavak']);}
       $OPrioritas   = ''; if ($_POST['OPrioritas'] > -1)   {$OPrioritas   = tiszta_szov($_POST['OPrioritas']);}
       // Az új oldal létrehozása
       $InsertIntoStr = "INSERT INTO oldal VALUES ('', '".$UjONev."','".$tiszta_OURL."','','".$ORLeiras."','".$OKulcszsavak
                        ."',".$OTipus.",1,".$OPrioritas.", NOW())";
       if (!mysqli_query($MySqliLink,$InsertIntoStr)) {die("Hiba OM 03 ");} 
         else { $UjID= mysqli_insert_id($MySqliLink);}
       if ($UjID>0) {$Oid = $f2 = $UjID;} else {$UjOErr = "A(z) $UjONev oldalt nem sikerűlt létrehozni.";}
     }
  }
  if ($UjOErr == '') {$funkcio = 'Modosit';}
}

//------------------------------------------------------------------------------------------------------------------
// MÁSOLÁS  
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitAltalanos gomb értéke Másolás, és a jogosúltság is megfelelő
if (($_POST['submitAltalanos'] == 'Másolás') and ($hozzaferes>6)) {
  $RegiOID  = $f2;
  // Az oldal nevének beolvasása az űrlapadatokból
  $UjONev   = tiszta_szov($_POST['ONev']);
  $MasolErr = '';
  // Egy néven csak egy oldal lehet
  $SelectStr = "SELECT id FROM oldal WHERE ONev='$UjONev' LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 04 ");
  $rowDB     = mysqli_num_rows($result); mysqli_free_result($result);
  if ($rowDB > 0) {
     //Ha már létezik az oldal, akkor nem másolunk     
     $MasolErr = "Már létezik oldal $UjONev néven!";
  } else {
     // OURL megtisztítása
     $tiszta_OURL = strtolower(trim($UjONev));
     $tiszta_OURL = URLTisztit($tiszta_OURL);
     //Ha már létezik az OURL, akkor nem másolunk
     $SelectStr = "SELECT id FROM oldal WHERE OURL='$tiszta_OURL' LIMIT 1"; 
     $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 05 ");
     $rowDB     = mysqli_num_rows($result); mysqli_free_result($result);
     if ($rowDB > 0) {
       //Már létezik
       $MasolErr = "Már létezik oldal $tiszta_OURL néven! ";
     } else {
       //A forrásoldal adatainak beolvasása
       $SelectStr = "SELECT * FROM oldal WHERE id=$f2 LIMIT 1"; 
       $result    = mysqli_query($MySqliLink,$SelectStr); 
       $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
       $ONev   = $row['ONev'];
       $OKep   = $row['OKep'];
       $OTipus = $row['OTipus'];
       $OSzulo = $row['OSzulo']; 
       //Szülőoldal kiválasztása
       if ($_POST['SzuloValaszt'] > '') {   
         $SzNev     = $_POST['SzuloValaszt'];
         $SelectStr = "SELECT id FROM oldal WHERE ONev='$SzNev' LIMIT 1"; 
         $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 06 ");
         $rowDB     = mysqli_num_rows($result);
         if ($rowDB > 0) {
           // Ha a szülőoldal létezik, akkor annak azonosítóját tároljuk
           $row    = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
           $OSzulo = $row['id'];          
         } else {
           // Ha a szülőoldal nem létezik, akkor a kezdőlap lesz a szülő
           $OSzulo = 1; 
           mysqli_free_result($result);
         }
       }
       // Bekérjük a változtatható jellemzőket
       $ORLeiras     = $row['ORLeiras']; if ($_POST['ORLeiras'] > '')  {$ORLeiras = tiszta_szov($_POST['ORLeiras']);}
       $OKulcszsavak = $row['OKulcszsavak']; if ($_POST['OKulcszsavak'] > '') {$OKulcszsavak = tiszta_szov($_POST['OKulcszsavak']);}
       $OPrioritas   = $row['OPrioritas']; if ($_POST['OPrioritas'] > -1)  {$OPrioritas = tiszta_szov($_POST['OPrioritas']);}
       // Létrehozzuk az oldalt, és lekérdezzük az egyedi azonosítóját
       $InsertIntoStr = "INSERT INTO oldal VALUES ('', '".$UjONev."','".$tiszta_OURL."','"
         .$OKep."','".$ORLeiras."','".$OKulcszsavak."',".$OTipus.",".$OSzulo
         .",".$OPrioritas.", NOW())";
       if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 07 ");}  
         else { $UjID= mysqli_insert_id($MySqliLink);} 
       // Létrehozzuk az oldalhoz tartozó rekordokat a kapcsolódó táblákban is
      $InsertIntoStr = "INSERT INTO oldal_tartalom ( id, Oid, OTartalom)
           (SELECT '', $UjID, OTartalom FROM  oldal_tartalom WHERE Oid = $RegiOID)";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 08 ");} 

      $InsertIntoStr = "INSERT INTO kep ( id, Oid, KNev, KURL, KLeiras, KSorszam)
           (SELECT '', $UjID, KNev, KURL, KLeiras, KSorszam FROM  kep WHERE Oid = $RegiOID)";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 09 ");} 

      // Ha termékoldalról van szó, akkor a termékek tábláiban is létrehozzuk az oldalhoz kapcsolódó rekordokat 
      if ($OTipus== OTermek) {
        $InsertIntoStr = "INSERT INTO termek_jellemzo ( id, Oid, JNev, JErtek, JSorszam)
           (SELECT '', $UjID, JNev, JErtek, JSorszam FROM  termek_jellemzo WHERE Oid = $RegiOID)";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 10 ");}

        $InsertIntoStr = "INSERT INTO termek ( id, Oid, TAr, TSzorzo, TKod, TtulNev, TtulErt, TSzalKlts, TSzallit)
           (SELECT '', $UjID, TAr, TSzorzo, TKod, TtulNev, TtulErt, TSzalKlts, TSzallit FROM  termek WHERE Oid = $RegiOID)";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 11 ");}

        $InsertIntoStr = "INSERT INTO termek_leiras ( id, Oid, TLeiras)
           (SELECT '', $UjID, TLeiras FROM  termek_leiras WHERE Oid = $RegiOID)";
        if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 12 ");}
      }

       $Oid = $f2 = $UjID;
     }
  }
  // Ha a másolás hiba nélkül megtörtént, akkor az adatok módosítása következik
  // Hiba esetén hibajelzés és visszatérés a másolás fonkcióhoz
  if ($MasolErr == '') {$funkcio = 'Modosit';} else {$funkcio = 'Masol'; echo "<h1>  Másol üzi: ". $MasolErr ." F2:$f2 </h1>";}
}
//------------------------------------------------------------------------------------------------------------------
// KÉP FELTÖLTÉSE
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha valamelyik Feltöltés gomb aktív, és a jogosúltság is megfelelő
if ((($_POST['submit_Kiskep'] == 'Feltöltés') 
|| ($_POST['submit_Kep1']    == 'Feltöltés') 
|| ($_POST['submit_Kep2']    == 'Feltöltés') 
|| ($_POST['submit_Kep3']    == 'Feltöltés') 
|| ($_POST['submit_Kep4']    == 'Feltöltés') 
|| ($_POST['submit_Kep5']    == 'Feltöltés'))
&& ($hozzaferes>6)) 
{
$KepOK       = false;
//Csak képek feltöltését engedélyezzük
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp        = explode(".", $_FILES["file"]["name"]);
$extension   = end($temp);
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"]   == "image/jpeg")
|| ($_FILES["file"]["type"]   == "image/jpg")
|| ($_FILES["file"]["type"]   == "image/pjpeg")
|| ($_FILES["file"]["type"]   == "image/x-png")
|| ($_FILES["file"]["type"]   == "image/png"))
&& ($_FILES["file"]["size"]   < 2000000)
&& in_array($extension, $allowedExts))
  {
    if ($_FILES["file"]["error"] > 0) {
      $UploadErr = "Hibakód: " . $_FILES["file"]["error"] . "<br>"; 
    } else {
      if (file_exists("kepek/" . $_FILES["file"]["name"])) {
        //Meglévő kép felülírása
        move_uploaded_file($_FILES["file"]["tmp_name"],"kepek/" . $_FILES["file"]["name"]);
        $UploadErr =  "Felülírva: " .$_FILES["file"]["name"]; $KepOK=true;
      } else {
        //Új kép feltöltése
        move_uploaded_file($_FILES["file"]["tmp_name"],"kepek/" . $_FILES["file"]["name"]);
        $UploadErr =  "Feltöltve: ". $_FILES["file"]["name"]; $KepOK=true;
      }
    }
  } else {
    if ($_FILES["file"]["name"] >'') {$UploadErr = "Érvénytelen file.";}
  }
}

//------------------------------------------------------------------------------------------------------------------
// KISKÉP NEVÉNEK TÁROLÁSA AZ oldal TÁBLÁBAN
//------------------------------------------------------------------------------------------------------------------
// Ha a képek feltöltése rendben lezajlott és a hozzáférés is megfelelő, akkor a 
// kiskép neve bekerül az adatbázisba 
if (($_POST['submit_Kiskep'] == 'Feltöltés') && $KepOK && ($hozzaferes>6)) {
  $OKep =$_FILES["file"]["name"];
  $UpdateStr = "UPDATE oldal SET OKep='$OKep' WHERE id=$f2";
  if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 13 ");}
}
// Ha a submit_KiskepTorol gomb elküldött értéke 'Törlés' és a hozzáférés is megfelelő, akkor a 
// kisképet törőljük
if (($_POST['submit_KiskepTorol'] == 'Törlés') && ($hozzaferes>6)) {
  $UpdateStr = "UPDATE oldal SET OKep='' WHERE id=$f2";
  if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 14 ");}
}

//------------------------------------------------------------------------------------------------------------------
// AZ oldal TÁBLA TÁROLÁSA -- Módosítás
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitAltalanos gomb értéke Módosítás, és a jogosúltság is megfelelő
if (($_POST['submitAltalanos'] == 'Módosítás') && ($hozzaferes>6)) {
    // Az adatbázisból beolvassuk az oldal adatait
    $SelectStr = "SELECT * FROM oldal WHERE id=$Oid LIMIT 1"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 15 ");
    $rowDB     = mysqli_num_rows($result);
    if ($rowDB > 0) {
      //Ha már létezik az Oid oldal, akkor frissítjük
      $row    = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $ONev   = $row['ONev'];
      $OURL   = $row['OURL'];
      $OKep   = $row['OKep']; 
      $OSzulo = $row['OSzulo'];
      //Az űrlapból érkezőkkel felülírjuk az eredeti adatokat
      $ORLeiras     = $row['ORLeiras'];     if ($_POST['ORLeiras'] > '')     {$ORLeiras     = tiszta_szov($_POST['ORLeiras']);}
      $OKulcszsavak = $row['OKulcszsavak']; if ($_POST['OKulcszsavak'] > '') {$OKulcszsavak = tiszta_szov($_POST['OKulcszsavak']);}
      $OPrioritas   = $row['OPrioritas'];   if ($_POST['OPrioritas'] > -1)   {$OPrioritas   = tiszta_szov($_POST['OPrioritas']);}
      //Az $OldalTipusok tömbből kikeressük az oldaltípus kódját
      $OTipus = $row['OTipus']; 
      if ($_POST['TipValszt'] > '') {
           $ValTipus = tiszta_szov($_POST['TipValszt']);
           $OTipus   = array_search($ValTipus, $OldalTipusok);
      }
      // A kezdőoldal neve is módosítható, a többié nem
      if ($OTipus==0) {$ONev = tiszta_szov($_POST['ONev']);}
      // Lekérdezzük a szülőoldal egyedi azonosítóját 
      if ($_POST['SzuloValaszt'] > '') {   
        $SzNev     = $_POST['SzuloValaszt'];
        $SelectStr = "SELECT id FROM oldal WHERE ONev='$SzNev' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 16 ");
        $rowDB     = mysqli_num_rows($result);
        if ($rowDB > 0) {
          // Lekérdezzük a szülőoldal egyedi azonosítóját
          $row    = mysqli_fetch_array($result, MYSQLI_ASSOC); 
          $OSzulo = $row['id'];          
        } else {
          // Ha a szülőoldal nem létezik, akkor a kezdőlap lesz a szülő
          $OSzulo = 1;
        }
        mysqli_free_result($result);       
      }
      // Frissítjük az oldal tábla adatait
      $UpdateStr = "UPDATE oldal SET ONev='$ONev', OURL='$OURL', OKep='$OKep', ORLeiras='$ORLeiras', OKulcszsavak='$OKulcszsavak', 
            OPrioritas='$OPrioritas', OTipus=$OTipus, OSzulo=$OSzulo,  ODatum=NOW()   WHERE id=$Oid";
      if (!mysqli_query($MySqliLink,$UpdateStr)) {die("Hiba OM 17 ");} 
    } else {
      //Ha nem létezik az Oid oldal, akkor baj van
      mysqli_free_result($result);
    }
}

//------------------------------------------------------------------------------------------------------------------
// A TARTALOM TÁBLA TÁROLÁSA -- Módosítás
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitTartalom gomb értéke Módosítás, és a jogosúltság is megfelelő
if (($_POST['submitTartalom'] == 'Módosítás') && ($hozzaferes>6))  {
  $AktUrlap = 'Tartalom';
  // A tartalom beolvasása űrlap adataiból
  $Tartalom = tiszta_szov($_POST['OTartalom']);

  //Ha az oldalhoz tartozik az oldal_tartalom táblában rekord, akkor azt módosítjuk
  $SelectStr = "SELECT id FROM oldal_tartalom WHERE Oid = $f2"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 18 ");
  $rowDB     = mysqli_num_rows($result);
  if ($rowDB>0) {
     // A rekord már létezik
     $row  = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
     $tTid = $row['id'];
     //Frissítjük a tartalom tábla tartalmát
     $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='$Tartalom' WHERE id=$tTid";
     if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 19 ");}
  } else {
     mysqli_free_result($result);
     // Ha a rekord még nem létezik, akkor létrehozzuk
     $InsertIntoStr = "INSERT INTO oldal_tartalom VALUES ('', $f2,'$Tartalom')";
     if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 20 ");} 
  }
}


//------------------------------------------------------------------------------------------------------------------
// A TERMÉK LEÍRÁS TÁBLA TÁROLÁSA -- Módosítás
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitTermekleiras gomb értéke Módosítás, és a jogosúltság is megfelelő
if (($_POST['submitTermekleiras'] == 'Módosítás') && ($hozzaferes>6)) {
  $AktUrlap = 'TermekLeir';
  // A termékleírás beolvasása űrlap adataiból
  $Tartalom = tiszta_szov($_POST['Termekleiras']);

  $SelectStr = "SELECT id FROM termek_leiras WHERE Oid = $f2"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 21 ");
  $rowDB     = mysqli_num_rows($result);
  if ($rowDB>0) {
     // Ha a rekord már létezik, akkor frissítjük
     $row  = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
     $tLid = $row['id'];

     $UpdateStr = "UPDATE termek_leiras SET TLeiras='$Tartalom' WHERE id=$tLid";
     if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 22 ");}
  } else {
     mysqli_free_result($result);
    // Ha a rekord még nem létezik, akkor létrehozzuk
    $InsertIntoStr = "INSERT INTO termek_leiras VALUES ('', $f2,'$Tartalom')";
    if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 23 ");} 
  }
}


//------------------------------------------------------------------------------------------------------------------
// A TERMÉK TÁBLA TÁROLÁSA -- Módosítás
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitTermek gomb értéke Módosítás, és a jogosúltság is megfelelő
if (($_POST['submitTermek'] == 'Módosítás') && ($hozzaferes>6))  {
  $AktUrlap = 'Termek';
  // A TermekTmb feltőltése a kezdeti értékekkel
  for ($i=1;$i<=10;$i++) {
     $TermekTmb[$i]['TAr'] = 0; $TermekTmb[$i]['TSzorzo'] = 1; $TermekTmb[$i]['TKod'] = '';
     $TtulNev = ''; $TermekTmb[$i]['TtulErt'] = ''; $TermekTmb[$i]['TSzalKlts'] = '';
     $TermekTmb[$i]['TSzallit'] = 0;
  }
  // Az űrlap adatainak betöltése a TermekTmb-be
  for ($i=1;$i<=10;$i++) {
    if ($_POST['TAr'.$i]>'') {$TermekTmb[$i]['TAr'] = tiszta_szov($_POST['TAr'.$i]);}
    if ($_POST['TSzorzo'.$i]>'') {$TermekTmb[$i]['TSzorzo'] = tiszta_szov($_POST['TSzorzo'.$i]);}
    if ($_POST['TKod'.$i]>'') {$TermekTmb[$i]['TKod'] = tiszta_szov($_POST['TKod'.$i]);}
    if ($_POST['TtulNev']>'') {$TtulNev = tiszta_szov($_POST['TtulNev']);}
    if ($_POST['TtulErt'.$i]>'') {$TermekTmb[$i]['TtulErt'] = tiszta_szov($_POST['TtulErt'.$i]);}
    if ($_POST['TSzalKlts'.$i]>'') {$TermekTmb[$i]['TSzalKlts'] = tiszta_szov($_POST['TSzalKlts'.$i]);}
    if ($_POST['TSzallit'.$i]>'') {$TermekTmb[$i]['TSzallit'] = tiszta_szov($_POST['TSzallit'.$i]);}
  }
  // A TermekTmb tartalmának tárolása az adatbázisban
  for ($i=1;$i<=10;$i++) {
     // Ellenőrizzük, hogy adott termékkód szerel-e az adatbázisban
     $SelectStr = "SELECT id FROM termek WHERE Oid = $f2 and TKod='".$TermekTmb[$i]['TKod']."'"; 
     $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 24 ");
     $rowDB     = mysqli_num_rows($result);
     if ($rowDB>0) {
       // Ha a termékkód már létezik, akkor frissítjük az adatokat
       $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
       $Tid = $row['id'];
       $UpdateStr = "UPDATE termek SET 
          TAr=".$TermekTmb[$i]['TAr'].",
          TSzorzo=".$TermekTmb[$i]['TSzorzo'].",
          TKod='".$TermekTmb[$i]['TKod']."',
          TtulNev='".$TtulNev."',
          TtulErt='".$TermekTmb[$i]['TtulErt']."',
          TSzalKlts='".$TermekTmb[$i]['TSzalKlts']."',
          TSzallit=".$TermekTmb[$i]['TSzallit']."
       WHERE id=$Tid";
       if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 25 ");}
     } else {
       // Ha a termékkód még nem létezik, akkor létrehozzuk
       if (($TermekTmb[$i]['TKod']>'') and ($TermekTmb[$i]['TtulErt']>'')) {
         $InsertIntoStr = "INSERT INTO termek VALUES ('', $f2,".$TermekTmb[$i]['TAr'].",".$TermekTmb[$i]['TSzorzo'].",
         '".$TermekTmb[$i]['TKod']."','".$TtulNev."','".$TermekTmb[$i]['TtulErt']."','".$TermekTmb[$i]['TSzalKlts']."',
          ".$TermekTmb[$i]['TSzallit'].")";
         if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 26 ");} 
       }
     }
  }
  // Ha egy terméknél nincs megadva kiemelt tulajdonság, azt törőljük 
  $DeletetStr = "Delete FROM termek  WHERE TtulErt='' ";
  if (!mysqli_query($MySqliLink,$DeletetStr)) {die("Hiba OM 27 ");}
}

//------------------------------------------------------------------------------------------------------------------
// A JELLEMZŐL TÁBLA TÁROLÁSA -- Módosítás
//------------------------------------------------------------------------------------------------------------------
//Csak akkor kezdünk bele, ha a submitJellemzok gomb értéke Módosítás, és a jogosúltság is megfelelő
if (($_POST['submitJellemzok'] == 'Módosítás') && ($hozzaferes>6))  {
  $AktUrlap = 'Jellemzo';
  // A JellemzoTmb feltőltése a kezdeti értékekkel
  for ($i=1;$i<=10;$i++) {$JellemzoTmb[$i]['JNev'] = ''; $JellemzoTmb[$i]['JErtek'] = ''; $JellemzoTmb[$i]['JSorszam'] = 0;}
  // Az JellemzoTmb adatainak betöltése a TermekTmb-be
  for ($i=1;$i<=10;$i++) {
    if ($_POST['JNev'.$i]>'')    {$JellemzoTmb[$i]['JNev'] = tiszta_szov($_POST['JNev'.$i]);}
    if ($_POST['JErtek'.$i]>'')  {$JellemzoTmb[$i]['JErtek'] = tiszta_szov($_POST['JErtek'.$i]);}
    if ($_POST['JSorszam'.$i]>0) {$JellemzoTmb[$i]['JSorszam'] = tiszta_int($_POST['JSorszam'.$i]);}
  }
  // A JellemzoTmb tartalmának tárolása az adatbázisban
  for ($i=1;$i<=10;$i++) {
     // Ellenőrizzük, hogy a termék adott sorszámú jellemzője szerel-e az adatbázisban
     $SelectStr = "SELECT id FROM termek_jellemzo WHERE Oid = $f2 and JSorszam=".$JellemzoTmb[$i]['JSorszam']; 
     $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 28 ");
     $rowDB     = mysqli_num_rows($result);
     if ($rowDB>0) {
       // Ha a termék adott sorszámú jellemzője már létezik, akkor frissítjük
       $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
       $Jid = $row['id'];

       $UpdateStr = "UPDATE termek_jellemzo SET 
          JSorszam=".$JellemzoTmb[$i]['JSorszam'].",
          JNev='".$JellemzoTmb[$i]['JNev']."',
          JErtek='".$JellemzoTmb[$i]['JErtek']."'
       WHERE id=$Jid";
       if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 29 ");}
     } else {
       mysqli_free_result($result);
       // Ha a termék adott sorszámú jellemzője még nem létezik, akkor létrehozzuk
       if (($JellemzoTmb[$i]['JSorszam']>0) and ($JellemzoTmb[$i]['JNev']>'')) {
         $InsertIntoStr = "INSERT INTO termek_jellemzo VALUES ('', $f2,'".$JellemzoTmb[$i]['JNev']."',
         '".$JellemzoTmb[$i]['JErtek']."',".$JellemzoTmb[$i]['JSorszam'].")";
         if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 30 ");} 
       }
     }
  }
  // Az üres jellemzők törlése
  $DeletetStr = "Delete FROM termek_jellemzo  WHERE JErtek='' ";
  if (!mysqli_query($MySqliLink,$DeletetStr)) {die("Hiba OM 31 ");}
}


//------------------------------------------------------------------------------------------------------------------
// KÉPEK TÖRLÉSE A kep TÁBLÁBAN
//------------------------------------------------------------------------------------------------------------------

//Csak akkor kezdünk bele, ha valamelyik törlő gomb aktív
if ((($_POST['submit_Torol_Kep1'] == 'Törlés') 
|| ($_POST['submit_Torol_Kep2']  == 'Törlés') 
|| ($_POST['submit_Torol_Kep3']  == 'Törlés') 
|| ($_POST['submit_Torol_Kep4']  == 'Törlés') 
|| ($_POST['submit_Torol_Kep5']  == 'Törlés')) 
&& ($hozzaferes>6)) 
{
  $AktUrlap = 'Kepek';
  // Kiválasztjuk a törlendő kép sorszámát
  if ($_POST['submit_Torol_Kep1'] == 'Törlés') {$KSorszam = 1;}
  if ($_POST['submit_Torol_Kep2'] == 'Törlés') {$KSorszam = 2;}
  if ($_POST['submit_Torol_Kep3'] == 'Törlés') {$KSorszam = 3;}
  if ($_POST['submit_Torol_Kep4'] == 'Törlés') {$KSorszam = 4;}
  if ($_POST['submit_Torol_Kep5'] == 'Törlés') {$KSorszam = 5;}
    // Ellenőrizzük, hogy létezik-e valójában
    $SelectStr = "SELECT * FROM kep WHERE Oid=$Oid and KSorszam=$KSorszam LIMIT 1"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 32 ");
    $rowDB     = mysqli_num_rows($result);
    if ($rowDB > 0) {
      // Jöhet a törlés
      $DeleteStr = "Delete FROM kep WHERE Oid=$Oid and KSorszam=$KSorszam LIMIT 1 ";
      if (!mysqli_query($MySqliLink,$DeleteStr))  {die("Hiba OM 33 ");} 
    }
}

//------------------------------------------------------------------------------------------------------------------
// KÉPEK NEVEINEK TÁROLÁSA A kep TÁBLÁBAN
//------------------------------------------------------------------------------------------------------------------

//Csak akkor kezdünk bele, ha valamelyik kép feltöltése gomb aktív
if ((($_POST['submit_Kep1'] == 'Feltöltés') 
|| ($_POST['submit_Kep2']  == 'Feltöltés') 
|| ($_POST['submit_Kep3']  == 'Feltöltés') 
|| ($_POST['submit_Kep4']  == 'Feltöltés') 
|| ($_POST['submit_Kep5']  == 'Feltöltés')) 
&& ($hozzaferes>6)) 
{
  $AktUrlap = 'Kepek';
  // Kiválasztjuk a kép sorszámát
  if ($_POST['submit_Kep1'] == 'Feltöltés') {$KSorszam = 1;}
  if ($_POST['submit_Kep2'] == 'Feltöltés') {$KSorszam = 2;}
  if ($_POST['submit_Kep3'] == 'Feltöltés') {$KSorszam = 3;}
  if ($_POST['submit_Kep4'] == 'Feltöltés') {$KSorszam = 4;}
  if ($_POST['submit_Kep5'] == 'Feltöltés') {$KSorszam = 5;}

  $KNev    = $_POST['KNev'];
  $KLeiras = $_POST['KLeiras'];

  if ($KepOK) {
    $KURL =  $_FILES["file"]["name"];

    $SelectStr = "SELECT * FROM kep WHERE Oid=$Oid and KSorszam=$KSorszam LIMIT 1"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 34 ");
    $rowDB     = mysqli_num_rows($result);
    if ($rowDB > 0) {
      //Ha már létezik az Oid oldal KSorszam sorszámú képe akkor frissítjük
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $UpdateStr = "UPDATE kep SET KURL='$KURL', KNev='$KNev', KLeiras='$KLeiras' WHERE Oid=$Oid and KSorszam=$KSorszam";
      if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 35 ");}
    } else {
       //Ha nem létezik az Oid oldal KSorszam sorszámú képe akkor létrehozzuk
      $InsertIntoStr = "INSERT INTO kep  VALUES ('',$Oid,'$KNev','$KURL', '$KLeiras', $KSorszam)";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  {die("Hiba OM 36 ");}
    }
  } else {
  //Ha kép nem csak neve vagy leírása változik
    mysqli_free_result($result);
    $SelectStr = "SELECT * FROM kep WHERE Oid=$Oid and KSorszam=$KSorszam LIMIT 1"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 37 ");
    $rowDB     = mysqli_num_rows($result); mysqli_free_result($result);    if ($rowDB > 0) {
      //Ha már létezik az Oid oldal KSorszam sorszámú képe akkor frissítjük

      $UpdateStr = "UPDATE kep SET  KNev='$KNev', KLeiras='$KLeiras' WHERE Oid=$Oid and KSorszam=$KSorszam";
      if (!mysqli_query($MySqliLink,$UpdateStr))  {die("Hiba OM 38 ");}
    } 
  }
}

//------------------------------------------------------------------------------------------------------------------
// VÁLASZTÁS A BEVITELI ŰRLAPOK KÖZÖTT
//------------------------------------------------------------------------------------------------------------------

if ($f2>0) {
  //Lekérdezzük a szerkesztés alatt álló oldal tíípuskódját
  $SelectStr = "SELECT OTipus FROM oldal WHERE id=$f2 LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 39 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $TipusKod  = $row['OTipus'];

// A kiválasztó gombok, és a hozzájuk tartozó címkék megjelenítése
// Amelyik aktív az checked jellemzőt kap
if($AktUrlap == 'Alap') { $HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chAlap' value='chAlap' checked><label class='divValsztLabel' for='chAlap'>Alapbeállítások</label>\n";}
  else { $HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chAlap' value='chAlap'><label class='divValsztLabel' for='chAlap'>Alapbeállítások</label>\n";}

if ($funkcio=='Modosit') {
  if($AktUrlap == 'Tartalom') {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTartalom' value='chTartalom' checked><label class='divValsztLabel' for='chTartalom'>Tartalom</label>\n";}
    else {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTartalom' value='chTartalom'><label class='divValsztLabel' for='chTartalom'>Tartalom</label>\n";}

  if ($TipusKod==OTermek) {
    if($AktUrlap == 'Termek') {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTermek' value='chTermek' checked><label class='divValsztLabel' for='chTermek'>Termék</label>\n";}
      else {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTermek' value='chTermek'><label class='divValsztLabel' for='chTermek'>Termék</label>\n";}
    if($AktUrlap == 'Jellemzo') {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chJellemzok' value='chJellemzok' checked><label class='divValsztLabel' for='chJellemzok'>Termékjellemzők</label>\n";}
      else  {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chJellemzok' value='chJellemzok'><label class='divValsztLabel' for='chJellemzok'>Termékjellemzők</label>\n";}
    if($AktUrlap == 'TermekLeir') {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTermekLeir' value='chTermekLeir' checked><label class='divValsztLabel' for='chTermekLeir'>Termékleírás</label>\n";}
      else  {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chTermekLeir' value='chTermekLeir'><label class='divValsztLabel' for='chTermekLeir'>Termékleírás</label>\n";}
  }

  if($AktUrlap == 'Kepek') {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chKepek' value='chKepek' checked><label class='divValsztLabel' for='chKepek'>Képek</label>\n";}
    else {$HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chKepek' value='chKepek'><label class='divValsztLabel' for='chKepek'>Képek</label>\n";}
}

// Az előnézet új oldal esetén nem látszik
if ($funkcio!='UjOldal') { $HTMLkod .= "<input type='radio' name='divValszt' class='divValszt' id='chElonezet' value='chElonezet'><label class='divValsztLabel' for='chElonezet'>Előnézet</label><br>\n";}

  $HTMLkod .= $VisszaHidden; 
}



//------------------------------------------------------------------------------------------------------------------
// ÚJ OLDAL ŰRLAP megjelenítése  
//------------------------------------------------------------------------------------------------------------------
// Új oldalnak csupán a neve, rövíd leírása, kulcsszavai, priorítása és típusa állítható
// ASz oldal típusa később nem módosítható


if ($funkcio=='UjOldal') { $HTMLkod .= "<h1>HŐŐŐŐŐŐŐŐ</h1>";
  $HTMLkod .= "\n<div id='DIValap' style='display:block;'>\n";
  $HTMLkod .= "<form action='?f0=szerkeszt&f1=Modosit&f2=$f2&f3=$f3&f4=$f4&f5=$f5' method='post' id='form_OAlapbeallUrlap'>\n";
  $HTMLkod .= "";

  $HTMLkod .= "<p><label for='ONev' class='label_1'>ÚJ oldal/termék neve:</label><br>\n ";
  $HTMLkod .= "<input type='text' name='ONev' id='ONev' placeholder='Oldalnév' 
             value='' style='font-size:1.1em;'> </p>\n";

  $HTMLkod .= "<p><label for='OKulcszsavak' class='label_1'>ÚJ oldal/termék kulcsszavai:</label><br> \n";
  $HTMLkod .= "<input type='text' name='OKulcszsavak' id='OKulcszsavak' placeholder='OKulcszsavak' size='100' maxlength='100'
              value='' > </p>\n";

  $HTMLkod .= "<p><label for='ORLeiras' class='label_1'>ÚJ oldal/termék rövíd leírása:</label><br> \n";
  $HTMLkod .= "<textarea name='ORLeiras' id='ORLeiras' placeholder='Rövíd leírása' 
              rows='4' cols='100' ></textarea></p>\n";

  $HTMLkod .= "<p><label for='OPrioritas' class='label_1'>ÚJ Oldal priorítása:</label><br> \n";
  $HTMLkod .= "<input type='number' name='OPrioritas' id='OPrioritas' min='0' max='255' step='1'
              value='0' > </p>\n";

  $HTMLkod .=  "<label for='TipValszt'>Típus: </label>
    <select name='TipValszt' id='TipValszt' size='1' >\n";
  $HTMLkod .=  "<option value='Kategoria'>Kategória</option>\n";
  $HTMLkod .=  "<option value='Alkategoria'>Alkategória</option>\n";
  $HTMLkod .=  "<option value='Termek'>Termék</option>\n";
  $HTMLkod .=  "<option value='Hirkategoria'>Hírkategoria</option>\n";
  $HTMLkod .=  "<option value='HirOldal'>Híroldal</option>\n";
  //!!!!!!!!!!!!!!! Új típusok esetén folytatni !!!!!!!!!!!!!!!!!!!!
  $HTMLkod .=  "</select>\n";

  $HTMLkod .=  "<br><br><input type='submit' name='submitAltalanos1' value='Létrehozás' style='float:right;' $tiltottSubmit><br>\n";

  $HTMLkod .= "</form>\n\n";
  $HTMLkod .= "</div>\n\n";

  echo $HTMLkod; $HTMLkod='';
}

//------------------------------------------------------------------------------------------------------------------
// ALAPBEÁLLÍTÁSOK ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------

if ($Oid>0) {
  $HTMLkod  .= "\n<div id='DIValap'>\n";
  // A szerkesztett oldal adatainak beolvasása
  $SelectStr = "SELECT * FROM oldal WHERE id=$Oid LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 39 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);

  $TipMut = $row['OTipus'];
  $Tipus  = $OldalTipusok[$TipMut];
  $OURL   = $row['OURL']; //!!!!!!

  // A kisképet csak módosításnál lehet megváltoztatni
  // Ekkor külön űrlapba kerűl
  if ($funkcio=='Modosit') {
    $HTMLkod .= "\n<div id='Form_Kiskep'>
      <form action='?f0=szerkeszt&f1=Modosit&f2=$f2&f3=$f3&f4=$f4&f5=$f5' method='post' enctype='multipart/form-data'>\n $VisszaHidden
      <img src='kepek/".$row['OKep']."' style='float:left;margin:5px;' alt='kis kép' height='60' >
      <label for='file_Kiskep' class='label_1'>Kiskép</label><br><br>".$row['OKep']."<br>
      <input type='file' name='file' id='file_Kiskep' ><br><br>\n
      <input type='submit' name='submit_Kiskep' value='Feltöltés' style='float:right;' $tiltottSubmit>
      <input type='submit' name='submit_KiskepTorol' value='Törlés' style='float:right;' $tiltottSubmit>
      <br><i>$UploadErr</i>
      </form> </div>\n\n";
  } else {
    $HTMLkod .= "<div id='Form_Kiskep'>
                <img src='kepek/".$row['OKep']."' style='float:left;margin:5px;' alt='kis kép' height='60' >
                </div>\n\n";
  }
  // Az alapbállítások űrlapjának összeállítása
  $HTMLkod .= "<form action='?f0=szerkeszt&f1=Modosit&f2=$f2&f3=$f3&f4=$f4&f5=$f5' method='post' id='form_OAlapbeallUrlap'>\n";
  $HTMLkod .= "<input type='hidden' name='OURL' value='".$row['OURL']."' >\n";
  
  if ($funkcio=='Modosit') {
    $HTMLkod .= "<p><label for='ONev' class='label_1'>Oldal/termék neve:</label><br>\n ";
    if ($Tipus=='Kezdolap') {
      //Kezdőlap esetén az oldal neve egyban a webáruház neve is, és módosítható
      $HTMLkod .= "<input type='text' name='ONev' id='ONev' placeholder='Oldalnév'         
          value='".$row['ONev']."' style='font-size:1.1em;' > </p>\n";
    } else {
      //A belső oldalak nevei nem módosíthatók
      $HTMLkod .= "<input type='text' name='ONev' id='ONev' placeholder='Oldalnév' 
          value='".$row['ONev']."' style='font-size:1.1em;' readonly> </p>\n";
    }
  }
  //Másolásnál a  belső oldalak nevei is módosíthatók
  if ($funkcio=='Masol') {
       $HTMLkod .= "<p><label for='ONev' class='label_1'>Oldal/termék ÚJ neve:</label><br>\n ";
       $HTMLkod .= "<input type='text' name='ONev' id='ONev' placeholder='Oldalnév' 
             value='".$row['ONev']."' style='font-size:1.1em;'> </p>\n";
  }
  // Tavábbi beviteli elemek és címkéik
  $HTMLkod .= "<p><label for='ONev' class='label_1'>Utolsó módosítás:</label> \n";
  $HTMLkod .= $row['ODatum']."  </p>";

  $HTMLkod .= "<p><label for='OKulcszsavak' class='label_1'>Oldal/termék kulcsszavai:</label><br> \n";
  $HTMLkod .= "<input type='text' name='OKulcszsavak' id='OKulcszsavak' placeholder='OKulcszsavak' size='100' maxlength='100'
              value='".$row['OKulcszsavak']."' > </p>\n";

  $HTMLkod .= "<p><label for='ORLeiras' class='label_1'>Oldal/termék rövíd leírása:</label><br> \n";
  $HTMLkod .= "<textarea name='ORLeiras' id='ORLeiras' placeholder='Rövíd leírása' 
              rows='4' cols='100' > ".$row['ORLeiras']." </textarea></p>\n";

  $HTMLkod .= "<p><label for='OPrioritas' class='label_1'>Oldal priorítása:</label><br> \n";
  $HTMLkod .= "<input type='number' name='OPrioritas' id='OPrioritas' min='0' max='255' step='1'
              value='".$row['OPrioritas']."' > </p>\n";
  
  // Az oldal típusának kiválasztása
//  $TipMut = $row['OTipus'];
//  $Tipus = $OldalTipusok[$TipMut];
  $HTMLkod .=  "<label for='TipValszt'>Típus: </label>
    <select name='TipValszt' id='TipValszt' size='1' disabled >\n";
  if ($Tipus=='Kezdolap') {$HTMLkod .=  "<option value='Kezdolap' selected>Kezdőlap</option>\n";}
      else {$HTMLkod  .=  '<option value="Kezdolap">Kezdőlap</option>';}
  if ($Tipus=='Kategoria') {$HTMLkod .=  "<option value='Kategoria' selected>Kategória</option>\n";}
      else  {$HTMLkod .=  "<option value='Kategoria'>Kategória</option>\n";}
  if ($Tipus=='Alkategoria') {$HTMLkod .=  "<option value='Alkategoria' selected>Alkategória</option>\n";}
      else {$HTMLkod  .=  "<option value='Alkategoria'>Alkategória</option>\n";}
  if ($Tipus=='Termek') {$HTMLkod .=  "<option value='Termek' selected>Termék</option>\n";}
      else {$HTMLkod  .=  "<option value='Termek'>Termék</option>\n";}
  if ($Tipus=='Hirkategoria') {$HTMLkod .=  "<option value='Hirkategoria' selected>Hírkategória</option>\n";}
      else {$HTMLkod .=  "<option value='Hirkategoria'>Hírkategória</option>\n";}
  if ($Tipus=='HirOldal') {$HTMLkod .=  "<option value='HirOldal' selected>Híroldal</option>\n";}
      else {$HTMLkod .=  "<option value='HirOldal'>Híroldal</option>\n";}
  //!!!!!!!!!!!!!!! Új típusok esetén folytatni !!!!!!!!!!!!!!!!!!!!
  $HTMLkod .=  "</select>\n";

  $OSzulo     = $row['OSzulo'];
  $SelectStr1 = "SELECT * FROM oldal WHERE id=$OSzulo LIMIT 1"; 
  $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba OM 40 ");
  $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC); mysqli_free_result($result1);
  $ValSzulo   = $row1['ONev'];

  // A szülőlista összeállítása
  $SzuloTipMut = -1;
  if ($TipMut>0) {
    switch ($TipMut) {
      case  "1":  $SzuloTipMut=0;  break;
      case  "2":  $SzuloTipMut=1;  break;
      case  "3":  $SzuloTipMut=2;  break;
      case "10":  $SzuloTipMut=0;  break;
      case "11":  $SzuloTipMut=10; break;
    }
  }
  $SzuloOk = 0; 
  if ($SzuloTipMut>-1) {
    $SzuloLista  = '';
    $SelectStr   = "SELECT * FROM oldal WHERE OTipus=$SzuloTipMut order by ONev "; 
    $result      = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 41 ");
    $SzuloLista .= "<option value='Mind' >Mind</option>\n";
    while($row   = mysqli_fetch_array($result))
    {
     if ($row['ONev']  ==$ValSzulo) 
         {$SzuloLista  .= "<option value='".$row['ONev']."' selected>".$row['ONev']."</option>\n"; $SzuloOk = 1;}
     else {$SzuloLista .= "<option value='".$row['ONev']."' >".$row['ONev']."</option>\n";}
    }
    mysqli_free_result($result);
    //A hírek a főoldalhoz is kapcsolhatók
    if ($TipMut ==11) {
      $SelectStr = "SELECT * FROM oldal WHERE OTipus=0 order by ONev "; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 42 ");
      $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      if ($row['ONev']  == $ValSzulo) 
         {$SzuloLista   .= "<option value='".$row['ONev']."' selected>".$row['ONev']."</option>\n"; $SzuloOk = 1;}
      else {$SzuloLista .= "<option value='".$row['ONev']."' >".$row['ONev']."</option>\n";}
    }
    $SzuloLista = "<label for='SzuloValaszt'>Szülő oldal: </label>
          <select name='SzuloValaszt' id='SzuloValaszt' size='1'>". $SzuloLista."</select>\n";
  }
  $HTMLkod .=  $SzuloLista;  

  // Nyomógombok az oldalra
  if ($funkcio =='Modosit') 
   {$HTMLkod   .= "<br><br>
     <input type='submit' name='submitAltalanos' value='Módosítás' style='float:right;' $tiltottSubmit> <br>\n";}
  if ($funkcio =='Masol') 
   {$HTMLkod   .= "<br><br>
     <input type='submit' name='submitAltalanos' value='Másolás' style='float:right;' $tiltottSubmit> <br>\n";}

  $HTMLkod .= "</form>\n\n";
  $HTMLkod .= "</div>\n\n";

}


//------------------------------------------------------------------------------------------------------------------
// TARTALOM ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------
if ($Oid>0) {
  $HTMLkod  .= "<div id='DIVtartalom'>";
  $HTMLkod  .= "<form action='#' method='post' id='form_OTartalomUrlap'>\n";

  $SelectStr = "SELECT * FROM  oldal_tartalom WHERE Oid=$Oid LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 43 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $OTartalom = karakter_csere_vissza($row['OTartalom']); 

  $HTMLkod  .= "<p><label for='OTartalom' class='label_1'>Szöveges tartalom:</label><br> \n";
  $HTMLkod  .= "<textarea name='OTartalom' id='OTartalom' placeholder='Szöveges tartalom' 
              rows='10' cols='100' >".$OTartalom."</textarea></p>\n";

  $HTMLkod  .= "<br><br><input type='submit' name='submitTartalom' value='Módosítás' style='float:right;' $tiltottSubmit> <br>\n";
  $HTMLkod  .= "</form></div>";
 
}
//------------------------------------------------------------------------------------------------------------------
// TERMÉK ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------

//TtulNev
if ($Oid>0) {
  $HTMLkod .= "<div id='DIVTermek'>";
  $HTMLkod .= "<form action='#' method='post' id='form_TermekUrlap'>\n";
  for ($i=1;$i<=10;$i++) {$TermekTmb[$i]['TKod'] = ''; $TermekTmb[$i]['TtulErt'] = ''; $TermekTmb[$i]['TAr'] = 0; 
     $TermekTmb[$i]['TSzorzo'] = 1; $TermekTmb[$i]['TSzalKlts'] = ''; $TermekTmb[$i]['TSzallit'] = ''; }

  $i = 0;
  $SelectStr = "SELECT * FROM termek WHERE Oid = $f2 ORDER by TKod"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 44 ");
  while($row = mysqli_fetch_array($result)) {
    $i++;
    $TermekTmb[$i]['TKod']      = $row['TKod'];
    $TermekTmb[$i]['TtulNev']   = $row['TtulNev'];  
    $TermekTmb[$i]['TtulErt']   = $row['TtulErt'];  
    $TermekTmb[$i]['TAr']       = $row['TAr'];
    $TermekTmb[$i]['TSzorzo']   = $row['TSzorzo'];
    $TermekTmb[$i]['TSzalKlts'] = $row['TSzalKlts'];  
    $TermekTmb[$i]['TSzallit']  = $row['TSzallit'];  
  }
  mysqli_free_result($result);
  $HTMLkod   .= "<table><tr><th>Kód</th><th>Kiemelt tulajdonság<br>
               <input type='text' name='TtulNev' id='TtulNev' size='20' maxlength='40' value='".$TermekTmb[1]['TtulNev']."' >
               </th><th>Ár</th><th>Szorzó</th><th>Szallítási Klts.</th><th>Szallítás (nap)</th></tr>";
  for ($i=1;$i<=10;$i++) {
    $HTMLkod .= "<tr><td><input type='text' name='TKod$i' id='TKod$i' size='10' maxlength='40'
                value='".$TermekTmb[$i]['TKod']."' >";
    $HTMLkod .= "</td><td><input type='text' name='TtulErt$i' id='TtulErt$i' size='20' maxlength='40'
                value='".$TermekTmb[$i]['TtulErt']."' >";
    $HTMLkod .= "</td><td><input type='text' name='TAr$i' id='TAr$i' size='10' maxlength='40'
                value='".$TermekTmb[$i]['TAr']."' >";
    $HTMLkod .= "</td><td><input type='text' name='TSzorzo$i' id='TSzorzo$i' size='4' maxlength='40'
                value='".$TermekTmb[$i]['TSzorzo']."' >";
    $HTMLkod .= "</td><td><input type='text' name='TSzalKlts$i' id='TSzalKlts$i' size='20' maxlength='40'
                value='".$TermekTmb[$i]['TSzalKlts']."' >";
    $HTMLkod .= "</td><td><input type='text' name='TSzallit$i' id='TSzallit$i' size='4' maxlength='40'
                value='".$TermekTmb[$i]['TSzallit']."' >";
    $HTMLkod .= "</td></tr>";  
  }  
  $HTMLkod   .= "</table>";
  $HTMLkod   .= "<br><br><input type='submit' name='submitTermek' value='Módosítás' style='float:right;' $tiltottSubmit><br>\n";
  $HTMLkod   .= "</form></div>";
}


//------------------------------------------------------------------------------------------------------------------
// JELLEMZŐK ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------

if ($Oid>0) {
  $HTMLkod .= "<div id='DIVJellemzok'>";
  $HTMLkod .= "<form action='#' method='post' id='form_JellemzokUrlap'>\n";

  for ($i=1;$i<=10;$i++) {$JellemzoTmb[$i]['JNev'] = ''; $JellemzoTmb[$i]['JErtek'] = ''; $JellemzoTmb[$i]['JSorszam'] = '';}

  $SelectStr  = "SELECT * FROM termek_jellemzo WHERE Oid = $f2 ORDER by JSorszam"; 
  $result     = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 45 ");
  while($row  = mysqli_fetch_array($result)) {
    $JSorszam = $row['JSorszam'];  
    $JellemzoTmb[$JSorszam]['JNev']     = $row['JNev'];
    $JellemzoTmb[$JSorszam]['JErtek']   = $row['JErtek'];  
    $JellemzoTmb[$JSorszam]['JSorszam'] = $row['JSorszam']; 
  }
  mysqli_free_result($result);
  $HTMLkod .= "<table><tr><th>Sorszám</th><th>Jellemző</th><th>Érték</th></tr>";

  for ($i=1;$i<=10;$i++) {
    $HTMLkod .= "<tr><td>
                 <input type='text' name='JSorszam$i' id='JSorszam$i' size='10' maxlength='40'
                    value='".$JellemzoTmb[$i]['JSorszam']."' >";
    $HTMLkod .= "</td><td>
                 <input type='text' name='JNev$i' id='JNev$i' size='10' maxlength='40'
                    value='".$JellemzoTmb[$i]['JNev']."' >";
    $HTMLkod .= "</td><td>
                 <input type='text' name='JErtek$i' id='JErtek$i' size='10' maxlength='40'
                    value='".$JellemzoTmb[$i]['JErtek']."' >";
    $HTMLkod .= "</td></tr>";
  }

  $HTMLkod .= "</table>";
  $HTMLkod .=  "<br><br><input type='submit' name='submitJellemzok' value='Módosítás' style='float:right;' $tiltottSubmit><br>\n";
  $HTMLkod .= "</form></div> ";

}
//------------------------------------------------------------------------------------------------------------------
// TERMÉKLEÍRÁS ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------

if ($Oid>0) {
  $HTMLkod  .= "<div id='DIVTleiras'>";
  $HTMLkod  .= "<form action='#' method='post' id='form_TermekleirasUrlap'>\n";

  $SelectStr = "SELECT * FROM  termek_leiras WHERE Oid=$Oid LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 46 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);

  $HTMLkod  .= "<p><label for='Termekleiras' class='label_1'>A termék részletes bemutatása:</label><br> \n";
  $HTMLkod  .= "<textarea name='Termekleiras' id='Termekleiras' placeholder='A termék részletes bemutatása' 
              rows='10' cols='100' >".$row['TLeiras']."</textarea></p>\n";

  $HTMLkod  .= "<br><br><input type='submit' name='submitTermekleiras' value='Módosítás' style='float:right;' $tiltottSubmit><br>\n";
  $HTMLkod  .= "</form></div>";

}

//------------------------------------------------------------------------------------------------------------------
// KÉPEK ŰRLAP megjelenítése
//------------------------------------------------------------------------------------------------------------------

if ($Oid>0) {
  $ModKepMut = $KSorszam;
  $HTMLkod  .= "<div id='DIVKepek'>";
  for ($i=1;$i<6;$i++) {$KepekTmb[$i]['KNev'] = ''; $KepekTmb[$i]['KURL'] = ''; $KepekTmb[$i]['KLeiras'] = ''; }
  $SelectStr = "SELECT * FROM kep WHERE Oid = $f2 ORDER by id"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OM 48 ");
  while($row = mysqli_fetch_array($result)) {
    $i = $row['KSorszam'];
    $KepekTmb[$i]['KNev']    = $row['KNev'];
    $KepekTmb[$i]['KURL']    = $row['KURL'];
    $KepekTmb[$i]['KLeiras'] = $row['KLeiras'];    
  }
  mysqli_free_result($result);
  for ($i=1;$i<6;$i++) {
    $HTMLkod .= "<div id='Form_Kep$i' class='Form_Kep'>";
    $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
    $HTMLkod .= "<img src='kepek/".$KepekTmb[$i]['KURL']."' style='float:left;margin:5px;' alt='kép$i' height='60' >";
    $HTMLkod .= "<label for='file_Kep$i' class='label_1'>Kép $i</label><br><br>".$KepekTmb[$i]['KURL']."<br>";
    $HTMLkod .= "<input type='file' name='file' id='file_Kep$i' ><br><br>";

    $HTMLkod .= "<p><label for='KNev' class='label_1'>A kép neve:</label><br> \n";
    $HTMLkod .= "<input type='text' name='KNev' id='KNev' placeholder='A kép neve' size='40' maxlength='40'
                value='".$KepekTmb[$i]['KNev']."' > </p>\n";

    $HTMLkod .= "<p><label for='KLeiras' class='label_1'>A kép rövíd leírása:</label><br> \n";
    $HTMLkod .= "<textarea name='KLeiras' id='KLeiras' placeholder='Kép rövíd leírása' 
                rows='4' cols='40' > ".$KepekTmb[$i]['KLeiras']." </textarea></p>\n";

    if ($ModKepMut==$i) {$HTMLkod .= "<i>$UploadErr</i> ";}

    $HTMLkod .= "<input type='submit' name='submit_Kep$i' value='Feltöltés' style='float:right;' $tiltottSubmit>";
    $HTMLkod .= "<input type='submit' name='submit_Torol_Kep$i' value='Törlés' style='float:right;' $tiltottSubmit>";
    $HTMLkod .= "</form> </div>\n\n";
  }

  $HTMLkod .= "</div> ";

}

//------------------------------------------------------------------------------------------------------------------
// ELŐNÉZET
//------------------------------------------------------------------------------------------------------------------

  if ($funkcio!='UjOldal') { 

  $HTMLURL  = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?f0=$OURL";



  $HTMLkod .= "<div id='DIVElonezet1'> <b>Vízszintes felbontás: </b>" ;
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo320' value='chElo320'>
               <label class='divEloValsztLabel' for='chElo320'>320 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo480' value='chElo480'>
               <label class='divEloValsztLabel' for='chElo480'>480 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo720' value='chElo720'>
               <label class='divEloValsztLabel' for='chElo720'>720 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo800' value='chElo800'>
               <label class='divEloValsztLabel' for='chElo800'>800 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo1080' value='chElo1080'>
               <label class='divEloValsztLabel' for='chElo1080'>1080 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo1024' value='chElo1024' checked>
               <label class='divEloValsztLabel' for='chElo1024'>1024 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo1280' value='chElo1280' >
               <label class='divEloValsztLabel' for='chElo1280'>1280 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo1600' value='chElo1600'>
               <label class='divEloValsztLabel' for='chElo1600'>1600 px | </label>";
  $HTMLkod .= "<input type='radio' name='chElo' class='EloValaszt' id='chElo1920' value='chElo1920'>
               <label class='divEloValsztLabel' for='chElo1920'>1920 px | </label>";

  $HTMLkod  .= "<div id='EloNezetKulso'>";

  $HTMLkod  .= "<div id='EloNezetBelso'>";
 
  $HTMLkod  .= "<iframe id='EloNezetIframe' src='$HTMLURL'  sandbox=''>" ;

  $HTMLkod  .= "</div></div>";
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Még üres !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

  $HTMLkod .= "</div>";



  echo $HTMLkod;
}


?>
