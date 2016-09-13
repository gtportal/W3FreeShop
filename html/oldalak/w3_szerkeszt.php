<?php
  global $f1, $f2, $f3, $f4, $f5, $VisszaHidden, $hozzaferes;  
  global $MySqliLink; 
  $HTMLkod = '';

//require_once("oldalak/w3_oldalment.php");


  // A szerkesztett oldal nevének lekérdezése
  $OldalID = tiszta_int($f2);
  if ($OldalID>-1) {
     $SelectStr = "SELECT ONev FROM oldal WHERE id=$OldalID LIMIT 1"; 
     $result    = mysqli_query($MySqliLink,$SelectStr) OR die("SZE 01 ");
     $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  }
  //A oldalnév / funkció megjelenítése 
  if ($_POST['submitAltalanos'] == 'Másolás')  {
         echo "<h1>Másolt tartalom szerkesztése</h1>";
  } else {
      if ($_POST['submitAltalanos'] == 'UjOldal')  {
         echo "<h1>Új oldal</h1>";
      } else {
         if ($row['ONev']>'') 
           { 
             if ($f1=='Masol') {echo "<h1>".$row['ONev']. "<small> másolása</small></h1>";
             } else {echo "<h1>".$row['ONev']. "<small> szekesztése</small></h1>";}
           } else {
             if ($f1=='UjOldal') {echo "<h1>Új oldal</h1>";} else {echo "<h1>Szekesztés</h1>";}
           } 
      }
  }
  // Az előző oldalra mutató link összeállítása
  if (($f1=='Torol') || ($f1=='TorolPl') || ($f1=='Modosit') || ($f1=='Masol') || ($f1=='UjOldal') || ($f1=='Feltotes'))
     { echo "<a href='?f0=szerkeszt&f1=$f5&f2=$f3&f3=$f4' class='Kivalasztott aVissza'>  Vissza &nbsp;&nbsp;</a><br><br>\n";}
//Csak megfelelő jogosúltság esetén lehet szerkeszteni
//Rejtett űrlapelemekben tároljuk az előző oldalra történő visszatéréshez szükséges adatokat
if ($hozzaferes>5) {
  switch ($f1) {
    //Az aktuális művelet kiválasztása
    case 'Torol': 
        //Ellenőrizzük, hogy az oldal törőlhető, és megerősítést kérünk
        //Termék eseteén a felhasználónak ki kell választania a termék(ek) kódját (kódjait)
        $HTMLkod .= "<div id='figyelmeztet'><form action='?f0=szerkeszt&f1=TorolPl&f2=$f2' method='post'>\n";
        $HTMLkod .= Torles_teszt($f2);
        $HTMLkod .= "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $HTMLkod .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $HTMLkod .= "<input type='hidden' name='OldalMut' value='$f5'>\n"; 
        $HTMLkod .= "<input type='submit' name='NEM' value='Mégsem'>\n";
        $HTMLkod .= "<input type='submit' name='torolOK' value='Mehet!'></form> </div>\n";
        echo $HTMLkod;
      break;
    case 'TorolPl':
        //Megtörténik az oldal törlése
        if (($_POST['torolOK'] > '') and ($_POST['Torolheto']=='Torolheto')) {$HTMLkod .= Oldal_torol($f2); echo $HTMLkod; }
        require_once("oldalak/w3_oldallista.php"); Kiir_Oladllista();
      break;
    case 'Modosit':  
        // Az oldal adatainak módosítását az Oldal_Modosit() függvény valósítja meg,
        // ha második paramétere 'Modosit'
        $VisszaHidden  = "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $VisszaHidden .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $VisszaHidden .= "<input type='hidden' name='OldalMut' value='$f5'>\n";
        require_once("oldalak/w3_oldalmodosit.php"); Oldal_Modosit($f2,'Modosit');
      break;
    case 'Masol':  
        // Az oldal adatainak másolását az Oldal_Modosit() függvény valósítja meg,
        // ha második paramétere 'Masol'
        $VisszaHidden  = "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $VisszaHidden .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $VisszaHidden .= "<input type='hidden' name='OldalMut' value='$f5'>\n";
        require_once("oldalak/w3_oldalmodosit.php"); Oldal_Modosit($f2,'Masol');
      break;
    case 'UjOldal':  
        // Az űj oldal létrehozását az Oldal_Modosit() függvény valósítja meg,
        // ha második paramétere 'UjOldal'
        $VisszaHidden  = "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $VisszaHidden .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $VisszaHidden .= "<input type='hidden' name='OldalMut' value='$f5'>\n";
        require_once("oldalak/w3_oldalmodosit.php"); Oldal_Modosit($f2,'UjOldal');
      break;

    case 'Feltotes':  
        $VisszaHidden  = "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $VisszaHidden .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $VisszaHidden .= "<input type='hidden' name='OldalMut' value='$f5'>\n";
        require_once("oldalak/w3_oldalfeltolt.php"); Oldal_Feltotes($f2,'UjOldal');
      break;

    case 'Mentes':  
        $VisszaHidden  = "<input type='hidden' name='TipValszt' value='$f3'>\n";
        $VisszaHidden .= "<input type='hidden' name='SzuloValaszt' value='$f4'>\n";
        $VisszaHidden .= "<input type='hidden' name='OldalMut' value='$f5'>\n";
        require_once("oldalak/w3_oldalment.php"); CSVKategoriaMentes(); 
        CSVHirKategoriaMentes(); CSVAlKategoriaMentes(); CSVHirekMentes(); 
        CSVKepekMentes(); CSVTermekJellemzoMentes(); CSVTermekMentes(); 
        $f1=$f2; $f2=$f3; $f3=$f4; 
        require_once("oldalak/w3_oldallista.php"); Kiir_Oladllista(); 
      break;

    default:  require_once("oldalak/w3_oldallista.php"); Kiir_Oladllista(); 
  }
}


