<?php


//------------------------------------------------------------------------------------------------------------------
// MEGRENDELÉS ŰRLAP KIÍRATÁSA	
//------------------------------------------------------------------------------------------------------------------
function Kiir_MegrendelUrlap($Lepes,$ErrorStr)
{
global $AktOldal,  $MySqliLink, $mm_azon, $mm_felhasznalo, $f0, $f1, $f2, $f3, $f4;
  if ($Lepes=='ellenorzes'){$readonly='readonly';} else {$readonly='';}
  // A hívó oldal URL-jének tárolása
  $Pf0 = tiszta_szov($_POST['f0']); 
  $Pf1 = tiszta_szov($_POST['f1']);
  $Pf2 = tiszta_szov($_POST['f2']);
  $Pf3 = tiszta_szov($_POST['f3']);
  $Pf4 = tiszta_szov($_POST['f4']);
  $HTMLVisszaURL  = "<input type='hidden' name='f0' value='$Pf0'>\n";
  $HTMLVisszaURL .= "<input type='hidden' name='f1' value='$Pf1'>\n";
  $HTMLVisszaURL .= "<input type='hidden' name='f2' value='$Pf2'>\n";
  $HTMLVisszaURL .= "<input type='hidden' name='f3' value='$Pf3'>\n";
  $HTMLVisszaURL .= "<input type='hidden' name='f4' value='$Pf4'>\n";

  //Ellenőrizzük, hogy a munkamenethez tartozik-e megrendelés tábla
  $SelectStr = "SELECT * FROM megrendeles WHERE mmAzon='$mm_azon' LIMIT 1";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 01 ");
  $rowDB     = mysqli_num_rows($result);
  if ($rowDB>0) {
    //Ha van akkor betöltjük a megrendelés adatait
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
    $Fnev      = $row['Fnev'];
    $Rszemnev  = $row['Rszemnev'];
    $Remail    = $row['Remail'];
    $Rtelszam1 = $row['Rtelszam1'];
    $Rtelszam2 = $row['Rtelszam2'];
    $Rorszag   = $row['Rorszag'];
    $Rvaros    = $row['Rvaros'];
    $Rirszam   = $row['Rirszam'];
    $Rcim      = $row['Rcim'];
    $SZorszag  = $row['SZorszag'];
    $SZvaros   = $row['SZvaros'];
    $SZirszam  = $row['SZirszam'];
    $SZcim     = $row['SZcim'];
    $RStatus = $row['RStatus'];
    $Rip = $row['Rip'];
  } else {
    //Ha nincs még, akkor létrehozzuk
    mysqli_free_result($result);
    if ($mm_felhasznalo>'') {
      //Bejelentkezett felhasználó esetén amit lehet másolunk
      $Fnev = $mm_felhasznalo;
      $SelectStr = "SELECT * FROM felhasznalo_reg WHERE Fnev='$Fnev' Limit 1"; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 02 ");
      $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $Fid       = $row['id'];
      $Rszemnev  = $row['Fszemnev'];
      $Remail    = $row['Femail'];
      $SelectStr = "SELECT * FROM felhasznalo_telefon WHERE Fid='$Fid' Limit 2"; 
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 03 ");
      $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); 
      if ($row['Ftelszam']>'') {$Rtelszam1 = $row['Ftelszam'];}
      $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); 
      if ($row['Ftelszam']>'') {$Rtelszam2 = $row['Ftelszam'];}
      mysqli_free_result($result);
      $SelectStr = "SELECT * FROM felhasznalo_cim WHERE Fid='$Fid' Limit 1";  
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 04 ");
      $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $Rorszag   = $row['Forszag'];
      $Rvaros    = $row['Fvaros'];
      $Rirszam   = $row['Firszam'];
      $Rcim      = $row['Fcim'];
      $SZorszag  = '';
      $SZvaros   = '';
      $SZirszam  = '';
      $SZcim     = '';
      $RStatus = 0;
      $Rip     = getip();
    } else {
      //Ismeretlen felhasználó esetén mindent kezdőértékre állítunk
      $Fnev = '';
      $Rszemnev  = '';
      $Remail    = '';
      $Rtelszam1 = '';
      $Rtelszam2 = '';
      $Rorszag   = '';
      $Rvaros    = '';
      $Rirszam   = '';
      $Rcim      = '';
      $SZorszag  = '';
      $SZvaros   = '';
      $SZirszam  = '';
      $SZcim     = '';
      $RStatus   = 0;
      $Rip = getip();
    }
  }

 // A kosár tartalmának beolvasása
  $SelectStr = "SELECT * FROM  kocsi WHERE mmAzon='$mm_azon'";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 05 "); 
  $i = 0;
  while($rowtermek = mysqli_fetch_array($result))
  {
    $TermekArr[$i]['TKod'] = $rowtermek['TKod'];
    $TermekArr[$i]['DB']   = $rowtermek['DB'];
    $i++;
  } mysqli_free_result($result);
  $SorDB = $i-1;
  for ($j=0;$j<=$SorDB;$j++) {
    $SelectStr  = "SELECT * FROM termek WHERE TKod='".$TermekArr[$j]['TKod']."'";
    $result     = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 06 "); 
    $rowtermek  = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $TermekArr[$j]['Oid'] = $rowtermek['Oid'];
    $TermekArr[$j]['TAr'] = $rowtermek['TAr'];
    $TermekArr[$j]['TSzorzo']  = $rowtermek['TSzorzo'];
    $TermekArr[$j]['TtulErt']  = $rowtermek['TtulErt'];
    $TermekArr[$j]['TSzallit'] = $rowtermek['TSzallit'];
    $SelectStr = "SELECT ONev FROM oldal WHERE id='".$TermekArr[$j]['Oid']."'";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 07 ");  
    $rowtermek = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $TermekArr[$j]['ONev'] = $rowtermek['ONev'];
  }
  // A termékeket megjelenítő táblázat összeállítása
  $Osszesen   = 0;
  $HTMLkocsi  = "<fieldset class='Nev'>  <legend> Terméklista: </legend><table>\n";
  $HTMLkocsi .= "<tr><th> Termék </th><th> Kód </th><th> Tulajdonság </th><th> Ár </th><th> Kedv.
                 </th><th> Akciós ár </th><th> Szállítás </th><th> db </th><th> Összesen </th></tr>\n";
  for ($j=0;$j<=$SorDB;$j++) {
    $Kedvezmeny  = (1-$TermekArr[$j]['TSzorzo']) * 100;
    $AkciosAr    = $TermekArr[$j]['TAr'] * $TermekArr[$j]['TSzorzo']; $AkciosArKi = ceil($AkciosAr);
    $SorOsszesen = $AkciosAr * $TermekArr[$j]['DB'];
    $Osszesen    = $Osszesen + $SorOsszesen;
    $HTMLkocsi  .= "
     <tr><td> ".$TermekArr[$j]['ONev']." 
     </td><td> ".$TermekArr[$j]['TKod']." 
     </td><td> ".$TermekArr[$j]['TtulErt']." 
     </td><td class='jobbra'> ".ceil($TermekArr[$j]['TAr'])." Ft 
     </td><td class='kozepre'><b> -".$Kedvezmeny."% </b>
     </td><td class='jobbra'> ".$AkciosArKi." Ft 
     </td><td class='jobbra'> ".$TermekArr[$j]['TSzallit']." nap
     </td><td class='kozepre'> <input type='number' name='RTermekDB$j' $readonly min='0' max='1000' step='' value='".$TermekArr[$j]['DB']."'> 
     </td><td class='jobbra'> ".$SorOsszesen." Ft
     </td></tr>"; 
     $HTMLkocsiPL .= "<input type='hidden' name='RTermekKod$j' value='".$TermekArr[$j]['TKod']."'>\n";
  }
  $HTMLkocsi .= '</table></fieldset>'.$HTMLkocsiPL ;
  $HTMLkocsi .= "<input type='hidden' name='SorDB' value='$SorDB'>\n";
  if ($Lepes =='ellenorzes'){$UrlapCim = 'Megrendelés adatainak ellenőrzése';} else {$UrlapCim = 'Megrendelés összeállítása';}
  // Megjelenítés
  $HTMLkod    = "<div id='DIVigazit'><div id='div_RendelUrlap'> <h1> $UrlapCim </h1>\n";
  $HTMLkod   .= "<form action='?f0=megrendel' method='post' id='form_RendelUrlap'>\n";
  $HTMLkod   .= "<input type='hidden' name='form_RendelUrlap' value='form_RendelUrlap'>\n";
  $HTMLkod    = $HTMLkod."<fieldset class='Nev'>  <legend> A megrendelő adatai: </legend>\n";
    if ($mm_felhasznalo>'') {
       $HTMLkod = $HTMLkod."<p><label for='fnev' class='label_1'>Felhasználónév: </label> \n";
       $HTMLkod = $HTMLkod."<input type='text' name='Fnev' id='Fnev' placeholder='Felhasználónév' value='$Fnev' readonly> </p>\n";
    }
  if (strpos($ErrorStr,'Err00')!== false) {$class="class='Error'";} else {$class='';}
  $HTMLkod .= "<p style='float:left'><label for='Rszemnev' class='label_1'>*Név: </label> \n";
  $HTMLkod .= "<input type='text' name='Rszemnev' id='Rszemnev' $class placeholder='Név' value='$Rszemnev' $readonly> </p>\n";

  if (strpos($ErrorStr,'Err01')!== false) {$class="class='Error'";} else {$class='';}
  $HTMLkod .= "<p style='float:left;'><label for='Remail' class='label_1'>*Email: </label> \n";
  $HTMLkod .= "<input type='text' name='Remail' id='Remail'$class placeholder='Email' value='$Remail' $readonly> </p> \n";
  $HTMLkod .= "<span style='clear:left'></span>";

  if (strpos($ErrorStr,'Err02')!== false) {$class="class='Error'";} else {$class='';}
  $HTMLkod .= "<p style='float:left'><label for='Rtelszam1' class='label_1'>*Telefon 1: </label> \n";
  $HTMLkod .= "<input type='text' name='Rtelszam1' id='Rtelszam1' $class placeholder='Telefonszám' value='$Rtelszam1' $readonly> </p>\n";
  $HTMLkod .= "<p style='float:left'><label for='Rtelszam2' class='label_1'>Telefon 2: </label> \n";
  $HTMLkod .= "<input type='text' name='Rtelszam2' id='Rtelszam2' placeholder='Telefonszám' value='$Rtelszam2' $readonly> </p>\n";

  $HTMLkod .= "<p style='float:left'><label for='Rorszag' class='label_1'>Orszag: </label> \n";
  $HTMLkod .= "<input type='text' name='Rorszag' id='Rorszag' placeholder='Orszag' value='$Rorszag' $readonly> </p>\n";
  $HTMLkod .= "<p style='float:left'><label for='Rvaros' class='label_1'>Város: </label> \n";
  $HTMLkod .= "<input type='text' name='Rvaros' id='Rvaros' placeholder='Város' value='$Rvaros' $readonly> </p>\n";
  $HTMLkod .= "<span style='clear:left;'></span>\n";
  $HTMLkod .= "<p style='float:left'><label for='Rirszam' class='label_1'>Irányítószám: </label> \n";
  $HTMLkod .= "<input type='text' name='Rirszam' id='Rirszam' placeholder='Irányítószám' value='$Rirszam' $readonly> </p>\n";
  $HTMLkod .= "<p style='float:left'><label for='Rcim' class='label_1'>Cím: </label> \n";
  $HTMLkod .= "<input type='text' name='Rcim' id='Rcim' placeholder='Közterület, házszám, emelet...' value='$Rcim' $readonly> </p>\n";
  $HTMLkod .= "</fieldset>\n"; 

  $HTMLkod .= "<fieldset class='Nev'>  <legend> Szállítási cím: </legend>\n";
  $HTMLkod .= "<p>Ha különbözik a számlázási címtől.</p>\n";
  $HTMLkod .= "<p style='float:left'><label for='SZorszag' class='label_1'>Orszag: </label> \n";
  $HTMLkod .= "<input type='text' name='SZorszag' id='SZorszag' placeholder='Orszag' value='$SZorszag' $readonly> </p>\n";
  $HTMLkod .= "<p style='float:left'><label for='SZvaros' class='label_1'>Város: </label> \n";
  $HTMLkod .= "<input type='text' name='SZvaros' id='SZvaros' placeholder='Város' value='$SZvaros' $readonly> </p>\n";
  $HTMLkod .= "<span style='clear:left;height:0;'></span>\n";
  $HTMLkod .= "<p style='float:left'><label for='SZirszam' class='label_1'>Irányítószám: </label> \n";
  $HTMLkod .= "<input type='text' name='SZirszam' id='SZirszam' placeholder='Irányítószám' value='$SZirszam' $readonly> </p>\n";
  $HTMLkod .= "<p style='float:left;'><label for='SZcim' class='label_1'>Cím: </label>\n";
  $HTMLkod .= "<input type='text' name='SZcim' id='SZcim' placeholder='Közterület, házszám, emelet...' value='$SZcim' $readonly> </p>\n";
  $HTMLkod .= "</fieldset>\n"; 

  if ($TermekArr[0]['ONev']>'') {$HTMLkod .= $HTMLkocsi; $tovabbOK=1;} else {$HTMLkod .= 'A Ön kosara üres.'; $tovabbOK=0;}
  if ($Lepes=='letrehozas') 
    {$HTMLkod .= "<a href='?f0=$Pf0&f1=$Pf1&f2=$Pf2&f3=$Pf4&f3=$Pf4' class='KepBtn'>  Mégsem</a>\n";
     if ($tovabbOK==1) {$HTMLkod .= "<input type='submit' name='RendelesLetrehoz' value='Tovább' >\n";}}
  else
    {$HTMLkod .=  "<input type='submit' name='MegrendelSubmit' value='Vissza' >\n";
     $HTMLkod .= "<input type='submit' name='RendelesVeglegesít'  value='Tovább' >\n";}

  $HTMLkod .= $HTMLVisszaURL;
  $HTMLkod .= "</form></div></div>\n";
  echo $HTMLkod;
}


