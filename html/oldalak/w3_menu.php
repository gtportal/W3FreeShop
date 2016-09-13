<?php
    
//------------------------------------------------------------------------------------------------------------------
// A MENÜ KIÍRATÁSA
//------------------------------------------------------------------------------------------------------------------
// A függvény feladatai:
// 1. megjeleníti a kiímelt hírkategóriák és aloldalaik linkjeit 
// 2. megjeleníti a be- vagy kijelentkező űrlapot
// 3. megjeleníti a felhasználókezeléshez, megrendelések kezeléséhez és a szekesztéshez kapcsolódó oldalak linkjeit 
// 4. megjeleníti a termékategóriák és aloldalaik (alkategóriák, termékek) linkjeit
// 5. megjeleníti az oldaltérkép linkjét

function Kiir_Menu()
{
 global $mm_felhasznalo, $MySqliLink, $AktOldal, $hozzaferes, $Bejelentkezes_UZ; 

  $HTMLkod   =  "\n\n<div id='Div_Memu'>\n"; 
  $HTMLkod  .=   "<nav>\n<ul class='Ul1'>\n";
  // Kiemelt hírkategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=10 and OPrioritas>99 ORDER BY OPrioritas DESC"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba ME 01 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id']; $OSzulo = $row['OSzulo']; 
    //Ha az adott oldal vagy annak első gyermeke aktív, akkor az 'AktLink' osztályba kerül
    if ($OID==$AktOldal['id'] or $OID==$AktOldal['OSzulo'] ) {$AktLink = "class='AktLink'";} else {$AktLink = "";}
    $HTMLkod .= "<li class='M1'><a href='?f0=$OURL' $AktLink>  $ONev</a>";
    if ($AktLink>'') {$HTMLkod .= Menu_Szint2($OID,11);}
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);  

  // Be vagy kijelentkező űrlap megjelenítése
  if ($mm_felhasznalo > '') {$HTMLkod .= Kiir_Kijelentkezes();} else {$HTMLkod .= Kiir_Bejelentkezes($Bejelentkezes_UZ); }
  // Felhasználófüggő oldalak
  if ($mm_felhasznalo > '') {
    $HTMLkod .= "<li class='Mf'><a href='?f0=regisztracio'>Adatok módosítása</a></li>\n";
    $HTMLkod .= "<li class='Mf'> <a href='?f0=jelszo_modositas'>Jelszó módosítása</a></li>\n";
  } else {
    $HTMLkod .= "<li class='Mf'><a href='?f0=regisztracio'>Regisztráció</a></li>\n";
  }
  if ($hozzaferes > 4) {$HTMLkod .= "<li class='Mf'><a href='?f0=rendelesek'>Megrendelések</a></li>\n";}
  if ($hozzaferes > 5) {$HTMLkod .= "<li class='Mf'><a href='?f0=szerkeszt'> Szerkesztés</a></li>\n";}

  // Kategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=1 ORDER BY OPrioritas DESC"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba ME 02 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id']; $OSzulo = $row['OSzulo']; 
    //Ha az adott oldal vagy annak 1. ill 2. gyermeke aktív, akkor az 'AktLink' osztályba kerül
    if ($OID==$AktOldal['id'] or $OID==$AktOldal['OSzulo'] or $OID==$AktOldal['OSZSzulo'] ) 
       {$AktLink = "class='AktLink'";} else {$AktLink = "";}
    $HTMLkod .= "<li class='M1'><a href='?f0=$OURL' $AktLink>  $ONev</a>";
    if ($AktLink>'') {$HTMLkod .= Menu_Szint2($OID,2);}
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result);

  $HTMLkod .=   "<hr style='clear:left;'>";

  // Egyéb hírkategóriák
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=10 and OPrioritas<100 ORDER BY OPrioritas DESC"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba ME 03 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id']; $OSzulo = $row['OSzulo']; 
    if ($OID==$AktOldal['id'] or $OID==$AktOldal['OSzulo'] ) {$AktLink = "class='AktLink'";} else {$AktLink = "";}
    $HTMLkod .= "<li class='M1'><a href='?f0=$OURL' $AktLink>  $ONev</a>";
    if ($AktLink>'') {$HTMLkod .= Menu_Szint2($OID,11);}
    $HTMLkod .= "</li>\n"; 
  } 
  mysqli_free_result($result); 

  $HTMLkod .=   "<hr style='clear:left;'>";
  $HTMLkod .= "<li class='Mf'><a href='?f0=oldalterkep'>Oldaltérkép</a></li>\n";
  $HTMLkod .=   "<div id='MenuLab'>";
  $HTMLkod .= Kiir_LatogatásInfo();
  $HTMLkod .= Kiir_SzerzoInfo();
  $HTMLkod .=   "</div>";
  $HTMLkod .=   "</ul>\n\n\n";
  $HTMLkod .= "</nav>";
  $HTMLkod .=  "<br></div>";
  echo $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// Az első színtű gyermekoldalak linkjeinek megjelenítése