//------------------------------------------------------------------------------------------------------------------
// OLDAL TÖRLÉSE
//------------------------------------------------------------------------------------------------------------------
// Ha a felhasználó megerősíti a törlési szándékát, akkor a kiválasztott oldalt töröljük.
//
//

function Oldal_torol($TorolONev)
{
global $mm_felhasznalo, $MySqliLink, $AktOldal, $hozzaferes; 
 $HTMLkod = '';
 if ($hozzaferes>6) {
   if ($_POST['OldalID'] > 0) {
     $OldalID = tiszta_int($_POST['OldalID']);
     $SelectStr = "SELECT ONev, OTipus FROM oldal WHERE id=$OldalID LIMIT 1"; 
     $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba SZE 02 ");
     $rowDB     = mysqli_num_rows($result);

     // Az oldal valóban létezik?
     if ($rowDB > 0){
        // Az oldal létezik
        $row    = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
        $OTipus = $row['OTipus'];
        $ONev   = $row['ONev'];
        // A kezdőlap és az alapvető oldalak nem törőlhetőek
        if (($OTipus > 0) and ($OTipus < 50)){
          // Különböző oldaltípusokhoz különböző további táblák tartozhatnak 
          switch ($OTipus) {
            case OKategoria: 
                $DeleteStr = "Delete FROM oldal  WHERE id = $OldalID";
                if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 03 ");}
                $HTMLkod  .= "A(z) $ONev kategória törlődött!";
              break;
            case OAlkategoria: 
                $DeleteStr = "Delete FROM oldal  WHERE id = $OldalID";
                if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 04 ");}
                $HTMLkod  .= "A(z) $ONev alkategória törlődött!";
              break;
            case OTermek: 
              //  $HTMLkod .= "OTermek";
                // Ellenőrizzük, hogy az összes termék altípus törlésre kerül-e.
                $TeljesTorles = 1;
                $TermekIDstr  = tiszta_szov($_POST['IDStr']);
                $TermekIDtmb  = explode('|', $TermekIDstr);
                foreach ($TermekIDtmb as $v) { 
                  $v=tiszta_int($v);
                  $IdMut="id_$v";
                  if ($_POST[$IdMut] > 0) {
                    $DeleteStr = "Delete FROM termek  WHERE id = $v";
                    if (!mysqli_query($MySqliLink,$DeleteStr))  {die("Hiba SZE 05 ");}
                  } else { $TeljesTorles = 0; }
                }
                if ($TeljesTorles>0) {
                    // Először törlöljük a kapcsolt táblákban a termékhez rendelt rekordokat
                    $DeleteStr = "Delete FROM termek_jellemzo  WHERE Oid = $OldalID";
                    if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 06 ");}

                    $DeleteStr = "Delete FROM termek_leiras  WHERE Oid = $OldalID";
                    if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 07 ");}

                    $DeleteStr = "Delete FROM termek  WHERE Oid = $OldalID";
                    if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 08 ");}

                    $DeleteStr = "Delete FROM oldal_tartalom  WHERE Oid = $OldalID";
                    if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 09 ");}

                    // Végül töröljük magát az oldalt
                    $DeleteStr = "Delete FROM oldal  WHERE id = $OldalID";
                    if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 10 ");}

                   $HTMLkod   .= "A(z) $ONev oldal törlődött!";
                } else {
                   $HTMLkod   .= "A(z) $ONev oldal kijelölt termékei törlődtek!";
                }
              break;
            case OHirkategoria: 
                $HTMLkod  .= "Hirkategoria";
                $DeleteStr = "Delete FROM oldal  WHERE id = $OldalID";
                if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 11 ");}
                $HTMLkod  .= "A(z) $ONev hírkategória törlődött!";
              break;

            case OHirOldal: 
                $HTMLkod  .= "OHirOldal";
                $DeleteStr = "Delete FROM oldal  WHERE id = $OldalID";
                if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba SZE 12 ");}
                $HTMLkod  .= "A(z) $ONev híroldal törlődött!";
              break;
            default:   $HTMLkod .= "Ismeretlen típus";
          }
        } else {
          // Nem törőlhető oldaltípus
          $HTMLkod = "<h1>Nem törőlhető:".$OTipus."</h1>"; 
        }
     } else {
        // Az oldal nem létezik
        $HTMLkod = "<h1>Nincs ilyen oldal:".$_POST['OldalID']."</h1>"; 
     }
   }
   return $HTMLkod; 
  }
}
//------------------------------------------------------------------------------------------------------------------
// OLDAL TÖRLÉSE ELŐTTI ELLENŐRZÉS (Kérdés és feltételek vizsgálata)
//------------------------------------------------------------------------------------------------------------------
// Ellenőrizzük, hogy
// 1. a törlendő oldal létezik
// 2. a törlendő oldalnak nincsenek gyermekoldalai
// 3. termék esetém adott néven egy vagy több termék van
//
//    Termékek esetén adott néven (de különböző méretben..) több termék is lehet,
//    ekkor a felhasználótól megkérdezzük, hogy mely terméket vagy termékeket akar törőlni. 