//------------------------------------------------------------------------------------------------------------------
// MEGRENDELÉS TÁBLA MÓDOSÍTÁSA	
//------------------------------------------------------------------------------------------------------------------

function Ment_Megrendeles()
{
global $AktOldal,  $MySqliLink, $mm_azon, $mm_felhasznalo, $f0, $f1, $f2, $f3, $f4;

  $ErrorStr ='';
  $Fnev = ''; $Rszemnev = ''; $Remail = ''; $Rtelszam1 = ''; $Rtelszam2 = '';
  $Rorszag = ''; $Rvaros  = ''; $Rirszam = ''; $Rcim = '';
  $SZorszag = ''; $SZvaros  = ''; $SZirszam = ''; $SZcim = ''; $SorDB = 0;
  $RStatus = 0;   $Rip = getip();


  if ($_POST['Fnev'] > '')  { $Fnev = tiszta_szov($_POST['Fnev']); } 

  if ($_POST['Rszemnev'] > '')  { $Rszemnev = tiszta_szov($_POST['Rszemnev']);} else {$ErrorStr .='Err00 ';}
  if ($_POST['Remail'] > '')    { $Remail = tiszta_szov($_POST['Remail']);} else {$ErrorStr .='Err01 ';}
  if ($_POST['Rtelszam1'] > '') { $Rtelszam1 = tiszta_szov($_POST['Rtelszam1']);}  else {$ErrorStr .='Err02 ';}
  if ($_POST['Rtelszam2'] > '') { $Rtelszam2 = tiszta_szov($_POST['Rtelszam2']);}

  if ($_POST['Rorszag'] > '') { $Rorszag = tiszta_szov($_POST['Rorszag']);} 
  if ($_POST['Rvaros'] > '')  { $Rvaros  = tiszta_szov($_POST['Rvaros']);}
  if ($_POST['Rirszam'] > '') { $Rirszam = tiszta_szov($_POST['Rirszam']);}
  if ($_POST['Rcim'] > '')    { $Rcim = tiszta_szov($_POST['Rcim']);}

  if ($_POST['SZorszag'] > '') { $SZorszag = tiszta_szov($_POST['SZorszag']);}
  if ($_POST['SZvaros'] > '')  { $SZvaros = tiszta_szov($_POST['SZvaros']);}
  if ($_POST['SZirszam'] > '') { $SZirszam = tiszta_szov($_POST['SZirszam']);}
  if ($_POST['SZcim'] > '')    { $SZcim = tiszta_szov($_POST['SZcim']);}

  if ($_POST['SorDB'] > '')  { $SorDB = tiszta_int($_POST['SorDB']);} 

  //Ellenőrizzük, hogy a munkamenethez tartozik-e megrendelés tábla
  $SelectStr = "SELECT * FROM megrendeles WHERE mmAzon='$mm_azon' LIMIT 1";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 08 ");
  $rowDB = mysqli_num_rows($result); mysqli_free_result($result);
  if ($rowDB>0) {
    //Ha van akkor frissítjük a megrendelés adatait
           $UpdateStr = "";
           if ($Rszemnev>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rszemnev='$Rszemnev'";} 
              else {$UpdateStr .= " Rszemnev='$Rszemnev'";}}
           if ($Remail>'') { if ($UpdateStr>'') {$UpdateStr .= ", Remail='$Remail'";} 
              else {$UpdateStr .= " Remail='$Remail'";}}
           if ($Rtelszam1>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rtelszam1='$Rtelszam1'";} 
              else {$UpdateStr .= " Rtelszam1='$Rtelszam1'";}}
           if ($Rtelszam2>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rtelszam2='$Rtelszam2'";} 
              else {$UpdateStr .= " Rtelszam2='$Rtelszam2'";}}

           if ($Rorszag>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rorszag='$Rorszag'";} 
              else {$UpdateStr .= " Rorszag='$Rorszag'";}}
           if ($Rvaros>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rvaros='$Rvaros'";} 
              else {$UpdateStr .= " Rvaros='$Rvaros'";}}
           if ($Rirszam>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rirszam='$Rirszam'";} 
              else {$UpdateStr .= " Rirszam='$Rirszam'";}}
           if ($Rcim>'') { if ($UpdateStr>'') {$UpdateStr .= ", Rcim='$Rcim'";} 
              else {$UpdateStr .= " Rcim='$Rcim'";}}

           if ($SZorszag>'') { if ($UpdateStr>'') {$UpdateStr .= ", SZorszag='$SZorszag'";} 
              else {$UpdateStr .= " SZorszag='$SZorszag'";}}
           if ($SZvaros>'') { if ($UpdateStr>'') {$UpdateStr .= ", SZvaros='$SZvaros'";} 
              else {$UpdateStr .= " SZvaros='$SZvaros'";}}
           if ($SZirszam>'') { if ($UpdateStr>'') {$UpdateStr .= ", SZirszam='$SZirszam'";} 
              else {$UpdateStr .= " SZirszam='$SZirszam'";}}
           if ($SZcim>'') { if ($UpdateStr>'') {$UpdateStr .= ", SZcim='$SZcim'";} 
              else {$UpdateStr .= " SZcim='$SZcim'";}}

           if ($UpdateStr>'') {$UpdateStr .= ", RStatus=$RStatus";} 
              else {$UpdateStr .= " RStatus=$RStatus";}
           if ($UpdateStr>'') {$UpdateStr .= ", Rip='$Rip'";} 
              else {$UpdateStr .= " Rip='$Rip'";}

           $UpdateStr = "UPDATE megrendeles SET $UpdateStr WHERE mmAzon='$mm_azon' ";
           if (!mysqli_query($MySqliLink,$UpdateStr))  { echo "Hiba RE 09 "; }
// Kosar frissíítése
    $TermekDB = tiszta_int($_POST['SorDB']);
    
    for ($j=0;$j<=$TermekDB;$j++) {
        $TKodStr = "RTermekKod$j";
        $TKod = tiszta_szov($_POST[$TKodStr]);
        if ($TKod>'') {
          $TDBStr = "RTermekDB$j";
          $TDB = tiszta_szov($_POST[$TDBStr]);
          if ($TDB > 0) {
            $UpdateStr = "UPDATE kocsi SET DB=$TDB WHERE TKod='$TKod' and mmAzon='$mm_azon'";
            if (!mysqli_query($MySqliLink,$UpdateStr))  { echo "Hiba RE 10 "; }
          } else {
            $SelectStr = "Delete FROM kocsi WHERE TKod='".$TKod."' and mmAzon='$mm_azon'";
            if (!mysqli_query($MySqliLink,$SelectStr)) { echo "Hiba RE 11 "; }
          }
        }     
    }

  } else {
    //Ha nincs akkor létrehozzuk
      $InsertIntoStr = "INSERT INTO megrendeles VALUES ('','$mm_azon','$Fnev','$Rszemnev','$Remail','$Rtelszam1','$Rtelszam2', 
                       '$Rorszag','$Rvaros','$Rirszam','$Rcim','$SZorszag','$SZvaros','$SZirszam','$SZcim',$RStatus,'$Rip',NOW())";
      if (!mysqli_query($MySqliLink,$InsertIntoStr))  { echo "Hiba RE 12 "; }    
  }
  return $ErrorStr;
}
//------------------------------------------------------------------------------------------------------------------
// MEGRENDELÉS VÉGLEGESÍTÉSE
//------------------------------------------------------------------------------------------------------------------
function Veglegesit_Megrendeles()
{
global $AktOldal,  $MySqliLink, $mm_azon, $mm_felhasznalo, $f0, $f1, $f2, $f3, $f4;

  $SorDB = 0;
  $Rip = getip();

  if ($_POST['SorDB'] > '')  { $SorDB = tiszta_int($_POST['SorDB']);}

  for ($j=0;$j<=$SorDB;$j++) {
    $RternekkodStr  = "RTermekKod$j";
    $RTermekKod[$j] = tiszta_szov($_POST[$RternekkodStr]);
    $RTermekDBStr   = "RTermekDB$j";
    $RTermekDB[$j]  = tiszta_szov($_POST[$RTermekDBStr]); 
  }
  //Ellenőrizzük, hogy a munkamenethez tartozik-e megrendelés tábla
  $SelectStr = "SELECT id FROM megrendeles WHERE mmAzon='$mm_azon' LIMIT 1";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 13 ");
  $rowDB     = mysqli_num_rows($result); 
  if ($rowDB>0) {
    //Ha van akkor frissítjük a megrendelés adatait
    $row  = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
    $megrendelesID = $row['id'];
    $UpdateStr = "UPDATE megrendeles SET mmAzon='-', RStatus=1 WHERE id=$megrendelesID ";
    if (!mysqli_query($MySqliLink,$UpdateStr))  { echo "Hiba RE 14 "; } 

    // A kocsi tartalmát átmásoljuk és a megrendeléshez kapcsoljuk
    for ($j=0;$j<=$SorDB;$j++) {
      $SelectStr = "SELECT * FROM termek WHERE TKod='".$RTermekKod[$j]."'";
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 15 "); 
      $rowtermek = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $TermekArr[$j]['Oid'] = $rowtermek['Oid'];
      $TermekArr[$j]['TAr'] = $rowtermek['TAr'];
      $TermekArr[$j]['TSzorzo']   = $rowtermek['TSzorzo'];
      $TermekArr[$j]['TtulErt']   = $rowtermek['TtulErt'];
      $TermekArr[$j]['TSzallit']  = $rowtermek['TSzallit'];
      $TermekArr[$j]['TSzalKlts'] = $rowtermek['TSzalKlts'];
      //Terméknév lekérdezése
      $SelectStr = "SELECT ONev FROM oldal WHERE id='".$TermekArr[$j]['Oid']."'";
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 16 ");  
      $rowtermek = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      $TermekArr[$j]['ONev'] = $rowtermek['ONev'];

      $InsertIntoStr = "INSERT INTO megrendelt_termek VALUES ('',$megrendelesID,'".$TermekArr[$j]['ONev']."','".$RTermekKod[$j]."',".$RTermekDB[$j].","
                       .$TermekArr[$j]['TAr'].",".$TermekArr[$j]['TSzorzo'].",'".$TermekArr[$j]['TSzalKlts']."',".$TermekArr[$j]['TSzallit'].")";
      if (!mysqli_query($MySqliLink,$InsertIntoStr)) { echo "Hiba RE 17 "; }
  
      $SelectStr = "Delete FROM kocsi WHERE mmAzon='$mm_azon' ";
      if (!mysqli_query($MySqliLink,$SelectStr)) { echo "Hiba RE 18 "; }    

      echo "<h1>Köszönjük a vásárlást!</h1>";
    }
  } else {
   // Baj van!!
  }
}