// Ha nincs ilyen, akkor üres karakterlánccal tér vissza
function Menu_Szint2($SZOID,$SZTIP)
{
global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  $Li ='';
  $kSZTIP = $SZTIP + 1;
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=$SZTIP and OSzulo=$SZOID ORDER BY OPrioritas DESC"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba ME 04 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    if ($OID==$AktOldal['id'] or $OID==$AktOldal['OSzulo'] or $OID==$AktOldal['OSZSzulo'] )
       {$AktLink = "class='AktLink'";} else {$AktLink = "";}
    $Li .= " <li class='M2'><a href='?f0=$OURL' $AktLink>  $ONev</a>";
    //if ($AktLink>'') {  $Li .= Menu_Szint3($OID,$kSZTIP); } --- A 3. szint kiíratásának lehetősége adott
    $Li .= "</li>\n"; 
  }
  if ($Li>'') {$HTMLkod = "\n<ul class='Ul2'>\n$Li</ul>\n"; } else {$HTMLkod ="";}
  return $HTMLkod;
}

//------------------------------------------------------------------------------------------------------------------
// A második színtű gyermekoldalak linkjeinek megjelenítése
// Ha nincs ilyen, akkor üres karakterlánccal tér vissza
function Menu_Szint3($SZOID,$SZTIP)
{
global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  $Li ='';
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=$SZTIP and OSzulo=$SZOID ORDER BY OPrioritas DESC"; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba ME 05 ");
  while($row = mysqli_fetch_array($result))
  {
    $ONev = $row['ONev']; $OURL = $row['OURL']; $OID  = $row['id'];
    if ($OID==$AktOldal['id'] or $OID==$AktOldal['OSzulo'] or $OID==$AktOldal['OSZSzulo'] )
       {$AktLink = "class='AktLink'";} else {$AktLink = "";}
    $Li .= " <li class='M3'><a href='?f0=$OURL' $AktLink>  $ONev</a>";
    $Li .= "</li>\n"; 
  }
  if ($Li>'') {$HTMLkod = "\n<ul class='Ul3'>\n$Li</ul>\n"; } else {$HTMLkod ="";}
  return $HTMLkod;
}

function Kiir_LatogatásInfo()
{
  $la = latogatok();  $lsz = latogatasok_szama ();  
  $HTMLkod = '<hr /> LÁTOGATÓK:  <br> ';
  $HTMLkod .= 'Online látogatók: '.$la.'<br />';
  $HTMLkod .= 'Eddigi  látogatóink száma: '.$lsz.'<br />  ';
  return $HTMLkod;
}


function Kiir_SzerzoInfo()
{
$HTMLkod = "
            <hr><div style='text-align:center;'>
            A W3Shop teljes leírása megtalálható a webfejlesztes.gtportal.eu oldalon:
            <img src='kepek/ikonok/webaruhaz_logo_1_100.png' alt='Webáruház készítés logó' height='100'  /><br>
            <a href='http://webfejlesztes.gtportal.eu/'>Webáruház készítés</a><br />
            <hr>
            Szerző: Gál Tamás<br />
            <img src='kepek/ikonok/gtportal_profil.png' alt='Gál Tamás fotó' height='100' width='70' /><br>

            <a href='https://plus.google.com/113582773665048373410?rel=author'>Google +</a><br>
            <a href='http://www.gtportal.eu/'>gtportal.eu</a><br />
            </div>
";


  return $HTMLkod;
}

?>
