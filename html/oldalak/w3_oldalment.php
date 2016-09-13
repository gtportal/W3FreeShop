<?php
    

define("KatFileMent", "csv_ment/kategoriak.csv", TRUE);
define("AlkatFileMent", "csv_ment/alkategoriak.csv", TRUE);
define("TermekFileMent", "csv_ment/termek.csv", TRUE);

define("TermekJellemzoFileMent", "csv_ment/termek_jellemzo.csv", TRUE);
define("KepFileMent", "csv_ment/kep.csv", TRUE);
define("HirFileMent", "csv_ment/hirek.csv", TRUE);
define("HirKatFileMent", "csv_ment/hirkategoriak.csv", TRUE);


define("KocsiVissza", chr(13), TRUE);
define("SorEmeles", chr(10), TRUE);



//------------------------------------------------------------------------------------------------------------------
// KATEGÓRIÁK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVKategoriaMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','OURL','OKep','ORLeiras','OKulcszsavak','OTipus','OSzulo','OPrioritas','OTartalom');
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=1"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   $AdatTMB[0] = $row['ONev'];
   $AdatTMB[1] = $row['OURL'];
   $AdatTMB[2] = $row['OKep'];
   $AdatTMB[3] = $row['ORLeiras'];
   $AdatTMB[4] = $row['OKulcszsavak'];
   $AdatTMB[5] = $row['OTipus'];
   $AdatTMB[6] = $row['OSzulo'];
   $AdatTMB[7] = $row['OPrioritas'];
   // A oldal tartalmának betöltése
   $SelectStr1 = "SELECT * FROM oldal_tartalom WHERE Oid=".$row['id']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[8] = $row1['OTartalom'];
   $SorTMB[]   = $AdatTMB;
   
  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(KatFileMent, 'w') or die("A ".KatFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}


//------------------------------------------------------------------------------------------------------------------
// HÍRKATEGÓRIÁK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVHirKategoriaMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','OURL','OKep','ORLeiras','OKulcszsavak','OTipus','OSzulo','OPrioritas','OTartalom');
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=10"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   $AdatTMB[0] = $row['ONev'];
   $AdatTMB[1] = $row['OURL'];
   $AdatTMB[2] = $row['OKep'];
   $AdatTMB[3] = $row['ORLeiras'];
   $AdatTMB[4] = $row['OKulcszsavak'];
   $AdatTMB[5] = $row['OTipus'];
   $AdatTMB[6] = $row['OSzulo'];
   $AdatTMB[7] = $row['OPrioritas'];
   // A oldal tartalmának betöltése
   $SelectStr1 = "SELECT * FROM oldal_tartalom WHERE Oid=".$row['id']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[8] = $row1['OTartalom'];
   $SorTMB[]   = $AdatTMB;   
  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(HirKatFileMent, 'w') or die("A ".HirKatFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}

//------------------------------------------------------------------------------------------------------------------
// HÍRKATEGÓRIÁK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVAlKategoriaMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','OURL','OKep','ORLeiras','OKulcszsavak','OTipus','OSzulo','OPrioritas','OTartalom');
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=2"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   $AdatTMB[0] = $row['ONev'];
   $AdatTMB[1] = $row['OURL'];
   $AdatTMB[2] = $row['OKep'];
   $AdatTMB[3] = $row['ORLeiras'];
   $AdatTMB[4] = $row['OKulcszsavak'];
   $AdatTMB[5] = $row['OTipus'];
   //Szülőoldal nevének lekérdezése
   $SelectStr1 = "SELECT ONev FROM oldal WHERE id=".$row['OSzulo']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[6] = $row1['ONev'];
   $AdatTMB[7] = $row['OPrioritas'];
   // A oldal tartalmának betöltése
   $SelectStr1 = "SELECT * FROM oldal_tartalom WHERE Oid=".$row['id']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[8] = $row1['OTartalom'];
   $SorTMB[]   = $AdatTMB;   
  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(AlkatFileMent, 'w') or die("A ".AlkatFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}

//------------------------------------------------------------------------------------------------------------------
// HÍREK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVHirekMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','OURL','OKep','ORLeiras','OKulcszsavak','OTipus','OSzulo','OPrioritas','OTartalom');
  $SelectStr = "SELECT * FROM oldal WHERE OTipus=11"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   $AdatTMB[0] = $row['ONev'];
   $AdatTMB[1] = $row['OURL'];
   $AdatTMB[2] = $row['OKep'];
   $AdatTMB[3] = $row['ORLeiras'];
   $AdatTMB[4] = $row['OKulcszsavak'];
   $AdatTMB[5] = $row['OTipus'];
  // $AdatTMB[6] = $row['OSzulo'];
   //Szülőoldal nevének lekérdezése
   $SelectStr1 = "SELECT ONev FROM oldal WHERE id=".$row['OSzulo']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[6] = $row1['ONev'];

   $AdatTMB[7] = $row['OPrioritas'];
   // A oldal tartalmának betöltése
   $SelectStr1 = "SELECT * FROM oldal_tartalom WHERE Oid=".$row['id']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[8] = $row1['OTartalom'];
   $SorTMB[]   = $AdatTMB;   
  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(HirFileMent, 'w') or die("A ".HirFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}