//------------------------------------------------------------------------------------------------------------------
// MEGRENDELÉSEK KIÍRATÁSA	
//------------------------------------------------------------------------------------------------------------------
function Kiir_Megrendelesek()
{
  global $AktOldal,  $MySqliLink, $mm_azon, $mm_felhasznalo, $hozzaferes, $f0, $f1, $f2, $f3, $f4;
//--------------------------------------------------------------------------------------------------------------
// AZ ŰRLAP ADATAINAK FELDOLGOZÁSA 

if (($_POST['ren_status'] > '') and ($hozzaferes>5)){
  $Ujstatus=0;
  switch ($_POST['ren_status']) {
    case "Elküldve":  $Ujstatus=1;  break;
    case "Olvasva":  $Ujstatus=2;  break;
    case "Egyeztetés megtörtént": $Ujstatus=3;  break;
    case "Kiszállítva":  $Ujstatus=4;  break;
    case "Törlés":  $Ujstatus=100;  break;
  }
  // Aktuális státusz lekérdezése  
  $SelectStr = "SELECT * FROM megrendeles WHERE id=$f1";  
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 19 "); 
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $AktStatus = $row['RStatus'];

  // Ha a régi és új státusz is nagyobb 0-nál, akkor egyszerüen frissíthető 
  // A még el nem küldött megrendelés nem módosítható, és az elküldött sem minősíthető vissza
  if (($Ujstatus>0) and ($AktStatus>0) and ($hozzaferes>6)) {
    $UpdateStr = "UPDATE megrendeles SET RStatus=$Ujstatus WHERE id=$f1"; 
    mysqli_query($MySqliLink,$UpdateStr) OR die("Hiba RE 20 ");     
  }
  // Az elküldött megrendeléseknél (státusz>0) a kapcsolt termékeket is töröljük
  if (($Ujstatus==100) and ($AktStatus>0) and ($hozzaferes>6))  {
    $DeleteStr = "Delete FROM megrendeles WHERE id=$f1"; 
    mysqli_query($MySqliLink,$DeleteStr) OR die("Hiba RE 21 ");
    $DeleteStr = "Delete FROM megrendelt_termek WHERE RAzon=$f1"; 
    mysqli_query($MySqliLink,$DeleteStr) OR die("Hiba RE 22 "); 
    $f1 ='';
    echo "<h1>A kijelölt megrendelést töröltük!</h1>";
  }
  // Töröljük a régi el nem küldött megrendeléseket
  // A 12 órával korábbiak mm azonosítóját egy tömbbe másoljuk, majd az azonosítóhoz tartozó megrendelés és kosárbejegyzéseket töröljük
  // A két lépés összevonható -> a while cikluson belül is megvalósíthatók a törlések
  $SelectStr = "SELECT mmAzon FROM megrendeles WHERE (DATE_ADD(RDatum,INTERVAL 12 HOUR) < NOW()) and (RStatus=0)"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 23 ");  
  while($row = mysqli_fetch_array($result)) {$ElavutTmb[] = $row['mmAzon'];} mysqli_free_result($result);  
  foreach ($ElavutTmb as $key => $value) {
    echo "<h1>$key : $value;</h1>";
    $DeleteStr = "Delete FROM megrendeles WHERE mmAzon='$value'"; 
    mysqli_query($MySqliLink,$DeleteStr) OR die("Hiba RE 24 "); 
    $DeleteStr = "Delete FROM kocsi WHERE mmAzon='$value'"; 
    mysqli_query($MySqliLink,$DeleteStr) OR die("Hiba RE 25 "); 
  }
}

//--------------------------------------------------------------------------------------------------------------
// A MEGRENDELÉSEK MEGJELENÍTÉSE
  if ($f1 > '')  {
    // Ha konkrét megrendelés adatait jelenítjük meg, akkor annak azonosítója $f1-ben érkezik
    $id = $f1;
    if ($hozzaferes>5) {$where = "WHERE id=$f1";} else {$where = "WHERE id=$f1 and Fnev='$mm_felhasznalo'";}
    //Az adminisztrátor minden megrendelést lát. Az átlag felhasználó csak a sajátját
    $SelectStr = "SELECT * FROM megrendeles $where";  
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 26 ");
    $rowDB     = mysqli_num_rows($result); 
    if ($rowDB>0) {
      $row  = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
      //Ha van, akkor betöltjük a megrendelés adatait
      $Fnev = $row['Fnev'];
      $id   = $row['id'];
      $Rszemnev = $row['Rszemnev'];
      $RDatum   = $row['RDatum'];
      $RStatus  = $row['RStatus'];  
    
      $Remail    = $row['Remail'];
      $Rtelszam1 = $row['Rtelszam1'];
      $Rtelszam2 = $row['Rtelszam2'];

      $Rorszag = $row['Rorszag'];
      $Rvaros  = $row['Rvaros'];
      $Rirszam = $row['Rirszam'];
      $Rcim    = $row['Rcim'];

      $SZorszag = $row['SZorszag'];
      $SZvaros  = $row['SZvaros'];
      $SZirszam = $row['SZirszam'];
      $SZcim    = $row['SZcim'];

      switch ($RStatus) {
             case "0":  $stat='Még nem küldte el';  break;
             case "1":  $stat='Elküldve';  break;
             case "2":  $stat='Olvasva'; break;
             case "3":  $stat='Egyeztetés megtörtént'; break;
             case "4":  $stat='Kiszállítva'; break;
      }

      $StatusForm  = "<h2>Rendelés státusza</h2><form action='#' method='post' id='form_RendelStatusUrlap'>\n";
      $StatusForm .= "<select name='ren_status'>";   
      if ($stat=='Még nem küldte el') {$StatusForm .= "<option selected>Még nem küldte el</option>";} else {$StatusForm .= "<option selected>Még nem küldte el</option>";}
      if ($stat=='Elküldve') {$StatusForm .= "<option selected>Elküldve</option>";} else {$StatusForm .= "<option> Elküldve </option>";}
      if ($stat=='Olvasva')  {$StatusForm .= "<option selected>Olvasva</option>";} else {$StatusForm .= "<option> Olvasva </option>";}
      if ($stat=='Egyeztetés megtörtént') {$StatusForm .= "<option selected>Egyeztetés megtörtént</option>";} else {$StatusForm .= "<option> Egyeztetés megtörtént </option>";}
      if ($stat=='Kiszállítva') {$StatusForm .= "<option selected>Kiszállítva</option>";} else {$StatusForm .= "<option> Kiszállítva </option>";}
      $StatusForm .= "<option> ---- </option>";
      if ($stat=='Törlés') {$StatusForm .= "<option selected>Törlés</option>";} else {$StatusForm .= "<option> Törlés </option>";}
      $StatusForm .= "</select>" ;
      $StatusForm .= "<input type='submit' name='submitRendelStatus' value='Módosít'>\n";
      $StatusForm .= "</form>" ;

      $Rkod =  "R".str_pad($id,5,"0",STR_PAD_LEFT);
      $ElerhetosegTbl = "<h2>Elérhetőség</h2><table><tr><th>Név</th><th>Email </th><th>Telefon 1 </th><th>Telefon 2 </th><th>Státusz </th></tr>";
      $ElerhetosegTbl.="<tr><td class='kozepre'>$Rszemnev</td><td class='kozepre'>$Remail</td><td class='kozepre'>$Rtelszam1</td><td class='kozepre'>$Rtelszam2</td><td class='kozepre'>$stat</td></tr>\n";       
      $ElerhetosegTbl .= "</table>";
      $Cim1Tbl .= "<h2>Számlázási cím</h2><table><tr><th>Ország</th><th>Irányítószám </th><th>Város </th><th>Cím</th></tr>";
      $Cim1Tbl .= "<tr><td class='kozepre'>$Rorszag</td><td class='kozepre'>$Rirszam</td><td  class='kozepre'>$Rvaros</td><td class='kozepre'>$Rcim</td></tr>\n";       
      $Cim1Tbl .= "</table>";
      $Cim1Tbl .= "<h2>Szállítási cím</h2><table><tr><th>Ország</th><th>Irányítószám </th><th>Város </th><th>Cím</th></tr>";
      $Cim1Tbl .= "<tr><td class='kozepre'>$SZorszag</td><td  class='kozepre'>$SZvaros</td><td  class='kozepre'>$SZirszam</td><td  class='kozepre'>$SZcim</td></tr>\n";       
      $Cim1Tbl .= "</table>";

     // A kosár tartalmának beolvasása
      $SelectStr = "SELECT * FROM  megrendelt_termek WHERE RAzon=$f1";
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 27 ");
      $i = 0;
      while($rowtermek = mysqli_fetch_array($result))
      {
        $RAzon = $rowtermek['RAzon'];
        $RTKod = $rowtermek['RTKod'];
        $RTNev = $rowtermek['RTNev'];
        $RTAr  = $rowtermek['RTAr'];
        $RTSzorzo   = $rowtermek['RTSzorzo'];
        $RTSzalKlts = $rowtermek['RTSzalKlts'];
        $RTSzallit  = $rowtermek['RTSzallit'];

        $Kedvezmeny = (1-$RTSzorzo) * 100;
        $AkciosAr   = $RTAr * $RTSzorzo; $AkciosAr = ceil($AkciosAr); $AkciosArKi = number_format($AkciosAr,0,",","."); $ArKi = number_format($RTAr,0,",",".");

        $TermekTbl .= "<tr><td>$RTKod</td><td>$RTNev</td><td  class='jobbra'>$ArKi Ft</td><td class='kozepre'>- $Kedvezmeny%</td><td class='jobbra'>$AkciosArKi Ft
                       </td><td class='kozepre'>$RTSzalKlts</td><td class='kozepre'>$RTSzallit nap</td></tr>\n"; 
      }
      $HTMLkod  = "<div id='DIVigazit'><div id='DIVmegrendeles'><h1>$Rkod megrenelés ($RDatum) </h1>"; 

      $HTMLkod .= $ElerhetosegTbl .$Cim1Tbl; 

      $HTMLkod .= "<h2>Rendelt termékek</h2><table>";
      $HTMLkod .= "<tr><th>Kód</th><th>Megnevezés </th><th>Ár</th><th>Kedv.</th><th>Akciós ár</th><th>Szállítási költség</th><th>Szállítási idő</th></tr>"; 
      $HTMLkod .= $TermekTbl;
      $HTMLkod .= "</table>";
      if ($hozzaferes>5) {$HTMLkod .= $StatusForm;}

      $HTMLkod .= "</div></div>"; 
      echo $HTMLkod;
    } else {
      //Az átlag felhasználó csak a saját megrendeléseit láthatja
      $HTMLkod = "<h1>A keresett megrendelés adatai nem elérhetők</h1>"; 
      echo $HTMLkod;
    }
  } else {
    //Ha $f1 üres, akkor a megrendelések listáját íratjuk ki
    echo "<h1> Megrendelések</h1>";
    if ($mm_felhasznalo>'') {
      // Csak a bejelentkezett felhasználók megtekinthetik a saját rendeléseiket
      if ($hozzaferes>5) {$where = "";} else {$where = "WHERE Fnev='$mm_felhasznalo'";} 
      // Az adminisztrátor ($hozzaferes>5) látja az összeset
      $SelectStr = "SELECT * FROM megrendeles $where";  
      $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba RE 28 ");
      $rowDB     = mysqli_num_rows($result); 
      if ($rowDB>0) {
        while($row = mysqli_fetch_array($result)) {
          //Ha van, akkor betöltjük a megrendelés adatait
          $Fnev = $row['Fnev'];
          $id   = $row['id'];
          $Rszemnev = $row['Rszemnev'];
          $RDatum   = $row['RDatum'];
          $RStatus  = $row['RStatus'];
          switch ($RStatus) {
             case "0":  $stat='Még nem küldte el';  break;
             case "1":  $stat='Elküldve';  break;
             case "2":  $stat='Olvasva'; break;
             case "3":  $stat='Egyeztetés megtörtént'; break;
             case "4":  $stat='Kiszállítva'; break;
          }
          $Rkod =  "R".str_pad($id,5,"0",STR_PAD_LEFT);;
          $MegrendelesTbl .= "<tr><td class='kozepre'> $Rszemnev </td><td>$Rkod</td><td> $RDatum </td><td> $stat </td><td>
          <a href='?f0=rendelesek&f1=$id'>Részletes adatok</b></td></tr>\n";
        } 
        mysqli_free_result($result);
        $MegrendelesTbl = "<table><tr><th>Név</th><th>Kód </th><th>Dátum </th><th>Státusz </th><th>Részletes adatok</th></tr>$MegrendelesTbl </table>";
      }
      $HTMLkod .= "<div id='DIVigazit'><div id='DIVmegrendelesLista'>";
      $HTMLkod .= $MegrendelesTbl;
      $HTMLkod .= "</div></div>";
    }
    echo $HTMLkod;
  }
}

?>
