<?php

//------------------------------------------------------------------------------------------------------------------
// AZ OLDAL TARTALMÁMAK MEGJELENÍTÉSE	
//------------------------------------------------------------------------------------------------------------------

function Kiir_Tartalom()
{
  global $AktOldal;
  global $Err_RegUrlap;
  global $Err_JelszModosit;
  $OURL = $AktOldal['OURL']; 
  $ErrorStr='';

// ----------  Speciális tartalom kiíratása  ----------------------------
  echo  "\n\n<div id='tartalom'>\n";
  switch ($OURL) {
    case "Kezdőlap":  ;
         break;
    case "regisztracio":  Kiir_RegUrlap($Err_RegUrlap); 
         break;
    case "jelszo_modositas":  Kiir_JelszoModosit($Err_JelszModosit); 
         break;
    case "szerkeszt":  require_once("oldalak/w3_szerkeszt.php");
         break;
    case "megrendel":  require_once("oldalak/w3_rendeles.php"); 
                       if ($_POST['RendelesLetrehoz'] == 'Tovább')  {$ErrorStr=Ment_Megrendeles();}
                       if ($_POST['RendelesVeglegesít'] == 'Tovább')  {
                           $ErrorStr=Veglegesit_Megrendeles();
                       }
                       if ($ErrorStr>'') { Kiir_MegrendelUrlap('letrehozas',$ErrorStr);
                       } else {
                         if ($_POST['MegrendelSubmit'] == 'Megrendel')  {Kiir_MegrendelUrlap('letrehozas',$ErrorStr);}   
                         if ($_POST['MegrendelSubmit'] == 'Vissza')  {Kiir_MegrendelUrlap('letrehozas',$ErrorStr);} 
                         if ($_POST['RendelesLetrehoz'] == 'Tovább')  {Kiir_MegrendelUrlap('ellenorzes',$ErrorStr); }
                       }   
         break;
    case "rendelesek":  require_once("oldalak/w3_rendeles.php"); Kiir_Megrendelesek();
         break;                                          

    case "Beállítás**":   // if ($hozzaferes > 5) { require_once("oldalak/mod_oldal.php");   }
         break;
    default: Kiir_Oldal();
  }
  echo  "</div>";
}

//------------------------------------------------------------------------------------------------------------------
// A KIÍRANDÓ OLDAL KIVÁLASZTÁSA	
//------------------------------------------------------------------------------------------------------------------

function Kiir_Oldal()
{
  global $AktOldal; 
  $OTipus = $AktOldal['OTipus']; 
  if ($_POST['KosarModositSubmit'] == 'Módosít')  {Modosit_kosar(); }
  if ($_POST['KosarbaSubmit'] == 'Kosárba')  {Modosit_TermekKosar(); }

  switch ($OTipus) {
    case OKezdolap:  Kiir_Kezdolap();
         break;
    case OKategoria:  Kiir_Kategoria();
         break;
    case OAlkategoria:  Kiir_AlKategoria();
         break;
    case OTermek:  Kiir_Termek();
         break;
    case OHirkategoria:  Kiir_Hirkategoria();
         break;
    case OHirOldal:  Kiir_HirOldal();
         break;
    case OOldalterkep:  Kiir_Oldalterkep();
         break;
    default: Kiir_HibaOldal();
  }
}


//------------------------------------------------------------------------------------------------------------------
// A KEZDŐLAP KIÍRATÁSA	
//------------------------------------------------------------------------------------------------------------------