//------------------------------------------------------------------------------------------------------------------
// KÉPEK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------


function CSVKepekMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','KNev','KURL','KLeiras','KSorszam');
  $SelectStr = "SELECT * FROM kep"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {

   $SelectStr1 = "SELECT * FROM oldal WHERE id=".$row['Oid']." LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);

   $AdatTMB[0] = $row1['ONev'];
   $AdatTMB[1] = $row['KNev'];
   $AdatTMB[2] = $row['KURL'];
   $AdatTMB[3] = $row['KLeiras'];
   $AdatTMB[4] = $row['KSorszam'];
   $SorTMB[]   = $AdatTMB;   
  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(KepFileMent, 'w') or die("A ".KepFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}


//------------------------------------------------------------------------------------------------------------------
// TERMÉK JELLEMZŐK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVTermekJellemzoMentes()
{
  global $MySqliLink;   
  // A fejléc összeállítása
  $FejlecTMB[]  =  'ONev';
  $SelectStr = "SELECT DISTINCT JNev  FROM  termek_jellemzo"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   $FejlecTMB[] = $row['JNev'];   
  }
  mysqli_free_result($result);
  $SorTMB[]   = $FejlecTMB;  
  // Tartalom összeállítása 
   $SelectStr = "SELECT * FROM oldal WHERE OTipus=3"; 
   $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T2");
  while($row = mysqli_fetch_array($result))
   {
     $ONev = $row['ONev'];
     $Oid = $row['id'];
     for($i=0; $i<count($FejlecTMB); $i++) {$TartalomTMB[$i]='x';} $TartalomTMB[0]=$ONev;
     $SelectStr1 = "SELECT  *  FROM  termek_jellemzo WHERE Oid=$Oid";  
     $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T3x");
     while($row1 = mysqli_fetch_array($result1))
     {
      // Az oszlopnak megfelelő tömbelembe helyezzük az értéket
      $oszlop = array_search($row1['JNev'], $FejlecTMB);
      $TartalomTMB[$oszlop]= $row1['JErtek'];   
     }
     mysqli_free_result($result1);
     $SorTMB[]   = $TartalomTMB; 
  }
  $fp = fopen(TermekJellemzoFileMent, 'w')  or die("A ".TermekJellemzoFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}

//------------------------------------------------------------------------------------------------------------------
// TERMÉKEK MENTÉSE CSV FÁJLBA
//------------------------------------------------------------------------------------------------------------------

function CSVTermekMentes()
{
  global $MySqliLink;   
  // fejléc összeállítása
  $SorTMB[]  =  array('ONev','OURL','OKep','ORLeiras','OKulcszsavak','OTipus','OSzulo','OPrioritas','OTartalom','TAr', 'TSzorzo','TKod', 'TtulNev', 'TtulErt', 'TSzalKlts', 'TSzallit', 'TLeiras' );

  $SelectStr = "SELECT * FROM termek"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba T3");
  while($row = mysqli_fetch_array($result))
  {
   // A termék tábla tartalmának betöltése
   $OID  = $row['Oid'];
   $SelectStr1 = "SELECT * FROM oldal WHERE id=$OID LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[0] = $row1['ONev'];
   $AdatTMB[1] = $row1['OURL'];
   $AdatTMB[2] = $row1['OKep'];
   $AdatTMB[3] = $row1['ORLeiras'];
   $AdatTMB[4] = $row1['OKulcszsavak'];
   $AdatTMB[5] = $row1['OTipus'];
   //$AdatTMB[6] = $row1['OSzulo'];
   //Szülőoldal nevének lekérdezése
   $SelectStr2 = "SELECT ONev FROM oldal WHERE id=".$row1['OSzulo']." LIMIT 1"; 
   $result2    = mysqli_query($MySqliLink,$SelectStr2) OR die("Hiba T2");
   $row2       = mysqli_fetch_array($result2, MYSQLI_ASSOC);mysqli_free_result($result2);
   $AdatTMB[6] = $row2['ONev'];

   $AdatTMB[7] = $row1['OPrioritas'];
   // A oldal tartalmának betöltése
   $SelectStr1 = "SELECT * FROM oldal_tartalom WHERE Oid=$OID LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[8] = $row1['OTartalom'];

   $AdatTMB[9]  = $row['TAr'];
   $AdatTMB[10] = $row['TSzorzo'];
   $AdatTMB[11] = $row['TKod'];
   $AdatTMB[12] = $row['TtulNev'];
   $AdatTMB[13] = $row['TtulErt'];
   $AdatTMB[14] = $row['TSzalKlts'];
   $AdatTMB[15] = $row['TSzallit'];

   $SelectStr1 = "SELECT TLeiras FROM termek_leiras WHERE Oid=$OID LIMIT 1"; 
   $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba T2");
   $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);mysqli_free_result($result1);
   $AdatTMB[16] = $row1['TLeiras'];

   $SorTMB[]   = $AdatTMB;

  }
  mysqli_free_result($result);
  // A kategoria.csv fájl írása
  $fp = fopen(TermekFileMent, 'w') or die("A ".TermekFileMent." állományt nem lehet megnyitni!");
  foreach ($SorTMB as $fields) {fputcsv($fp, $fields);}
  fclose($fp);
}



?>