function Torles_teszt($TorolONev)
{
  global $mm_felhasznalo, $MySqliLink, $AktOldal;  
  $HTMLkod = ''; $Oid = -1;

//CSVKategoriaMentes(); CSVHirKategoriaMentes(); CSVAlKategoriaMentes(); CSVHirekMentes(); CSVKepekMentes(); 
//CSVTermekJellemzoMentes(); CSVTermekMentes(); 

//CSVTermekMentes(); 

  // Oldal ID lekérdezése
        $SelectStr = "SELECT id, OTipus FROM oldal WHERE ONev='$TorolONev' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba SZE 13 ");
        $rowDB    = mysqli_num_rows($result);
        if ($rowDB > 0) {
          // Az oldal létezik
          $row      = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
          $Oid      = $row['id'];
          $OTipus   = $row['OTipus'];
          
          $HTMLkod .= "<input type='hidden' name='OldalID' value='$Oid'>\n"; 

         // Aloldalak lekérdezése
          $SelectStr = "SELECT id FROM oldal WHERE OSzulo=$Oid "; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba SZE 14 ");
          $rowDB     = mysqli_num_rows($result);  mysqli_free_result($result);
          if ($rowDB > 0) {
                 $HTMLkod .=  "<div id='hibaStr'><b>A(z) $TorolONev oldal csak az aloldalai törlése után törölhető! </b></div>\n";
          } else {
          // Ha terméket törlünk, akkor ellenőrizni kell, hogy adott néven több termék is lehet
            if ($OTipus==OTermek) {
               $HTMLkod .= "<h1>Mely termékeket kívánja törölni?</h1>\n";
               $IDStr    = '';

               $SelectStr = "SELECT id, TKod, TtulNev, TtulErt FROM termek WHERE Oid=$Oid "; 
               $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba SZE 15 ");
               while($row = mysqli_fetch_array($result))
               {
                  $TtulNev = $row['TtulNev'];
                  $TBLkod .=  "<tr><td><input type='checkbox' name='id_".$row['id']."'  value='".$row['id']."'>
                               </td><td>".$row['TKod']."</td><td>".$row['TtulErt']."</td><td>$TorolONev</td></tr>\n";
                  if ($IDStr=='') {$IDStr .=  $row['id'];} else {$IDStr .= "|" .$row['id'];}          
              }
              mysqli_free_result($result);
              $HTMLkod .=  "<table><tr><th>Ssz</th><th>Kód</th><th>".$TtulNev."</th><th>Terméknév</th></tr>".$TBLkod."</table>\n";
              $HTMLkod .= "<input type='hidden' name='IDStr' value='$IDStr'>\n";
              $HTMLkod .= "<input type='hidden' name='Torolheto' value='Torolheto'>\n"; 
            } else {
              $HTMLkod .=  "<h1>Biztosan törli a(z) $TorolONev oldalt? </h1>\n"; 
              $HTMLkod .= "<input type='hidden' name='Torolheto' value='Torolheto'>\n";                 
            }
          }
       } else {
          $HTMLkod .=  "<div id='hibaStr'><b>A(z) $TorolONev oldal nem létezik </b></div>\n";
       }
   return $HTMLkod; 
  }

?>