function Kiir_Kezdolap()
{
  global $AktOldal,  $MySqliLink;
  $HTMLkod = '';
  $HTMLkosar =  Kiir_kosar();
  //Tartalom beolvasása a #-el határolt elemekre bontása
  //A tartalom 5 lépésben egymás után kerül megjelenítésre
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("Hiba T0 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
  $TartalomStr = karakter_csere_vissza($row['OTartalom']); 
  $TartalomTmb = explode('#', $TartalomStr);
  for ($i=1;$i<=5;$i++) {if ($TartalomTmb[$i]=='') {$TartalomTmb[$i]=$TartalomTmb[0];} }
  //Az oldalhoz tartozó képek betöltése
  $i = 0;
  $SelectStr = "SELECT * FROM kep WHERE Oid=".$AktOldal['id']." ORDER by KSorszam "; 
  $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("Hiba T00 ");
  while($row = mysqli_fetch_array($result))
  {
    $BKepURLTmb[$i] = $row['KURL']; $BKepNevTmb[$i] = $row['KNev']; $BKepLeirTmb[$i] = $row['KLeiras']; $i++;
  } mysqli_free_result($result);
  if ($BKepURLTmb[0] > '') {
    for ($j=$i;$j<5;$j++) {$BKepURLTmb[$j] = $BKepURLTmb[0]; $BKepLeirTmb[$i]=$BKepLeirTmb[0]; $BKepNevTmb[$i]=$BKepNevTmb[0];}
    for ($i=0;$i<5;$i++) {$BKepTmb[$i] = "<img src='kepek/".$BKepURLTmb[$i]."' alt='".$BKepLeirTmb[$i] ."' title='".$BKepNevTmb[$i]."' class='BKep' id='BKep$i' >";}
  } else {
    for ($i=0;$i<5;$i++) {$BKepTmb[$i] = '';}
  }
  $Tartalom ='';
  //A képek megjelenítése
  for ($i=0;$i<5;$i++) {
    $Tartalom .= "\n<div id='FoBannerBelso$i' class='FBB'>".$BKepTmb[$i].$TartalomTmb[$i]."</div>";
  }
  // A kosár megjelenítése (Csak akkor látszik, ha a felhasználó a kosár ikonra kattint.)
  if ($HTMLkosar>'') {$HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div><br><br>";} else { $HTMLkod .= "<div id='FoBanner'>$Tartalom </div>";}
  // Kategóriák adatainak betöltése és képes link formájában történő megjelenítése
  $SelectStr = "SELECT * FROM oldal WHERE OSzulo=".$AktOldal['id']." and OTipus=".OKategoria ; 
  $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("Hiba T1 ");
  while($rowkat = mysqli_fetch_array($result))
  {
   $HTMLkod .= "<div class='KepesMenu' title='".$rowkat['ORLeiras']."'>
                <a href='?f0=".$rowkat['OURL']."'><figure class='Linkfigure' style='background-image:url(kepek/".$rowkat['OKep'].");'></figure>
                 <p>".$rowkat['ONev']."</p>
                </a></div>";
  }
  mysqli_free_result($result);
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// A KATEGÓRIA KIÍRATÁSA	
//------------------------------------------------------------------------------------------------------------------

function Kiir_Kategoria()
{
  global $AktOldal,  $MySqliLink;
  $HTMLkod   ='';
  $HTMLkosar =  Kiir_kosar();
  if ($HTMLkosar>'') 
     {$HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div> <br class='jobbramegtor1' style='line-height:4px;'>";}
  $HTMLkod  .= "<h1>".$AktOldal['ONev']."</h1>";
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T2");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
  $HTMLTartalom = $row['OTartalom']."<br><br>"; 
  // A kijelölt kategória alkategóriáinak megjelenítése
  $SelectStr = "SELECT * FROM oldal WHERE OSzulo=".$AktOldal['id']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($rowAlkat = mysqli_fetch_array($result))
  {
   $HTMLAlkat .= "<div class='KepesMenu' title='".$rowAlkat['ORLeiras']."'> <a href='?f0=".$rowAlkat['OURL']."'>
                  <figure class='Linkfigure' style='background-image:url(kepek/".$rowAlkat['OKep'].");'></figure>
                 <p>".$rowAlkat['ONev']."</p> </a></div>";
  }
  mysqli_free_result($result);
  // További kategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=".OKategoria; 
  $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("Hiba T4");
  while($rowKat = mysqli_fetch_array($result))
  {
   $HTMLKat .= "<div class='KepesMenu' title='".$rowKat['ORLeiras']."'> <a href='?f0=".$rowAlkat['OURL']."'>
               <figure class='Linkfigure' style='background-image:url(kepek/".$rowKat['OKep'].");'></figure>
                 <p>".$rowKat['ONev']."</p>  </a></div>";
  }
  mysqli_free_result($result);
  $HTMLkod .= "<div id='TovKat'><h1>További kategóriák</h1> $HTMLKat </div>";
  $HTMLkod .= $HTMLAlkat; 
  $HTMLkod .= "<br><br>".$HTMLTartalom; 
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// A ALKATEGÓRIA KIÍRATÁSA	
//------------------------------------------------------------------------------------------------------------------

function Kiir_AlKategoria()
{
  global $AktOldal,  $MySqliLink;
  $HTMLkod = '';
  $HTMLkosar =  Kiir_kosar();
  if ($HTMLkosar>'') {$HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div> <br class='jobbramegtor1' style='line-height:4px;'>";}
  $HTMLkod .= "<h1>".$AktOldal['ONev']."</h1>";

  // Az alkategória tartalma
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T5");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
  $HTMLTartalom .= $row['OTartalom']; 

  // Az alkategória elemei
  $SelectStr = "SELECT * FROM oldal WHERE OSzulo=".$AktOldal['id']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T6");
  while($rowAlkat = mysqli_fetch_array($result))
  {
   $HTMLAlkat .= "<div class='KepesMenu' title='".$rowAlkat['ORLeiras']."'>
                <a href='?f0=".$rowAlkat['OURL']."'><figure class='Linkfigure' style='background-image:url(kepek/".$rowAlkat['OKep'].");'></figure>
                 <p>".$rowAlkat['ONev']."</p>
                </a></div>";
  }
  mysqli_free_result($result);

// Kiemelt ajánlat
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=3 and OPrioritas=100";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T7");  
  $i = 1;
  while($row = mysqli_fetch_array($result))
  {
    if ($i<5) {
      if ($row['ONev']!=$AktOldal['ONev']) { $i++;
       $KiemeltStr .= "<div class='KepesMenu' title='".$row['ORLeiras']."'>
                <a href='?f0=".$row['OURL']."'>
                 <figure class='Linkfigure' style='background-image:url(kepek/".$row['OKep'].");'></figure>
                 <p>".$row['ONev']."</p>
                </a></div>";
      }
    }
  }   
   $HTMLkod .= "<div id='KiemeltTermek2'><h1>Kiemelt termékek</h1>$KiemeltStr</div>";

   $HTMLkod .=  $HTMLAlkat;
   $HTMLkod .=  "<br><br>".$HTMLTartalom;
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// A KOSÁR TARTALMÁNAK MÓDOSÍTÁSA	
//------------------------------------------------------------------------------------------------------------------

function Modosit_TermekKosar()
{
  global $AktOldal,  $MySqliLink, $mm_azon;

  // Kosár tartalmának módisítása
  if ($_POST['KosarbaSubmit'] > '')  { 
    $TermekDB = tiszta_int($_POST['TermekDB']); 
    // A termékoldalhoz tartozó termékkódok lekérdezése
    $SelectStr = "SELECT * FROM termek WHERE Oid=".$AktOldal['id']." ORDER BY TKod"; 
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T8");  
    $i = 0;
    while($rowtermek = mysqli_fetch_array($result))
    {
      $TermekKod[$i] = $rowtermek['TKod']; 
      $i++; 
    }
    mysqli_free_result($result);  

    for ($j=0;$j<=$TermekDB;$j++) { 
      $TermekDBStr = "TermekDB$j"; 
      $DB = tiszta_int($_POST[$TermekDBStr]);
      $SelectStr = "SELECT * FROM kocsi WHERE TKod='".$TermekKod[$j]."' and mmAzon='$mm_azon'";
      $result    = mysqli_query($MySqliLink,$SelectStr);
      $rowDB = mysqli_num_rows($result);
      if ($_POST[$TermekDBStr] > 0)  { 
        if ($rowDB>0) {
           // Van már ilyen sor
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            $UpdateStr = "UPDATE kocsi SET DB=$DB WHERE TKod='".$TermekKod[$j]."' and mmAzon='$mm_azon'";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo "Hiba T9";}
        } 
          else {
          mysqli_free_result($result);
          $InsertIntoStr = "INSERT INTO kocsi VALUES ('','$mm_azon','".$TermekKod[$j]."',$DB, NOW())";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo "Hiba T10";}
        }
     } else {
        if ($rowDB>0) {
          mysqli_free_result($result);
          $SelectStr = "Delete FROM kocsi WHERE TKod='".$TermekKod[$j]."' and mmAzon='$mm_azon'";
          if (!mysqli_query($MySqliLink,$SelectStr)) {echo "Hiba T11";}
        }
      }
    }
  }
}


//------------------------------------------------------------------------------------------------------------------
// A TERMÉKOLDAL MEGJELENÍTÉSE	
//------------------------------------------------------------------------------------------------------------------
function Kiir_Termek()
{
  global $AktOldal,  $MySqliLink, $mm_azon;

  $HTMLkod ='';

  $HTMLH1 = "<h1 style='clear:right;'>".$AktOldal['ONev']."</h1>";

  $HTMLkosar =  Kiir_kosar();

  $SelectStr = "SELECT * FROM kep WHERE Oid=".$AktOldal['id']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T12");  
  $i = 1;
  while($rowkep = mysqli_fetch_array($result))
  {
  $HTMLNagykep .= "<div class='NagykepClass' id='DIVNagykep$i' style='background-image:url(kepek/".$rowkep['KURL'].");'> 
                    </div>\n";
    $checked = '';
    if ($i == 1)  {$checked = 'checked';}
    if ($_POST['NagykepValaszt'] == "chNagykepValaszt$i") {$checked = 'checked';}
    $HTMLkiskepek .= "<input type='radio' name='NagykepValaszt' class='NagykepValaszt' id='chNagykepValaszt$i' value='chNagykepValaszt$i' $checked>
                    <label for='chNagykepValaszt$i' class='LabelKiskep' id='LabelKiskep$i'> 
                    <img src='kepek/".$rowkep['KURL']."' alt='".$rowkep['KNev']."' class='Kiskep' ></label>\n";
    $i++; 
  }
  mysqli_free_result($result);
  $HTMLKepek = "<div id='TermekKepek'>\n";
  $HTMLKepek .= $HTMLkiskepek;
  $HTMLKepek .= $HTMLNagykep;
  $HTMLKepek .= "</div>\n";

  // oldal_tartalom tábla lekérdezése
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T13"); 
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $HTMLTartalom = $row['OTartalom']."<br>";
  $HTMLRovidLeir .= "<div id='RovidLeir'>".$AktOldal['ORLeiras']."</div><br>";


  // termékek betöltése tömbbe
  $SelectStr = "SELECT * FROM termek WHERE Oid=".$AktOldal['id']." ORDER BY TKod"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T14"); 
  $i = 0;
  while($rowtermek = mysqli_fetch_array($result))
  {
    $TermekArr[$i]['TKod'] = $rowtermek['TKod'];
    $TermekArr[$i]['TAr'] = $rowtermek['TAr'];
    $TermekArr[$i]['TSzorzo'] = $rowtermek['TSzorzo'];
    $TermekArr[$i]['TtulErt'] = $rowtermek['TtulErt'];
    $TermekArr[$i]['TSzalKlts'] = $rowtermek['TSzalKlts'];
    $TermekArr[$i]['TSzallit'] = $rowtermek['TSzallit'];
    $i++; 
  }
  mysqli_free_result($result);

  $TermekDB = $i-1;
  // kocsi tábla lekérdezése
  for ($j=0;$j<=$TermekDB;$j++) {
    $SelectStr = "SELECT * FROM kocsi WHERE TKod='".$TermekArr[$j]['TKod']."' and mmAzon='$mm_azon'";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T15"); 
    $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    if ($row['DB'] > 0) {$TermekArr[$j]['DB'] = $row['DB'];}
  }

  // Terméklista összeállítása
  $TermekTbl  = "";
  $TermekTbl .= "<table ><thead>";
  $TermekTbl .= "<tr><th class='th1'> Kód </th><th class='th1'> Jellemző </th><th class='th1'> Ár </th><th class='th1'> </th><th class='th1'> Akciós ár 
                 </th><th> <input type='submit' name='KosarbaSubmit' value='Kosárba' id='KosarbaSubmit'></th></tr> </thead><tbody>";
  for ($j=0;$j<=$TermekDB;$j++) {
   $Kedvezmeny= (1-$TermekArr[$j]['TSzorzo']) * 100;
   $AkciosAr= $TermekArr[$j]['TAr'] * $TermekArr[$j]['TSzorzo']; $AkciosArKi = ceil($AkciosAr);
   $TermekTbl .= "<tr><td title='Kód'> ".$TermekArr[$j]['TKod']." </td><td title='Jellemző'> ".$TermekArr[$j]['TtulErt']." 
     </td><td class='jobbra' title='Ár'> ".ceil($TermekArr[$j]['TAr'])." Ft </td><td class='kozepre' title='Kedvezmény'><b> -".$Kedvezmeny."% </b>
     </td><td class='jobbra' title='Akciós ár'> ".$AkciosArKi." Ft 
     </td><td class='kozepre' title='Darab'> <input type='number' name='TermekDB$j' min='0' max='1000' step='' value='".$TermekArr[$j]['DB']."'> 
     </td></tr>";
  }
  $TermekTbl .= "</tbody></table>";

  // Szállítási adatok tábla összeállítása
  $SzallitTbl = "<table >";
  $SzallitTbl .= "<thead><tr><th> Kód </th><th> Szállítási idő</th><th>Szállítási költség </th></tr></thead><tbody>";
  for ($j=0;$j<=$TermekDB;$j++) {
   $SzallitTbl .= "<tr><td> ".$TermekArr[$j]['TKod']." </td><td class='kozepre'> ".$TermekArr[$j]['TSzallit']." nap 
   </td><td> ".$TermekArr[$j]['TSzalKlts']." </td></tr>\n";
  }
  $SzallitTbl .= "<tbody></table>";

  //Jellemzők tábla összeállítása
  $SelectStr = "SELECT * FROM termek_jellemzo WHERE Oid=".$AktOldal['id']." ORDER BY JSorszam";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T16");
  $i = 0;
  $JellemzokTbl = '';
  while($rowJellemzo = mysqli_fetch_array($result))
  {
   $JellemzokTbl .= "<tr><td>". $rowJellemzo['JNev']." </td><td> ".$rowJellemzo['JErtek']."</td></tr> ";
  } mysqli_free_result($result);
  if ($JellemzokTbl>'') {
    $JellemzokTbl = "<table> $JellemzokTbl </table>";
  }

  //Leírás div összeállítása
    $SelectStr = "SELECT * FROM termek_leiras WHERE Oid=".$AktOldal['id']." LIMIT 1";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T17"); 
    $rowLeiras   = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $TermekLeiras = $rowLeiras['TLeiras']; 


  $TermekinfoFej = '';
  if (!($_POST['PanelValaszt'] > ''))  {$PanelValaszt = 'chJellemzoPanel';}
  if ($_POST['PanelValaszt'] == 'chSzallitasPanel')  {$PanelValaszt = 'chSzallitasPanel';}
  if ($_POST['PanelValaszt'] == 'chJellemzoPanel')  {$PanelValaszt = 'chJellemzoPanel';}
  if ($_POST['PanelValaszt'] == 'chLeirasPanel')  {$PanelValaszt = 'chLeirasPanel';}

  if($PanelValaszt == 'chJellemzoPanel') 
    {$TermekinfoFej .= "<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chJellemzoPanel' 
                       value='chJellemzoPanel' checked>";}
    else {$TermekinfoFej .="<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chJellemzoPanel'
                       value='chJellemzoPanel'>";}
  $TermekinfoFej .= "<label class='PanelValasztLabel' for='chJellemzoPanel'>Jellemzők</label>";
  if($PanelValaszt == 'chSzallitasPanel') 
    {$TermekinfoFej .= "<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chSzallitasPanel' 
                       value='chSzallitasPanel' checked>";}
    else {$TermekinfoFej .="<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chSzallitasPanel'
                       value='chSzallitasPanel'>";}
  $TermekinfoFej .= "<label class='PanelValasztLabel' for='chSzallitasPanel'>Szállítás</label>";

  if($PanelValaszt == 'chLeirasPanel') 
    {$TermekinfoFej .= "<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chLeirasPanel' 
                       value='chLeirasPanel' checked>";}
    else {$TermekinfoFej .="<input type='radio' name='PanelValaszt' class='PanelValaszt' id='chLeirasPanel'
                       value='chLeirasPanel'>";}
  $TermekinfoFej .= "<label class='PanelValasztLabel' for='chLeirasPanel'>Leírás</label>";


// Hasonló termékek
  $SelectStr = "SELECT * FROM oldal WHERE OSzulo=".$AktOldal['OSzulo']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T18");
  $i = 1;
  while($row = mysqli_fetch_array($result))
  {
    if ($row['ONev']!=$AktOldal['ONev']) { 
     $HTermekStr .= "<div class='KepesMenu' title='".$row['ORLeiras']."'>
                <a href='?f0=".$row['OURL']."'><figure class='Linkfigure' style='background-image:url(kepek/".$row['OKep'].");'></figure>
                 <p>".$row['ONev']."</p>
                </a></div>";

    }
  }
// Kiemelt ajánlat
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=3 and OPrioritas=100";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T19"); 
  $i = 1;
  while($row = mysqli_fetch_array($result))
  {
    if ($i<5) {
      if ($row['ONev']!=$AktOldal['ONev']) { $i++;
       $KiemeltStr .= "<div class='KepesMenu' title='".$row['ORLeiras']."'>
                <a href='?f0=".$row['OURL']."'>
                 <figure class='Linkfigure' style='background-image:url(kepek/".$row['OKep'].");'></figure>
                 <p>".$row['ONev']."</p>
                </a></div>";
      }
    }
  }

  $HTMLkod .= $HTMLH1;
 // $HTMLkod .= $HTMLRovidLeir."";
  $HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div>";

  $HTMLkod .= $HTMLKepek;
  $HTMLkod .= "<br class='jobbramegtor1' style='line-height:4px;'><div id='KiemeltTermek'><h1>Kiemelt termékek</h1>$KiemeltStr</div>";

  $HTMLkod .= "<div id='DIVTermekinfo'>\n";
  $HTMLkod .= $TermekinfoFej."<br>";
  $HTMLkod .= "<div id='SzallitasPanel'>$SzallitTbl</div>";
  $HTMLkod .= "<div id='JellemzokPanel'>$JellemzokTbl</div>";
  $HTMLkod .= "<div id='LeirasPanel'>$TermekLeiras</div>";
  $HTMLkod .= "</div><br class='SorTores'>\n";

  $HTMLkod .= "<div id='DIVTLista'><form action='#' method='post' id='form_Tkocsi'>\n";
  $HTMLkod .= "<input type='hidden' name='TermekDB' value='$TermekDB'>\n";
  $HTMLkod .= $TermekTbl;
  $HTMLkod .= "</form></div>\n";
  $HTMLkod .= "<br class='SorTores1'>\n".$HTMLTartalom;
  $HTMLkod .= "<div id='KiemeltTermek1'><h1>Kiemelt termékek</h1>$KiemeltStr</div>";
  $HTMLkod .= "<div id='HTermek'><h1>Hasonló termékek</h1>$HTermekStr</div>"; 
  echo $HTMLkod;
}


//------------------------------------------------------------------------------------------------------------------
// A KOSÁR TARTALMÁMAK MÓDOSÍTÁSA	
//------------------------------------------------------------------------------------------------------------------
function Modosit_kosar()
{
  global $AktOldal,  $MySqliLink, $mm_azon;

  if ($_POST['KosarModositSubmit'] > '')  { 
    $TermekDB = tiszta_int($_POST['SorDB']);
    for ($j=0;$j<=$TermekDB;$j++) {
      $TKodStr = "RTermekKod$j";
      $TKod = tiszta_szov($_POST[$TKodStr]);
      $TDBStr = "RTermekDB$j";
      $TDB = tiszta_szov($_POST[$TDBStr]);
      if ($TDB > 0) {
            $UpdateStr = "UPDATE kocsi SET DB=$TDB WHERE TKod='$TKod' and mmAzon='$mm_azon'";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo "Hiba T20 ";}
      } else {
          $SelectStr = "Delete FROM kocsi WHERE TKod='".$TKod."' and mmAzon='$mm_azon'";
          if (!mysqli_query($MySqliLink,$SelectStr)) {echo "Hiba T21 ";}
      }
    }
  }
}

//------------------------------------------------------------------------------------------------------------------
// A KOSÁR TARTALMÁNAK KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------

function Kiir_kosar()
{
  global $AktOldal,  $MySqliLink, $mm_azon, $f0, $f1, $f2, $f3, $f4;
  // Beolvassuk a kocsi tábla adott munkamenethez tartozó rekordjait
  // A termékkódok és darabszámok a TermekArr tömbbe kerülnek
  $SelectStr = "SELECT * FROM  kocsi WHERE mmAzon='$mm_azon'";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T22 ");  
  $i = 0;
  while($rowtermek = mysqli_fetch_array($result))
  {
    $TermekArr[$i]['TKod'] = $rowtermek['TKod'];
    $TermekArr[$i]['DB'] = $rowtermek['DB'];
    $i++;
  } mysqli_free_result($result);

if ($i>0) {
  $SorDB = $i-1;
  // A TermekArr tömbben lévő termékek többi adatait is lekérdezzük
  for ($j=0;$j<=$SorDB;$j++) {
    $SelectStr = "SELECT * FROM termek WHERE TKod='".$TermekArr[$j]['TKod']."'";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T23 "); 
    $rowtermek       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $TermekArr[$j]['Oid'] = $rowtermek['Oid'];
    $TermekArr[$j]['TAr'] = $rowtermek['TAr'];
    $TermekArr[$j]['TSzorzo'] = $rowtermek['TSzorzo'];
    $TermekArr[$j]['TtulErt'] = $rowtermek['TtulErt'];
    $TermekArr[$j]['TSzallit'] = $rowtermek['TSzallit'];

    $SelectStr = "SELECT ONev FROM oldal WHERE id='".$TermekArr[$j]['Oid']."'";
    $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T24 ");   
    $rowtermek       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
    $TermekArr[$j]['ONev'] = $rowtermek['ONev'];
  }
  $Osszesen = 0;
  // Az űrlap fejének összeállítása
  $HTMLkod .= "<form action='?f0=megrendel' method='post' id='form_kocsiLista'>\n";

    $HTMLkod .= "<input type='checkbox' name='chkosar' id='chkosar' value='chkosar'>
     <label for='chkosar'><img src='kepek/ikonok/kosarikon28p.png' alt='Kosár' title='Kosár' > </label><br>";

  $HTMLkod .= "<div id='kosarMutat'>";
  $HTMLkod .= "<table><caption>Az Ön kosarának tartalma: </caption>\n";
  $HTMLkod .= "<thead><tr><th> Termék </th><th> Kód </th><th> Tulajdonság </th><th> Ár </th><th> Kedv.
                 </th><th> Akciós ár </th><th> Szállítás </th><th> db </th><th> Összesen </th></tr></thead><tbody>\n";
  // A termékeket tartalmazó sorok összeállítása
  for ($j=0;$j<=$SorDB;$j++) {
    $Kedvezmeny= (1-$TermekArr[$j]['TSzorzo']) * 100;
    $AkciosAr= $TermekArr[$j]['TAr'] * $TermekArr[$j]['TSzorzo']; $AkciosArKi = ceil($AkciosAr);
    $SorOsszesen = $AkciosArKi * $TermekArr[$j]['DB'];
    $Osszesen = $Osszesen + $SorOsszesen;
    $HTMLkod .= "
     <tr><td title='Termék'> ".$TermekArr[$j]['ONev']." 
     </td><td title='Kód'> ".$TermekArr[$j]['TKod']." 
     </td><td title='Tulajdonság'> ".$TermekArr[$j]['TtulErt']." 
     </td><td class='jobbra'  title='Ár'> ".ceil($TermekArr[$j]['TAr'])." Ft 
     </td><td class='kozepre'  title='Kedv.'><b> -".$Kedvezmeny."% </b>
     </td><td class='jobbra'  title='Akciós ár'> ".$AkciosArKi." Ft 
     </td><td class='jobbra'  title='Sz.idő'> ".$TermekArr[$j]['TSzallit']." nap
     </td><td class='kozepre'  title='db'> <input type='number' name='RTermekDB$j' min='0' max='1000' step='' value='".$TermekArr[$j]['DB']."'> 
     </td><td class='jobbra'  title='Összesen'> ".$SorOsszesen." Ft
     </td></tr>"; 
     $HTMLkocsiPL .= "<input type='hidden' name='RTermekKod$j' value='".$TermekArr[$j]['TKod']."'>\n";
  }
  $HTMLkod .= '</tbody></table>';
  // A termékek darabszámát és a $_GET tömb fontosabb elemeit is elküldjük 
  $HTMLkod .= "<input type='hidden' name='SorDB' value='$SorDB'>\n";
  $HTMLkod .= $HTMLkocsiPL ;

  $OURL = $AktOldal['OURL'];
  $HTMLkod .= "<input type='hidden' name='f0' value='$OURL'>\n";
  $HTMLkod .= "<input type='hidden' name='f1' value='$f1'>\n";
  $HTMLkod .= "<input type='hidden' name='f2' value='$f2'>\n";
  $HTMLkod .= "<input type='hidden' name='f3' value='$f3'>\n";

  $HTMLkod .= "<br class='jobbramegtor' style='line-height:4px;'>
               <input style='clear:left;' type='submit' name='KosarModositSubmit' formaction='#' value='Módosít' >\n";
  $HTMLkod .= "<input type='submit' name='MegrendelSubmit' value='Megrendel' >\n";

  $HTMLkod .= "<div id='osszesen' style='float:right;'><strong>Összesen: $Osszesen Ft</strong></div>\n";
  $HTMLkod .= "</div></form>";
} else {
  $HTMLkod = '';
}

 return $HTMLkod;
}


//------------------------------------------------------------------------------------------------------------------
// HÍRKATEGÓRIA KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------

function Kiir_Hirkategoria()
{

 global $AktOldal,  $MySqliLink;

  $HTMLkod = '';
  $HTMLkosar =  Kiir_kosar();
  if ($HTMLkosar>'') {$HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div>";}
  $HTMLkod .= "<h1>".$AktOldal['ONev']."</h1>";
  // Az alkategória tartalma
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("Hiba T25 "); 
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
  $HTMLTartalom .= $row['OTartalom']; 

    $HTMLTartalom  = karakter_csere_vissza($HTMLTartalom);
    $arr = array( "#1" => "", "#2" => "", "#3" => "", "#4" => "", "#5" => "", "##" => "");  
    $HTMLTartalom  = strtr($HTMLTartalom ,$arr);


  // A hírkategória elemei
  $SelectStr = "SELECT * FROM oldal WHERE OSzulo=".$AktOldal['id']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T26 ");
  while($rowElozetes = mysqli_fetch_array($result))
  {
    $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$rowElozetes['id']; 
    $resultTartalom = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T27 ");
    $rowTartalom    = mysqli_fetch_array($resultTartalom , MYSQLI_ASSOC);mysqli_free_result($resultTartalom );
    $HTMLETartalom  = $rowTartalom['OTartalom']; 

    $HTMLETartalom  = karakter_csere_vissza($HTMLETartalom);
    $HTMLETartalom  = substr($HTMLETartalom,0,strpos($HTMLETartalom,"##"));
    $arr = array( "#1" => "", "#2" => "", "#3" => "", "#4" => "", "#5" => "", "##" => "");  
    $HTMLETartalom  = strtr($HTMLETartalom ,$arr);

    $HTMLElozetes .= "<div class='HirElozetes'><div class='HirLink' title='".$rowElozetes['ORLeiras']."'>";
    $HTMLElozetes .= "<a href='?f0=".$rowElozetes['OURL']."'>";
    if ($rowElozetes['OKep']>'') 
      {$HTMLElozetes .= "<img src='kepek/".$rowElozetes['OKep']."' alt='".$rowElozetes['ORLeiras']."' class='ElozetesImg' />";}
    $HTMLElozetes .= "<h2>".$rowElozetes['ONev']."</h2>";
    $HTMLElozetes .= "</a></div>";
    $HTMLElozetes .= "$HTMLETartalom ";
    $HTMLElozetes .= "<a href='?f0=".$rowElozetes['OURL']."' class='hirTovabb'> ".$rowElozetes['ONev']." részletesen </a><br>";
    $HTMLElozetes .= "</div>";
  }
  mysqli_free_result($result);

  $HTMLkod .= "<div id='Elozetesek'> $HTMLElozetes</div>";
  $HTMLkod .=  "<br style='clear:left;'><br>".$HTMLTartalom;
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// HÍROLDAL KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------

function Kiir_HirOldal()
{
  global $AktOldal,  $MySqliLink;
  $HTMLkod   = '';
  $HTMLkosar =  Kiir_kosar();
  if ($HTMLkosar>'') {$HTMLkod .= "<div id='KosarKulso'>$HTMLkosar</div>";}
  $HTMLkod  .= "<h1>".$AktOldal['ONev']."</h1>";
  // Az alkategória tartalma
  $SelectStr = "SELECT * FROM oldal_tartalom WHERE Oid=".$AktOldal['id']." LIMIT 1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T28 ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
  $HTMLTartalom .= $row['OTartalom']; 

  $SelectStr = "SELECT * FROM kep WHERE Oid=".$AktOldal['id']; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T29 ");
  $i = 1;
  while($rowkep = mysqli_fetch_array($result))
  {
  $HTMLHirKepTMB[] = "<figure class='HirKepFigure'> <img src='kepek/".$rowkep['KURL']."' alt='".$rowkep['KNev']."'  />
                          <figcaption>  ".$rowkep['KNev']."    </figcaption>
                    </figure> \n";
    $i++; 
  }
  mysqli_free_result($result);
  for ($j=$i;$j==5;$j++) {$HTMLHirKepTMB[]='';}
  $HTMLTartalom  = karakter_csere_vissza($HTMLTartalom);
  $arr = array( "#1" => "$HTMLHirKepTMB[0]", "#2" => "$HTMLHirKepTMB[1]", "#3" => "$HTMLHirKepTMB[2]", "#4" => "$HTMLHirKepTMB[3]", "#5" => "$HTMLHirKepTMB[4]", "##" => "");  
  $HTMLTartalom  = strtr($HTMLTartalom ,$arr);
   foreach ($HTMLHirKepTMB as  $value) {$HTMLKepek .= $value."\n";}

  $HTMLkod .=  "<div id='HirT'>$HTMLTartalom</div>";
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// HIBAOLDAL KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------
function Kiir_HibaOldal()
{
  $HTMLkod = '';
  global $AktOldal,  $MySqliLink, $f1, $f3, $f3;
  $SelectStr = "SELECT * FROM oldal WHERE  OTipus=".OKategoria ; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T31 ");
  while($rowkat = mysqli_fetch_array($result))
  {
   $HTMLkod .= "<div class='KepesMenu' title='".$rowkat['ORLeiras']."'>
                <a href='?f0=".$rowkat['OURL']."'><figure class='Linkfigure' style='background-image:url(kepek/".$rowkat['OKep'].");'></figure>
                 <p>".$rowkat['ONev']."</p>
                </a></div>";
  }
  mysqli_free_result($result);

echo "<h1> feladat: $feladat</h1>";
echo "<h1> f1: $f1</h1>";
echo "<h1> f2: $f2</h1>";
echo "<h1> f3: $f3</h1>";
echo "<h1> OTipus: ".$AktOldal['OTipus']."</h1>";


echo "<div id='HiabOldalT'><h1>Hibaoldal</h1>
 A webhely nem létező oldalát próbálta megnyitni. <br>
Ez úgy fordulhatott elő, hogy 
<ul>
<li>az aloldal megszűnt vagy</li>
<li>hibás linkre kattintott vagy</li>
<li>hibásan írta be az oldal címét.</li>
</ul> 
<strong>A webáruház kategóriái közül itt választhat. Az egyéb szöveges tartalmak eléréséhez a menü nyújt segítséget.</strong>
<h2>Kategóriák </h2></div>
$HTMLkod ";
}


//------------------------------------------------------------------------------------------------------------------
// OLDALTÉRKÉP KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------
function Kiir_Oldalterkep()
{
global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  $Li ='';

$HTMLkod .= "<h1>Oldaltérkép</h1>";

// Speciális menüpontok megjelenítése
  $HTMLkod .= "<ul class='Szint1'>";
  $HTMLkod .= "<li class='OT1'><a href='./'> Kezdőlap</a></li>\n";

  // Kiemelt hírkategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=10 and OPrioritas>99 ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T32 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $HTMLkod .= "<li class='OT1'><a href='?f0=$OURL'>  $ONev</a>";
    $HTMLkod .= Oldalterkep_Szint2($OID,11);
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);

  // Kategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=1 ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T33 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $HTMLkod .= "<li class='OT1'><a href='?f0=$OURL'>  $ONev</a>";
    $HTMLkod .= Oldalterkep_Szint2($OID,2);
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);

  // Általános  hírkategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=10 and OPrioritas<100 ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T34 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $HTMLkod .= "<li class='OT1'><a href='?f0=$OURL'>  $ONev</a>";
    $HTMLkod .= Oldalterkep_Szint2($OID,11);
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);

  // Egyéb oldalak
  $SelectStr = "SELECT * FROM oldal WHERE OTipus>49 ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T35 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $HTMLkod .= "<li class='OT1'><a href='?f0=$OURL'>  $ONev</a>";
    $HTMLkod .= Oldalterkep_Szint2($OID,11);
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);

  $HTMLkod .=   "</ul>";
  echo "<div id='DIVoldalterkep'>$HTMLkod</div>";
}

//------------------------------------------------------------------------------------------------------------------
// Az első színtű gyermekoldalak linkjeinek megjelenítése
// Ha nincs ilyen, akkor üres karakterlánccal tér vissza 

function Oldalterkep_Szint2($SZOID,$SZTIP)
{
global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  $Li ='';
  $kSZTIP = $SZTIP + 1;
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=$SZTIP and OSzulo=$SZOID ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T36 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $Li .= "<li class='OT2'><a href='?f0=$OURL'>  $ONev</a>";
    $Li .= Oldalterkep_Szint3($OID,$kSZTIP);
    $Li .= "</li>\n"; 
  }
  if ($Li>'') {$HTMLkod = "<ul class='Szint2'>$Li</ul>"; } else {$HTMLkod ="";}
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// A második színtű gyermekoldalak linkjeinek megjelenítése
// Ha nincs ilyen, akkor üres karakterlánccal tér vissza 

function Oldalterkep_Szint3($SZOID,$SZTIP)
{
global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  $Li ='';
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=$SZTIP and OSzulo=$SZOID ORDER BY OPrioritas"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T37 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    $Li .= "<li class='OT3'><a href='?f0=$OURL'>  $ONev</a>";
    $Li .= "</li>\n"; 
  }
  if ($Li>'') {$HTMLkod = "<ul class='Szint3'>$Li</ul>"; } else {$HTMLkod ="";}
  return $HTMLkod;
}

?>
