<?php
    
define("KatFile", "csv_cel/kategoriak.csv", TRUE);
define("AlkatFile", "csv_cel/alkategoriak.csv", TRUE);
define("TermekFile", "csv_cel/termek.csv", TRUE);

define("TermekJellemzoFile", "csv_cel/termek_jellemzo.csv", TRUE);
define("KepFile", "csv_cel/kep.csv", TRUE);
define("HirFile", "csv_cel/hirek.csv", TRUE);
define("HirKatFile", "csv_cel/hirkategoriak.csv", TRUE);

//require_once("oldalak/w3_oldalment.php");

//------------------------------------------------------------------------------------------------------------------
// KATEGÓRIÁK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

//CSVKategoriaMentes(); CSVHirKategoriaMentes(); CSVAlKategoriaMentes(); CSVHirekMentes(); CSVKepekMentes(); 
//CSVTermekJellemzoMentes(); CSVTermekMentes(); 

//CSVAlKategoriaMentes(); CSVHirekMentes();


//CSVKategoriaBetoltPL(); CSVHirKategoriaBetoltPL(); CSVAlkategoriaBetoltPL(); CSVHirekBetoltPL();
//CSVKepJellemzoBetoltPL(); CSVTermekJellemzoBetoltPL();

//CSVTermekMentes(); 
//CSVTermekBetoltPL();



function CSVKategoriaBetolt()
{
  global $MySqliLink;                          
  
  $ErrorStr   = '';
  $_SOREMELES = chr(13).chr(10);
  $i          = 0;
  $FSorok     = array(); 

  $rowCT = 0;
  if (($handle = fopen(KatFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".KatFile." állományt nem lehet megnyitni!";}

  $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'OURL' => -1 ,'OKep' => -1 ,'ORLeiras' => -1 ,'OKulcszsavak' => -1 ,
                      'OTipus' => -1 ,'OSzulo' => -1 ,'OPrioritas' => -1 , 'OTartalom' => -1 );
  $InitTMB   = array('id' => 0 ,'ONev' => '','OURL' => 'x' ,'OKep' => '','ORLeiras' => ''  ,'OKulcszsavak' => '' ,
                      'OTipus' => 1 ,'OSzulo' => 1 ,'OPrioritas' => 1 , 'OTartalom' => '' );
  $i=0;
  foreach ($FSorok as $FSor) {
   if ($i==0) {
      //Fejléc kezelése
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t");
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='OURL') {$FejlecTMB['OURL']=$j;}
        if($v=='OKep') {$FejlecTMB['OKep']=$j;}
        if($v=='ORLeiras') {$FejlecTMB['ORLeiras']=$j;}
        if($v=='OKulcszsavak') {$FejlecTMB['OKulcszsavak']=$j;}
        if($v=='OTipus') {$FejlecTMB['OTipus']=$j;}
        if($v=='OSzulo') {$FejlecTMB['OSzulo']=$j;}
        if($v=='OPrioritas') {$FejlecTMB['OPrioritas']=$j;}
        if($v=='OTartalom') {$FejlecTMB['OTartalom']=$j;}
        $j++;
      } $i=1;
    } else {
      //Tartalom kezelése
      $KatTMB = $InitTMB;
      $KatStrTMB = $FSor;
      $j=0; 
      foreach ($KatStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$KatTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)  {$KatTMB['ONev'] = $v;  }
        if ($FejlecTMB['OURL']==$j)  {$KatTMB['OURL'] = $v;}
        if ($FejlecTMB['OKep']==$j)  {$KatTMB['OKep'] = $v;}
        if ($FejlecTMB['ORLeiras']==$j)  {$KatTMB['ORLeiras'] = $v;}
        if ($FejlecTMB['OKulcszsavak']==$j)  {$KatTMB['OKulcszsavak'] = $v;}
        if ($FejlecTMB['OTipus']==$j)  {$KatTMB['OTipus'] = $v;} 
        if ($FejlecTMB['OSzulo']==$j)  {$KatTMB['OSzulo'] = $v;}
        if ($FejlecTMB['OPrioritas']==$j)  {$KatTMB['OPrioritas'] = $v;}
        if ($FejlecTMB['OTartalom']==$j)  {$KatTMB['OTartalom'] = tiszta_szov($v);}
        $j++;
      } 
      if (2 > strlen($KatTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév! ';} // Nincs név
      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($KatTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);
        // Van már adott néven kategória???
        $SelectStr = "SELECT id FROM oldal WHERE ONev='".$KatTMB['ONev']."' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 01");
        $rowDB     = mysqli_num_rows($result);
        if ($rowDB > 0) {
         // Ha van, akkor adatait módosítjuk
           $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
           $id = $row['id'];
           $UpdateStr = "";
           if ($KatTMB['OKep']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKep='".$KatTMB['OKep']."'";} 
              else {$UpdateStr .= " OKep='".$KatTMB['OKep']."'";}}
           if ($KatTMB['ORLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", ORLeiras='".$KatTMB['ORLeiras']."'";} 
              else {$UpdateStr .= " ORLeiras='".$KatTMB['ORLeiras']."'";}}
           if ($KatTMB['OKulcszsavak']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKulcszsavak='".$KatTMB['OKulcszsavak']."'";} 
              else {$UpdateStr .= " OKulcszsavak='".$KatTMB['OKulcszsavak']."'";}}
           if ($KatTMB['OPrioritas']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OPrioritas='".$KatTMB['OPrioritas']."'";} 
              else {$UpdateStr .= " OPrioritas='".$KatTMB['OPrioritas']."'";}}
           if ($UpdateStr>'') {$UpdateStr .= ", ODatum=NOW()";}
           // Az ONev és az OURL változatlan. Az OTipus és az OSzulo az integritás megörzése érdekében nem változhat.
           $UpdateStr = "UPDATE oldal SET $UpdateStr WHERE id=$id";
           if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 02 ";}

          if ($KatTMB['OTartalom']>'') {
             $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='".$KatTMB['OTartalom']."' WHERE id=$id";
             if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 03 ";}
           }
          // Ha nincs, akkor az új kategóriát felvesszük
        } else { 
           $InsertIntoStr = "INSERT INTO oldal (ONev, OURL, OKep, ORLeiras, OKulcszsavak, OTipus, OSzulo, OPrioritas, ODatum) "
                          . "VALUES ('".$KatTMB['ONev']."','".$tiszta_OURL."','"
           .$KatTMB['OKep']."','".$KatTMB['ORLeiras']."','".$KatTMB['OKulcszsavak']."',".$KatTMB['OTipus'].",".$KatTMB['OSzulo']
           .",".$KatTMB['OPrioritas'].", NOW())";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 04 ";} else { $ID1= mysqli_insert_id($MySqliLink);} 

          $InsertIntoStr = "INSERT INTO oldal_tartalom  (Oid, OTartalom) "
                         . "VALUES ($ID1, '".$KatTMB['OTartalom']."')";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 05 ";} 
        }
      }
    } 
  }
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A kategóriák adatai feltöltve.";}
  return $ErrorStr;
}


//------------------------------------------------------------------------------------------------------------------
// HIRKATEGÓRIÁK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVHirKategoriaBetolt()
{
  global $MySqliLink; 
  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(HirKatFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".HirKatFile." állományt nem lehet megnyitni!";}

  $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'OURL' => -1 ,'OKep' => -1 ,'ORLeiras' => -1 ,'OKulcszsavak' => -1 ,
                      'OTipus' => -1 ,'OSzulo' => -1 ,'OPrioritas' => -1 , 'OTartalom' => -1 );
  $InitTMB = array('id' => 0 ,'ONev' => '','OURL' => 'x' ,'OKep' => '','ORLeiras' => ''  ,'OKulcszsavak' => '' ,
                    'OTipus' => 10 ,'OSzulo' => 1 ,'OPrioritas' => 1 , 'OTartalom' => '' );
  $i=0;
  foreach ($FSorok as $FSor) {
  //Fejléc kezelése
   if ($i==0) {
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t"); 
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='OURL') {$FejlecTMB['OURL']=$j;}
        if($v=='OKep') {$FejlecTMB['OKep']=$j;}
        if($v=='ORLeiras') {$FejlecTMB['ORLeiras']=$j;}
        if($v=='OKulcszsavak') {$FejlecTMB['OKulcszsavak']=$j;}
        if($v=='OTipus') {$FejlecTMB['OTipus']=$j;}
        if($v=='OSzulo') {$FejlecTMB['OSzulo']=$j;}
        if($v=='OPrioritas') {$FejlecTMB['OPrioritas']=$j;}
        if($v=='OTartalom') {$FejlecTMB['OTartalom']=$j;}
        $j++;
      } $i=1;
    } else {
      $HirKatTMB    = $InitTMB;
      $HirKatStrTMB = $FSor;
      $j=0; 
      foreach ($HirKatStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$HirKatTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)  {$HirKatTMB['ONev'] = $v;  }
        if ($FejlecTMB['OURL']==$j)  {$HirKatTMB['OURL'] = $v;}
        if ($FejlecTMB['OKep']==$j)  {$HirKatTMB['OKep'] = $v;}
        if ($FejlecTMB['ORLeiras']==$j)  {$HirKatTMB['ORLeiras'] = $v;}
        if ($FejlecTMB['OKulcszsavak']==$j)  {$HirKatTMB['OKulcszsavak'] = $v;}
        if ($FejlecTMB['OTipus']==$j)  {$HirKatTMB['OTipus'] = $v;} 
        if ($FejlecTMB['OSzulo']==$j)  {$HirKatTMB['OSzulo'] = $v;}
        if ($FejlecTMB['OPrioritas']==$j)  {$HirKatTMB['OPrioritas'] = $v;}
        if ($FejlecTMB['OTartalom']==$j)  {$HirKatTMB['OTartalom'] = tiszta_szov($v);}
        $j++;
      } 
      if (2 > strlen($HirKatTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév!';} // Nincs név
  
      $Szuloid = 1; //Hírkategória mindig a kezdőlaphoz kapcsolódik
      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($HirKatTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);
        // Van már adott néven kategória???
        $SelectStr = "SELECT id FROM oldal WHERE ONev='".$HirKatTMB['ONev']."' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 06");
        $rowDB     = mysqli_num_rows($result);
        if ($rowDB > 0) {
         // Ha van, akkor adatait módosítjuk
           $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
           $id = $row['id'];
           $UpdateStr = "";
           if ($HirKatTMB['OKep']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKep='".$HirKatTMB['OKep']."'";} 
              else {$UpdateStr .= " OKep='".$HirKatTMB['OKep']."'";}}
           if ($HirKatTMB['ORLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", ORLeiras='".$HirKatTMB['ORLeiras']."'";} 
              else {$UpdateStr .= " ORLeiras='".$HirKatTMB['ORLeiras']."'";}}
           if ($HirKatTMB['OKulcszsavak']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKulcszsavak='".$HirKatTMB['OKulcszsavak']."'";} 
              else {$UpdateStr .= " OKulcszsavak='".$HirKatTMB['OKulcszsavak']."'";}}
           if ($HirKatTMB['OPrioritas']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OPrioritas='".$HirKatTMB['OPrioritas']."'";} 
              else {$UpdateStr .= " OPrioritas='".$HirKatTMB['OPrioritas']."'";}}

           if ($UpdateStr>'') {$UpdateStr .= ", ODatum=NOW()";}
           // Az ONev és az OURL változatlan. Az OTipus és az OSzulo az integritás megörzése érdekében nem változhat.
           $UpdateStr = "UPDATE oldal SET $UpdateStr WHERE id=$id";
           if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 07 ";}

          if ($HirKatTMB['OTartalom']>'') {
             $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='".$HirKatTMB['OTartalom']."' WHERE id=$id";
             if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 08 ";}
           }
          // Ha nincs, akkor az új kategóriót felvesszük
        } else { 
           $InsertIntoStr = "INSERT INTO oldal (ONev, OURL, OKep, ORLeiras, OKulcszsavak, OTipus, OSzulo, OPrioritas, ODatum)"
                          . "VALUES ('".$HirKatTMB['ONev']."','".$tiszta_OURL."','"
                          .$HirKatTMB['OKep']."','".$HirKatTMB['ORLeiras']."','".$HirKatTMB['OKulcszsavak']."',".$HirKatTMB['OTipus'].","
                          .$Szuloid.",".$HirKatTMB['OPrioritas'].", NOW())";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 09 ";} else { $ID1= mysqli_insert_id($MySqliLink); } 

          $InsertIntoStr = "INSERT INTO oldal_tartalom  (Oid, OTartalom) "
                         . "VALUES ($ID1, '".$HirKatTMB['OTartalom']."')";
          if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 10 ";} 
        }
      }
    } 
  }
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A hírkategóriák feltöltve.";}
  return $ErrorStr;
}




//------------------------------------------------------------------------------------------------------------------
// ALKATEGÓRIÁK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVAlkategoriaBetolt()
{
  global $MySqliLink; 
  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(AlkatFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".AlkatFile." állományt nem lehet megnyitni!";}

  $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'OURL' => -1 ,'OKep' => -1 ,'ORLeiras' => -1 ,'OKulcszsavak' => -1 ,
                      'OTipus' => -1 ,'OSzulo' => -1 ,'OPrioritas' => -1 , 'OTartalom' => -1 );
  $InitTMB = array('id' => 0 ,'ONev' => '','OURL' => 'x' ,'OKep' => '','ORLeiras' => ''  ,'OKulcszsavak' => '' ,
                    'OTipus' => 2 ,'OSzulo' => '' ,'OPrioritas' => 1 , 'OTartalom' => '' );
  $i=0;
  foreach ($FSorok as $FSor) {
   if ($i==0) {
      //Fejléc kezelése
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t"); 
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='OURL') {$FejlecTMB['OURL']=$j;}
        if($v=='OKep') {$FejlecTMB['OKep']=$j;}
        if($v=='ORLeiras') {$FejlecTMB['ORLeiras']=$j;}
        if($v=='OKulcszsavak') {$FejlecTMB['OKulcszsavak']=$j;}
        if($v=='OTipus') {$FejlecTMB['OTipus']=$j;}
        if($v=='OSzulo') {$FejlecTMB['OSzulo']=$j;}
        if($v=='OPrioritas') {$FejlecTMB['OPrioritas']=$j;}
        if($v=='OTartalom') {$FejlecTMB['OTartalom']=$j;}
        $j++;
      } $i=1;
    } else {
      //Tartalom kezelése
      $alKatTMB = $InitTMB;
      $alKatStrTMB = $FSor;
      $j=0; 
      foreach ($alKatStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$alKatTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)  {$alKatTMB['ONev'] = $v;  }
        if ($FejlecTMB['OURL']==$j)  {$alKatTMB['OURL'] = $v;}
        if ($FejlecTMB['OKep']==$j)  {$alKatTMB['OKep'] = $v;}
        if ($FejlecTMB['ORLeiras']==$j)  {$alKatTMB['ORLeiras'] = $v;}
        if ($FejlecTMB['OKulcszsavak']==$j)  {$alKatTMB['OKulcszsavak'] = $v;}
        if ($FejlecTMB['OTipus']==$j)  {$alKatTMB['OTipus'] = $v;} 
        if ($FejlecTMB['OSzulo']==$j)  {$alKatTMB['OSzulo'] = $v;}
        if ($FejlecTMB['OPrioritas']==$j)  {$alKatTMB['OPrioritas'] = $v;}
        if ($FejlecTMB['OTartalom']==$j)  {$alKatTMB['OTartalom'] = tiszta_szov($v);}
        $j++;
      } 

      if (2 > strlen($alKatTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév! ';} // Nincs oldalnév 
      if (2 > strlen($alKatTMB['OSzulo'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó szülőoldal! '.$alKatTMB['ONev'].";";} // Nincs szülőoldal

      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($alKatTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);

        // Szülő oldal OURL-jének keresése 
        // Névből URL
        $szulo_OURL = strtolower(trim($alKatTMB['OSzulo']));
        $szulo_OURL = URLTisztit($szulo_OURL);
        $SelectStr  = "SELECT id FROM oldal WHERE OURL='$szulo_OURL' LIMIT 1"; 
        $result     = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 11");
        $rowDB      = mysqli_num_rows($result);
        if ($rowDB > 0) {
          $row   = mysqli_fetch_array($result, MYSQLI_ASSOC);  mysqli_free_result($result);
          $katid = $row['id'];
   
          // Van már adott néven alkategória???
          $SelectStr = "SELECT id FROM oldal WHERE ONev='".$alKatTMB['ONev']."' LIMIT 1"; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 12");
          $rowDB     = mysqli_num_rows($result);
          if ($rowDB > 0) 
           // Ha van, akkor adatait módosítjuk
          {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
            $id = $row['id'];
            $UpdateStr = "";
            if ($alKatTMB['OKep']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKep='".$alKatTMB['OKep']."'";} 
              else {$UpdateStr .= " OKep='".$alKatTMB['OKep']."'";}}
            if ($alKatTMB['ORLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", ORLeiras='".$alKatTMB['ORLeiras']."'";} 
              else {$UpdateStr .= " ORLeiras='".$alKatTMB['ORLeiras']."'";}}
            if ($alKatTMB['OKulcszsavak']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKulcszsavak='".$alKatTMB['OKulcszsavak']."'";}
              else {$UpdateStr .= " OKulcszsavak='".$alKatTMB['OKulcszsavak']."'";}}
            if ($alKatTMB['OPrioritas']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OPrioritas='".$alKatTMB['OPrioritas']."'";} 
              else {$UpdateStr .= " OPrioritas='".$alKatTMB['OPrioritas']."'";}}
            if ($katid>0) { if ($UpdateStr>'') {$UpdateStr .= ", OSzulo=$katid";} 
              else {$UpdateStr .= " OSzulo=$katid";}}
            if ($UpdateStr>'') {$UpdateStr .= ", ODatum=NOW()";}
            // Az ONev és az OURL változatlan. Az OTipus és az OSzulo az integritás megörzése érdekében nem változhat.
            $UpdateStr = "UPDATE oldal SET $UpdateStr WHERE id=$id";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 13 ";} 

            if ($alKatTMB['OTartalom']>'') {
              $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='".$alKatTMB['OTartalom']."' WHERE id=$id";
              if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 14 ";} 
            }
          } else {
            // Ha nincs, akkor az új alkategóriát felvesszük
            mysqli_free_result($result);

            $InsertIntoStr = "INSERT INTO oldal (ONev, OURL, OKep, ORLeiras, OKulcszsavak, OTipus, OSzulo, OPrioritas, ODatum)"
                          . " VALUES ('".$alKatTMB['ONev']."','".$tiszta_OURL."','"
                             .$alKatTMB['OKep']."','".$alKatTMB['ORLeiras']."','".$alKatTMB['OKulcszsavak']."',".$alKatTMB['OTipus'].",".$katid
                             .",".$alKatTMB['OPrioritas'].", NOW())";  
            if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 15 ";}  else { $ID1= mysqli_insert_id($MySqliLink);}
 
            $InsertIntoStr = "INSERT INTO oldal_tartalom  (Oid, OTartalom) "
                           . "VALUES ($ID1, '".$alKatTMB['OTartalom']."')";
            if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 16 ";} 
          }
        } else {
          mysqli_free_result($result);
          $ErrorStr = "Ismeretlen szülőoldal!";
          echo "<p class='Error1'>".$alKatTMB['ONev']. "alkategória szülőkategóriája nem létezik.</p>";
        }
      }
    }
  }
 if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "Az alkategóriák feltöltve.";}
 return $ErrorStr;
}


//------------------------------------------------------------------------------------------------------------------
// HÍREK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVHirekBetolt()
{
  global $MySqliLink; 
  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(HirFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".HirFile." állományt nem lehet megnyitni!";}
  $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'OURL' => -1 ,'OKep' => -1 ,'ORLeiras' => -1 ,'OKulcszsavak' => -1 ,
                     'OTipus' => -1 ,'OSzulo' => -1 ,'OPrioritas' => -1 , 'OTartalom' => -1 );
  $InitTMB   = array('id' => 0 ,'ONev' => '','OURL' => 'x' ,'OKep' => '','ORLeiras' => ''  ,'OKulcszsavak' => '' ,
                     'OTipus' => 11 ,'OSzulo' => '' ,'OPrioritas' => 1 , 'OTartalom' => '' );
  $i=0;
  foreach ($FSorok as $FSor) {
   if ($i==0) {
      //Fejléc kezelése
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t"); 
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='OURL') {$FejlecTMB['OURL']=$j;}
        if($v=='OKep') {$FejlecTMB['OKep']=$j;}
        if($v=='ORLeiras') {$FejlecTMB['ORLeiras']=$j;}
        if($v=='OKulcszsavak') {$FejlecTMB['OKulcszsavak']=$j;}
        if($v=='OTipus') {$FejlecTMB['OTipus']=$j;}
        if($v=='OSzulo') {$FejlecTMB['OSzulo']=$j;}
        if($v=='OPrioritas') {$FejlecTMB['OPrioritas']=$j;}
        if($v=='OTartalom') {$FejlecTMB['OTartalom']=$j;}
        $j++;
      } $i=1;
    } else {
      //Tartalom kezelése
      $HirekTMB = $InitTMB;
      $HirekStrTMB = $FSor;
      $j=0; 
      foreach ($HirekStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$HirekTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)  {$HirekTMB['ONev'] = $v;  }
        if ($FejlecTMB['OURL']==$j)  {$HirekTMB['OURL'] = $v;}
        if ($FejlecTMB['OKep']==$j)  {$HirekTMB['OKep'] = $v;}
        if ($FejlecTMB['ORLeiras']==$j)  {$HirekTMB['ORLeiras'] = $v;}
        if ($FejlecTMB['OKulcszsavak']==$j)  {$HirekTMB['OKulcszsavak'] = $v;}
        if ($FejlecTMB['OTipus']==$j)  {$HirekTMB['OTipus'] = $v;} 
        if ($FejlecTMB['OSzulo']==$j)  {$HirekTMB['OSzulo'] = $v;}
        if ($FejlecTMB['OPrioritas']==$j)  {$HirekTMB['OPrioritas'] = $v;}
        if ($FejlecTMB['OTartalom']==$j)  {$HirekTMB['OTartalom'] = tiszta_szov($v);  } 
        $j++;
      } 

      if (2 > strlen($HirekTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév!';} // Nincs oldalnév 
      if (2 > strlen($HirekTMB['OSzulo'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó szülőoldal'.$HirekTMB['OSzulo'].";";} // Nincs szülőoldal

      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($HirekTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);

        // Szülő oldal OURL-jének keresése 
        // Névből URL
        $szulo_OURL = strtolower(trim($HirekTMB['OSzulo']));
        $szulo_OURL = URLTisztit($szulo_OURL);
        $SelectStr  = "SELECT id FROM oldal WHERE OURL='$szulo_OURL' LIMIT 1"; 
        $result     = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 17");
        $rowDB      = mysqli_num_rows($result);
        if ($rowDB > 0) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
          $katid = $row['id'];
          
          // Van már adott néven híroldal???
          $SelectStr = "SELECT id FROM oldal WHERE ONev='".$HirekTMB['ONev']."' LIMIT 1"; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 18");
          $rowDB     = mysqli_num_rows($result);
          if ($rowDB > 0) 
           // Ha van, akkor adatait módosítjuk
          {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  mysqli_free_result($result);
            $id = $row['id'];
            $UpdateStr = "";
            if ($HirekTMB['OKep']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKep='".$HirekTMB['OKep']."'";} 
              else {$UpdateStr .= " OKep='".$HirekTMB['OKep']."'";}}
            if ($HirekTMB['ORLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", ORLeiras='".$HirekTMB['ORLeiras']."'";} 
              else {$UpdateStr .= " ORLeiras='".$HirekTMB['ORLeiras']."'";}}
            if ($HirekTMB['OKulcszsavak']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKulcszsavak='".$HirekTMB['OKulcszsavak']."'";}
              else {$UpdateStr .= " OKulcszsavak='".$HirekTMB['OKulcszsavak']."'";}}
            if ($HirekTMB['OPrioritas']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OPrioritas='".$HirekTMB['OPrioritas']."'";} 
              else {$UpdateStr .= " OPrioritas='".$HirekTMB['OPrioritas']."'";}}
            if ($katid>0) { if ($UpdateStr>'') {$UpdateStr .= ", OSzulo=$katid";} 
              else {$UpdateStr .= " OSzulo=$katid";}}
            if ($UpdateStr>'') {$UpdateStr .= ", ODatum=NOW()";}
            // Az ONev és az OURL változatlan. Az OTipus és az OSzulo az integritás megörzése érdekében nem változhat.
            $UpdateStr = "UPDATE oldal SET $UpdateStr WHERE id=$id";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 19 ";} 

            if ($HirekTMB['OTartalom']>'') {
              $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='".$HirekTMB['OTartalom']."' WHERE id=$id"; 
              if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 20 ";} 
            }
           // Ha nincs, akkor az új híroldalt felvesszük
          } else {
            mysqli_free_result($result);

            $InsertIntoStr = "INSERT INTO oldal (ONev, OURL, OKep, ORLeiras, OKulcszsavak, OTipus, OSzulo, OPrioritas, ODatum)"
                           . "VALUES ('".$HirekTMB['ONev']."','".$tiszta_OURL."','"
                           .$HirekTMB['OKep']."','".$HirekTMB['ORLeiras']."','".$HirekTMB['OKulcszsavak']."',".$HirekTMB['OTipus'].",".$katid
                           .",".$HirekTMB['OPrioritas'].", NOW())";  
            if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 21 ";}  else { $ID1= mysqli_insert_id($MySqliLink);} 

            $InsertIntoStr = "INSERT INTO oldal_tartalom (Oid, OTartalom) "
                           . "VALUES ($ID1, '".$HirekTMB['OTartalom']."')";
            if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 22 ";} 
          }
        } else {
          mysqli_free_result($result);
          $ErrorStr = "Ismeretlen szülőoldal!";
          echo "<p class='Error1'>A '".$HirekTMB['ONev']. "' szülőkategóriája nem létezik.</p>";
        }
      }
    }
  }
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A hírek adatai feltöltve.";}
  return $ErrorStr;
}



//------------------------------------------------------------------------------------------------------------------
// KÉPEK JELLEMZŐINEK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVKepJellemzoBetolt()
{
  global $MySqliLink, $AruhazNev ; 
  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(KepFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".KepFile." állományt nem lehet megnyitni!";}

   $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'KNev' => -1 ,'KURL' => -1 ,'KLeiras' => -1 ,'KSorszam' => -1 );
   $InitTMB = array('id' => 0 ,'ONev' => '','KNev' => '' ,'KURL' => '','KLeiras' => ''  ,'KSorszam' => 1 );

  $i=0;

  foreach ($FSorok as $FSor) {
  //Fejléc kezelése
    if ($i==0) {
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v);  
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='KNev') {$FejlecTMB['KNev']=$j;}
        if($v=='KURL') {$FejlecTMB['KURL']=$j;}
        if($v=='KLeiras') {$FejlecTMB['KLeiras']=$j;}
        if($v=='KSorszam') {$FejlecTMB['KSorszam']=$j;} 
        $j++;
      } $i=1;
    } else {
      $KepTMB = $InitTMB;
      $KepStrTMB = $FSor;
      $j=0; 
      foreach ($KepStrTMB as $v) { 
        $v=trim($v); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$KepTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)     {$KepTMB['ONev']     = $v;  }
        if ($FejlecTMB['KNev']==$j)     {$KepTMB['KNev']     = $v;}
        if ($FejlecTMB['KURL']==$j)     {$KepTMB['KURL']     = $v;}
        if ($FejlecTMB['KLeiras']==$j)  {$KepTMB['KLeiras']  = $v;}
        if ($FejlecTMB['KSorszam']==$j) {$KepTMB['KSorszam'] = $v;} 
        $j++;
      } 
      if (2 > strlen($KepTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév! ';}   //Nincs oldalnév
      if (2 > strlen($KepTMB['KURL'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó kép fájlnév!:'.$KepTMB['KURL'].";";} // Nincs URL

      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($KepTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);

        // A gazdaoldal OURL-jének keresése 
        // Névből URL
        if ($AruhazNev == $KepTMB['ONev']) {$KepTMB['ONev']='';} // A kezdőlapnál nincs $f0
        $szulo_OURL = strtolower(trim($KepTMB['ONev']));
        $szulo_OURL = URLTisztit($szulo_OURL);
        $SelectStr  = "SELECT id FROM oldal WHERE OURL='$szulo_OURL' LIMIT 1"; 
        $result     = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 44");
        $rowDB      = mysqli_num_rows($result);


        // A szülőolda létezik
        if ($rowDB > 0) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
          $Oid = $row['id'];         

          // Van már adott néven kép???
          $SelectStr = "SELECT id FROM kep WHERE Oid='".$Oid."' and KSorszam='".$KepTMB['KSorszam']."' LIMIT 1"; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 45");
          $rowDB = mysqli_num_rows($result);
          if ($rowDB > 0) 
           // Ha van, akkor adatait módosítjuk
          {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
            $id = $row['id'];
            $UpdateStr = "";
            if ($KepTMB['KNev']>'') { if ($UpdateStr>'') {$UpdateStr .= ", KNev='".$KepTMB['KNev']."'";} 
              else {$UpdateStr .= " KNev='".$KepTMB['KNev']."'";}}
            if ($KepTMB['KURL']>'') { if ($UpdateStr>'') {$UpdateStr .= ", KURL='".$KepTMB['KURL']."'";} 
              else {$UpdateStr .= " KURL='".$KepTMB['KURL']."'";}}
            if ($KepTMB['KLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", KLeiras='".$KepTMB['KLeiras']."'";} 
              else {$UpdateStr .= " KLeiras='".$KepTMB['KLeiras']."'";}}

            // Az ONev és a KSorszam változatlan az OSzulo az integritás megörzése érdekében nem változhat.
            $UpdateStr = "UPDATE kep SET $UpdateStr WHERE id=$id";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 46 ";}
          } else {
             mysqli_free_result($result);

            // A következő sorszám lekérdezése
            $SelectStr = "SELECT KSorszam FROM kep WHERE Oid=$Oid ORDER BY KSorszam DESC LIMIT 1"; 
            $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 47");
            $rowDB = mysqli_num_rows($result);
            if ($rowDB > 0) 
            {
             // Ha van, akkor adatait módosítjuk
              $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  mysqli_free_result($result);
              $KSorszam = $row['KSorszam']; $KSorszam++;
            } else {
               mysqli_free_result($result);
              $KSorszam  = 1;
            }

            $InsertIntoStr = "INSERT INTO kep (Oid, KNev, KURL, KLeiras, KSorszam)  "
                          . "VALUES (".$Oid.",'".$KepTMB['KNev']."','".$KepTMB['KURL']
                          ."','".$KepTMB['KLeiras']."',".$KSorszam.");";
            if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 48 ";}
          }
        } else {
          mysqli_free_result($result);
          echo "<h1>Ismeretlen szülőoldal : ".$KepTMB['ONev']."</h1>";
        }
      }
    }
  }
  // Ha nem történt hiba, akkor egységes üzenetet küldünk
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A képek jellemzői feltöltve.";}
  return $ErrorStr; 
}


//------------------------------------------------------------------------------------------------------------------
// TERMÉK JELLEMZŐK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVTermekJellemzoBetolt()
{
 global $MySqliLink; 
  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(TermekJellemzoFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".TermekJellemzoFile." állományt nem lehet megnyitni!";}

  $i=0;
  foreach ($FSorok as $FSor) {
    $JSorSzam = 0;
    if ($i==0) {
      //Fejléc beolvasása
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t"); 
        $FejlecTMB[$j]=$v;
        $j++;
      } $i=1;
    } else {
      //Értékek beolvasása
      $JellemzoStrTMB = $FSor;
      $j1=0; 
      foreach ($JellemzoStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        $JellemzoTMB[$j1] = $v;
        $j1++;    
      }
      $TermekNev = $JellemzoStrTMB[0]; 
      if (2 > strlen($TermekNev)) {$ErrorStr = $ErrorStr.'ERR: Hiányzó terméknév!';} // Nincs terméknév 

      if ($ErrorStr=='') {
        // A termék létezik???
        $SelectStr = "SELECT id FROM oldal WHERE ONev='".$TermekNev."' LIMIT 1"; 
        $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 39");
        $rowDB     = mysqli_num_rows($result);
        if ($rowDB>0) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
          $Oid = $row['id'];
          
          for ($tj=1; $tj<$j; $tj++) {
            $JellemzoNev   = $FejlecStrTmb[$tj]; 
            $JellemzoErtek = $JellemzoTMB[$tj]; 
            // A jellemző értéke is meg van adva???
            if ($JellemzoErtek>'') {
              // A termékhez adott jellemző már létezik??
              $SelectStr = "SELECT id FROM termek_jellemzo WHERE Oid=$Oid and JNev='$JellemzoNev' LIMIT 1"; 
              $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 40");
              $rowDB     = mysqli_num_rows($result);
              if ($rowDB>0) {
                // Már létezik
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
                $Jid = $row['id'];
                
                $UpdateStr = "UPDATE termek_jellemzo SET JErtek='$JellemzoErtek' WHERE id=$Jid";
                if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 41 ";}

              } else {    
                // Létre kell hozni
                mysqli_free_result($result);

                // A legnagyobb foglalt sorszám lekérdezése
                $SelectStr = "SELECT JSorszam FROM termek_jellemzo WHERE Oid=$Oid ORDER by JSorszam DESC LIMIT 1"; 
                $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 42");
                $row       = mysqli_fetch_array($result, MYSQLI_ASSOC);mysqli_free_result($result);
                $JSorSzam  = $row['JSorszam']; $JSorSzam++;

                $InsertIntoStr = "INSERT INTO termek_jellemzo (Oid, JNev, JErtek, JSorszam)"
                               . "VALUES ($Oid,'".$JellemzoNev."','".$JellemzoErtek."',$JSorSzam)";
                if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 43 ";} 
              }
            }
          }
        } else {
          mysqli_free_result($result);
        }
      } else { $ErrorStr = $ErrorStr.'ERR02:'.$TermekNev.";"; } // Nem létező termék
    }
  }
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A termékek jellemzői feltöltve.";}
  return $ErrorStr; 
}



//------------------------------------------------------------------------------------------------------------------
// TERMÉKEK FELTÖLTÉSE CSV FÁJLBÓL
//------------------------------------------------------------------------------------------------------------------

function CSVTermekBetolt()
{
 global $MySqliLink; 

  $ErrorStr = '';
  $_SOREMELES = chr(13).chr(10);
  $i = 0;
  $FSorok = array(); 
  $rowCT = 0;
  if (($handle = fopen(TermekFile, "r")) !== FALSE) {
    while (($AdatTMB = fgetcsv($handle))  !== FALSE) {
        $FSorok[$rowCT] = $AdatTMB;
        $rowCT++;
    }
    fclose($handle);
  } else { $ErrorStr   = "ERR: A ".TermekFile." állományt nem lehet megnyitni!";}

  $FejlecTMB = array('id' => -1 ,'ONev' => -1 ,'OKep' => -1 ,'ORLeiras' => -1 ,'OKulcszsavak' => -1 ,'OTipus' => -1 ,
     'OSzulo' => -1,'OPrioritas' => -1 , 'OTartalom' => -1,'TAr' => -1 , 'TSzorzo' => -1,'TKod' => -1 , 
     'TtulNev' => -1 , 'TtulErt' => -1, 'TSzalKlts' => -1, 'TSzallit' => -1 , 'TLeiras' => -1  );
  $InitTMB = array('id' => 0 ,'ONev' => '','OKep' => '','ORLeiras' => ''  ,'OKulcszsavak' => '' ,'OTipus' => 3,
     'OSzulo' => '','OPrioritas' => 1 , 'OTartalom' => '','TAr' => '' , 'TSzorzo' => '','TKod' => '' ,  
     'TtulNev' => '' , 'TtulErt' => '', 'TSzalKlts' => '', 'TSzallit' => 10 , 'TLeiras' => '' );
  $i=0;
  foreach ($FSorok as $FSor) {
   if ($i==0) {
      //Fejléc kezelése
      $FejlecStrTmb = $FSor;
      $j=0;
      foreach ($FejlecStrTmb as $v) { 
        $v=trim($v,"' \t"); 
        if($v=='id') {$FejlecTMB['id']=$j;}
        if($v=='ONev') {$FejlecTMB['ONev']=$j;}
        if($v=='OKep') {$FejlecTMB['OKep']=$j;}
        if($v=='ORLeiras') {$FejlecTMB['ORLeiras']=$j;}
        if($v=='OKulcszsavak') {$FejlecTMB['OKulcszsavak']=$j;}
        if($v=='OSzulo') {$FejlecTMB['OSzulo']=$j;}
        if($v=='OPrioritas') {$FejlecTMB['OPrioritas']=$j;}
        if($v=='OTartalom') {$FejlecTMB['OTartalom']=$j;}

        if($v=='TAr') {$FejlecTMB['TAr']=$j;}
        if($v=='TSzorzo') {$FejlecTMB['TSzorzo']=$j;}
        if($v=='TKod') {$FejlecTMB['TKod']=$j;}
        if($v=='TtulNev') {$FejlecTMB['TtulNev']=$j;}
        if($v=='TtulErt') {$FejlecTMB['TtulErt']=$j;}
        if($v=='TSzalKlts') {$FejlecTMB['TSzalKlts']=$j;}

        if($v=='TSzallit') {$FejlecTMB['TSzallit']=$j;}
        if($v=='TLeiras') {$FejlecTMB['TLeiras']=$j;}
        $j++;
      } $i=1;
    } else {
      //Tartalom kezelése
      $TermekTMB = $InitTMB;
      $TermekStrTMB = $FSor;
      $j=0; 
      foreach ($TermekStrTMB as $v) { 
        $v=trim($v,"' \t"); 
        if ($FejlecTMB['id']==$j)  {if ($v>0) {$TermekTMB['id'] = $v;} }
        if ($FejlecTMB['ONev']==$j)  {$TermekTMB['ONev'] = $v;  }
        if ($FejlecTMB['OKep']==$j)  {$TermekTMB['OKep'] = $v;}
        if ($FejlecTMB['ORLeiras']==$j)  {$TermekTMB['ORLeiras'] = $v;}
        if ($FejlecTMB['OKulcszsavak']==$j)  {$TermekTMB['OKulcszsavak'] = $v;}
        if ($FejlecTMB['OSzulo']==$j)  {$TermekTMB['OSzulo'] = $v;}
        if ($FejlecTMB['OPrioritas']==$j)  {$TermekTMB['OPrioritas'] = $v;}
        if ($FejlecTMB['OTartalom']==$j)  {$TermekTMB['OTartalom'] = tiszta_szov($v);}
        if ($FejlecTMB['TAr']==$j)  {$TermekTMB['TAr'] = $v;}
        if ($FejlecTMB['TSzorzo']==$j)  {$TermekTMB['TSzorzo'] = $v;}
        if ($FejlecTMB['TKod']==$j)  {$TermekTMB['TKod'] = $v;}
        if ($FejlecTMB['TtulNev']==$j)  {$TermekTMB['TtulNev'] = $v;}
        if ($FejlecTMB['TtulErt']==$j)  {$TermekTMB['TtulErt'] = $v;}
        if ($FejlecTMB['TSzalKlts']==$j)  {$TermekTMB['TSzalKlts'] = $v;}
        if ($FejlecTMB['TSzallit']==$j)  {$TermekTMB['TSzallit'] = $v;}
        if ($FejlecTMB['TLeiras']==$j)  {$TermekTMB['TLeiras'] = $v;}
        $j++;
      }

      if (2 > strlen($TermekTMB['ONev'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó oldalnév!';} // Nincs oldalnév 
      if (2 > strlen($TermekTMB['OSzulo'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó szülőoldal! '.$TermekTMB['OSzulo'].";";} // Nincs szülőoldal
      if (2 > strlen($TermekTMB['TKod'])) {$ErrorStr = $ErrorStr.'ERR: Hiányzó termékkód! '.$TermekTMB['TKod'].";";} // Nincs kód
      if ($ErrorStr == '') {
        // OURL megtisztítása
        $tiszta_OURL = strtolower(trim($TermekTMB['ONev']));
        $tiszta_OURL = URLTisztit($tiszta_OURL);
        // Szülő oldal OURL-jének keresése 
        // Névből URL
        $szulo_OURL = strtolower(trim($TermekTMB['OSzulo']));
        $szulo_OURL = URLTisztit($szulo_OURL);
        $SelectStr  = "SELECT id FROM oldal WHERE OURL='$szulo_OURL' LIMIT 1"; 
        $result     = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 23");
        $rowDB      = mysqli_num_rows($result);
        if ($rowDB > 0) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
          $Szid = $row['id'];
          
          // Van már adott néven termék???
          $SelectStr = "SELECT id FROM oldal WHERE ONev='".$TermekTMB['ONev']."' LIMIT 1"; 
          $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 24");
          $rowDB     = mysqli_num_rows($result);
          if ($rowDB > 0) {
           // Ha van, akkor adatait módosítjuk          
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
            $Oid = $row['id'];
            $UpdateStr = "";
            if ($TermekTMB['OKep']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKep='".$TermekTMB['OKep']."'";} 
              else {$UpdateStr .= " OKep='".$TermekTMB['OKep']."'";}}
            if ($TermekTMB['ORLeiras']>'') { if ($UpdateStr>'') {$UpdateStr .= ", ORLeiras='".$TermekTMB['ORLeiras']."'";} 
              else {$UpdateStr .= " ORLeiras='".$TermekTMB['ORLeiras']."'";}}
            if ($TermekTMB['OKulcszsavak']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OKulcszsavak='".$TermekTMB['OKulcszsavak']."'";}
              else {$UpdateStr .= " OKulcszsavak='".$TermekTMB['OKulcszsavak']."'";}}
            if ($TermekTMB['OPrioritas']>'') { if ($UpdateStr>'') {$UpdateStr .= ", OPrioritas='".$TermekTMB['OPrioritas']."'";} 
              else {$UpdateStr .= " OPrioritas='".$TermekTMB['OPrioritas']."'";}}
            if ($Szid>0) { if ($UpdateStr>'') {$UpdateStr .= ", OSzulo=$Szid";} 
              else {$UpdateStr .= " OSzulo=$Szid";}}

            if ($UpdateStr>'') {$UpdateStr .= ", ODatum=NOW()";}
            // Az ONev és az OURL változatlan. Az OTipus és az OSzulo az integritás megörzése érdekében nem változhat.
            $UpdateStr = "UPDATE oldal SET $UpdateStr WHERE id=$Oid";
            if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 25 ";}

            $SelectStr = "SELECT id FROM oldal_tartalom WHERE Oid='$Oid' LIMIT 1"; 
            $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 26");
            $rowDB = mysqli_num_rows($result);

            if ($TermekTMB['OTartalom']>'') {
              if ($rowDB>0) {
                $UpdateStr = "UPDATE oldal_tartalom SET OTartalom='".$TermekTMB['OTartalom']."' WHERE Oid=$Oid";
                if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 27 ";}
              } else {
                $InsertIntoStr = "INSERT INTO oldal_tartalom (Oid, OTartalom) "
                               . "VALUES ($Oid,'".$TermekTMB['OTartalom']."')";
                if (!mysqli_query($MySqliLink,$InsertIntoStr)) {echo " CSV hiba 28 ";} 
              }
            }
            $SelectStr = "SELECT id FROM termek_leiras WHERE Oid='$Oid' LIMIT 1"; 

            $result    = mysqli_query($MySqliLink,$SelectStr) OR die("CSV hiba 29");
            $rowDB = mysqli_num_rows($result);
            if ($TermekTMB['TLeiras']>'') {
              if ($rowDB>0) {
                $UpdateStr = "UPDATE termek_leiras SET TLeiras='".$TermekTMB['TLeiras']."' WHERE Oid=$Oid";
                if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 30 ";}
              } else {
                $InsertIntoStr = "INSERT INTO termek_leiras (Oid, TLeiras) "
                               . "VALUES ($Oid,'".$TermekTMB['TLeiras']."')";
                if (!mysqli_query($MySqliLink,$InsertIntoStr)) {echo " CSV hiba 31 ";}
              }
            }
            $SelectStr = "SELECT id FROM termek WHERE TKod='".$TermekTMB['TKod']."' LIMIT 1"; 
            $result    = mysqli_query($MySqliLink,$SelectStr)  OR die("CSV hiba 32");
            $rowDB     = mysqli_num_rows($result);
            if ($rowDB>0) {
              $UpdateStr = '';
              if ($TermekTMB['TAr']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TAr='".$TermekTMB['TAr']."'";} 
                 else {$UpdateStr .= " TAr='".$TermekTMB['TAr']."'";}}
              if ($TermekTMB['TSzorzo']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TSzorzo='".$TermekTMB['TSzorzo']."'";} 
                 else {$UpdateStr .= " TSzorzo='".$TermekTMB['TSzorzo']."'";}}
              if ($TermekTMB['TtulNev']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TtulNev='".$TermekTMB['TtulNev']."'";} 
                 else {$UpdateStr .= " TtulNev='".$TermekTMB['TtulNev']."'";}}
              if ($TermekTMB['TtulErt']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TtulErt='".$TermekTMB['TtulErt']."'";} 
                 else {$UpdateStr .= " TtulErt='".$TermekTMB['TtulErt']."'";}}
              if ($TermekTMB['TSzalKlts']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TSzalKlts='".$TermekTMB['TSzalKlts']."'";} 
                 else {$UpdateStr .= " TSzalKlts='".$TermekTMB['TSzalKlts']."'";}}
              if ($TermekTMB['TSzallit']>'') { if ($UpdateStr>'') {$UpdateStr .= ", TSzallit='".$TermekTMB['TSzallit']."'";} 
                 else {$UpdateStr .= " TSzallit='".$TermekTMB['TSzallit']."'";}}
              $UpdateStr = "UPDATE termek SET $UpdateStr WHERE TKod='".$TermekTMB['TKod']."'";
              if (!mysqli_query($MySqliLink,$UpdateStr))  {echo " CSV hiba 33 ";}
            } else {
              $InsertIntoStr = "INSERT INTO termek (Oid, TAr, TSzorzo, TKod, TtulNev, TtulErt, TSzalKlts, TSzallit)"
                             . "VALUES ($Oid,".$TermekTMB['TAr'].",".$TermekTMB['TSzorzo'].",'"
                              .$TermekTMB['TKod']."','".$TermekTMB['TtulNev']."','".$TermekTMB['TtulErt']."','"
                              .$TermekTMB['TSzalKlts']."',".$TermekTMB['TSzallit'].")";
              if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 34 ";} else { $ID1= mysqli_insert_id($MySqliLink);}
            }

            // Ha nincs, akkor az új terméket felvesszük
          } else {
             mysqli_free_result($result);
             $InsertIntoStr = "INSERT INTO oldal (ONev, OURL, OKep, ORLeiras, OKulcszsavak, OTipus, OSzulo, OPrioritas, ODatum)"
                            . "VALUES ('".$TermekTMB['ONev']."','".$tiszta_OURL."','"
                            .$TermekTMB['OKep']."','".$TermekTMB['ORLeiras']."','".$TermekTMB['OKulcszsavak']."',".$TermekTMB['OTipus'].","
                            .$Szid.",".$TermekTMB['OPrioritas'].", NOW())";
             if (!mysqli_query($MySqliLink,$InsertIntoStr)) {echo " CSV hiba 35 ";} else { $ID1= mysqli_insert_id($MySqliLink);}
 
             $InsertIntoStr = "INSERT INTO oldal_tartalom (Oid, OTartalom) "
                            . "VALUES ($ID1, '".$TermekTMB['OTartalom']."')";
             if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 36 ";}

             $InsertIntoStr = "INSERT INTO termek_leiras (Oid, TLeiras) "
                            . "VALUES ($ID1, '".$TermekTMB['TLeiras']."')";
             if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 37 ";}
 
             $InsertIntoStr = "INSERT INTO termek (Oid, TAr, TSzorzo, TKod, TtulNev, TtulErt, TSzalKlts, TSzallit)"
                            . "VALUES ($ID1,".$TermekTMB['TAr'].",".$TermekTMB['TSzorzo'].",'"
                            .$TermekTMB['TKod']."','".$TermekTMB['TtulNev']."','".$TermekTMB['TtulErt']."','"
                            .$TermekTMB['TSzalKlts']."',".$TermekTMB['TSzallit'].")";
             if (!mysqli_query($MySqliLink,$InsertIntoStr))  {echo " CSV hiba 38 ";} else { $ID1= mysqli_insert_id($MySqliLink);} 
          }
       } else { 
          mysqli_free_result($result); 
          $ErrorStr = "Ismeretlen szülőoldal!";
          echo "<p class='Error1'>".$TermekTMB['ONev']. " szülőkategóriája nem létezik.</p>";
       }
      }
    }
  }
  if (strpos($ErrorStr,"ERR") === false) {$ErrorStr .= "A termékek adatai feltöltve.";}
  return $ErrorStr;
}







//------------------------------------------------------------------------------------------------------------------
// A CSV fájl feltöltése szerverre
//------------------------------------------------------------------------------------------------------------------
// A $_FILES globális tömbben található fájl feltöltése a szerver csv_cel könyvtárába
// A feltölt fájlok átnevezéssel szabványos neveket kapnak 
// A szabványos fájlnevek konstanskén vannak deklarálva (elől)

function CSV_Feltoltese()
{
  $CSVOK=false; $UploadErr='';
  //A feltöltsé feltételei csv kiterjesztés és típus,  2MB-nál kisebb méret
  $allowedExts = array("csv", "CSV");
  $temp        = explode(".", $_FILES["file"]["name"]); 
  $extension   = end($temp);
  if (($_FILES["file"]["type"] == "text/csv")
  && ($_FILES["file"]["size"] < 2000000)
  && in_array($extension, $allowedExts)) { 
    if ($_FILES["file"]["error"] > 0){$UploadErr = "Hiba kódja: " . $_FILES["file"]["error"] . "<br>";
    } else {
      if (file_exists("csv_cel/" . $_FILES["file"]["name"])) {
        move_uploaded_file($_FILES["file"]["tmp_name"],"csv_cel/" . $_FILES["file"]["name"]);
        $UploadErr =  "Felülírva: " .$_FILES["file"]["name"]; $KepOK=true;
      } else {
        move_uploaded_file($_FILES["file"]["tmp_name"],"csv_cel/" . $_FILES["file"]["name"]);
        $UploadErr =  "Feltöltve: ". $_FILES["file"]["name"]; $KepOK=true;
      }
      if ($_POST['submit_KategoriaFeltolt']      == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], KatFile);}
      if ($_POST['submit_AlkategoriaFeltolt']    == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], AlkatFile);}
      if ($_POST['submit_TermekFeltolt']         == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], TermekFile);}
      if ($_POST['submit_TermekJellemzoFeltolt'] == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], TermekJellemzoFile);}
      if ($_POST['submit_KepFeltolt']            == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], KepFile);}
      if ($_POST['submit_HirFeltolt']            == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], HirFile);}
      if ($_POST['submit_HirKatFeltolt']         == 'Feltöltés') { rename ('csv_cel/'.$_FILES["file"]["name"], HirKatFile);}
    }
  } else { $UploadErr = "Érvénytelen file."; }

  return $UploadErr;
}



//------------------------------------------------------------------------------------------------------------------
// A CSV fájl feltöltése szerverre
//------------------------------------------------------------------------------------------------------------------
// Feltöltés esetén a fájlnevek ellenőrzése után meg kell hívni
// a részfeladatokat (feltöltés, betöltés) megvalósító függbényeket

function Oldal_Feltotes($Oid,$funkcio)
{
  global $hozzaferes;
  global $MySqliLink, $f1, $f2, $f3, $f4, $f5; 
  global $AktOldal, $VisszaHidden, $OldalTipusok; 

  $CSVfielname = $_FILES["file"]["name"]; 
  //Ha a felhasználó a feltöltést választotta, és jó fájlnevet adott meg, akkor a csv fájlt feltöltjük
  if ((($_POST['submit_KategoriaFeltolt']  == 'Feltöltés') and ($CSVfielname == 'kategoriak.csv'))
  || (($_POST['submit_AlkategoriaFeltolt'] == 'Feltöltés') and ($CSVfielname == 'alkategoriak.csv'))
  || (($_POST['submit_TermekFeltolt']      == 'Feltöltés') and ($CSVfielname == 'termek.csv'))
  || (($_POST['submit_TermekJellemzoFeltolt'] == 'Feltöltés') and ($CSVfielname == 'termek_jellemzo.csv'))
  || (($_POST['submit_KepFeltolt']         == 'Feltöltés') and ($CSVfielname == 'kep.csv'))
  || (($_POST['submit_HirFeltolt']         == 'Feltöltés') and ($CSVfielname == 'hirek.csv'))
  || (($_POST['submit_HirKatFeltolt']      == 'Feltöltés') and ($CSVfielname == 'hirkategoriak.csv'))) 
  {
       $UploadErr = CSV_Feltoltese(); 
  } 
  //Ha a felhasználó a feltöltést választotta, de rossz fájlnevet adott meg, akkor hibajelzést kap
  if ((($_POST['submit_KategoriaFeltolt']  == 'Feltöltés') and ($CSVfielname != 'kategoriak.csv'))
  || (($_POST['submit_AlkategoriaFeltolt'] == 'Feltöltés') and ($CSVfielname != 'alkategoriak.csv'))
  || (($_POST['submit_TermekFeltolt']      == 'Feltöltés') and ($CSVfielname != 'termek.csv'))
  || (($_POST['submit_TermekJellemzoFeltolt'] == 'Feltöltés') and ($CSVfielname != 'termek_jellemzo.csv'))
  || (($_POST['submit_KepFeltolt']         == 'Feltöltés') and ($CSVfielname != 'kep.csv'))
  || (($_POST['submit_HirFeltolt']         == 'Feltöltés') and ($CSVfielname != 'hirek.csv'))
  || (($_POST['submit_HirKatFeltolt']      == 'Feltöltés') and ($CSVfielname != 'hirkategoriak.csv'))) 
  {
       $UploadErr .= "ERR: Hibás fájlnév: $CSVfielname"; 
  } 
  //Ha a csv fájl feltöltése hiba nélkül megtörtént, akkor tartalmát betöltjük az adatbázis megfelelő tábláiba
  if  (strpos($UploadErr,"ERR") === false) { 
    if ($_POST['submit_KategoriaFeltolt']      == 'Feltöltés') { $UploadErr = CSVKategoriaBetolt(); }
    if ($_POST['submit_AlkategoriaFeltolt']    == 'Feltöltés') { $UploadErr = CSVAlkategoriaBetolt(); }
    if ($_POST['submit_TermekFeltolt']         == 'Feltöltés') { $UploadErr = CSVTermekBetolt(); }
    if ($_POST['submit_TermekJellemzoFeltolt'] == 'Feltöltés') { $UploadErr = CSVTermekJellemzoBetolt(); }
    if ($_POST['submit_KepFeltolt']            == 'Feltöltés') { $UploadErr = CSVKepJellemzoBetolt(); }
    if ($_POST['submit_HirKatFeltolt']         == 'Feltöltés') { $UploadErr = CSVHirKategoriaBetolt(); }
    if ($_POST['submit_HirFeltolt']            == 'Feltöltés') { $UploadErr = CSVHirekBetolt(); }
  } else {
    $arrErr = array( "ERR:" => "", "Err:" => "");  $UploadErr  = strtr($UploadErr ,$arrErr); 
  }



//------------------------------------------------------------------------------------------------------------------
// FELTÖLTÉS ŰRLAPok megjelenítése
//------------------------------------------------------------------------------------------------------------------
// A feltöltés oldalon 7 db űrlap található, amelyek egy-egy oldaltípus és a képek adatainak 
// feltöltését teszik lehetővé.
// Minden űrlap lehetővé teszi egy fájl kitallózását és feltöltését
// Hiba esetén az admin hibajelzést kap

  $HTMLkod .= "<div id='DIVOldalFeltolt'>";

// Kategóriák feltöltése
  $HTMLkod .= "<div id='Form_KategoriaFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";

  $HTMLkod .= "<h2>1. Kategóriák feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_KategoriaFeltolt' class='label_1'>A 'kategoriak.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_KategoriaFeltolt' ><br> \n";

  if (($_POST['submit_KategoriaFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";}

  $HTMLkod .= "<input type='submit' name='submit_KategoriaFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";

// alkategóriák feltöltése
  $HTMLkod .= "<div id='Form_AlkategoriaFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>2. Alkategóriák feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_AlkategoriaFeltolt' class='label_1'>Az 'alkategoriak.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_AlkategoriaFeltolt' ><br>\n";
  if (($_POST['submit_AlkategoriaFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";} 
  $HTMLkod .= "<input type='submit' name='submit_AlkategoriaFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";

// termékek feltöltése
  $HTMLkod .= "<div id='Form_TermekFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>3. Termékek feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_TermekFeltolt' class='label_1'>A 'termek.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_TermekFeltolt' ><br>\n";
  if (($_POST['submit_TermekFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";} 
  $HTMLkod .= "<input type='submit' name='submit_TermekFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";

// termékek jellemzőinek feltöltése
  $HTMLkod .= "<div id='Form_TermekFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>4. Termékek jellemzők feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_TermekJellemzoFeltolt' class='label_1'>A 'termek_jellemzo.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_TermekJellemzoFeltolt' ><br>\n";
  if (($_POST['submit_TermekJellemzoFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";} 
  $HTMLkod .= "<input type='submit' name='submit_TermekJellemzoFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";



// Hírkategória feltöltése feltöltése
  $HTMLkod .= "<div id='Form_HirKatFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>5. Hírkategória feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_HirKatFeltolt' class='label_1'>A 'hirkategoriak.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_HirKatFeltolt' ><br>\n";
  if (($_POST['submit_HirKatFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";}
  $HTMLkod .= "<input type='submit' name='submit_HirKatFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";


// Hírek feltöltése feltöltése
  $HTMLkod .= "<div id='Form_HirFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>6. Hírek feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_HirFeltolt' class='label_1'>A 'hirek.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_HirFeltolt' ><br>\n";
  if (($_POST['submit_HirFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";} 
  $HTMLkod .= "<input type='submit' name='submit_HirFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";


// képek jellemzőinek feltöltése
  $HTMLkod .= "<div id='Form_KepFeltolt' class='Form_OldalFeltolt'>\n";
  $HTMLkod .= "<form action='#' method='post' enctype='multipart/form-data'> $VisszaHidden ";
  $HTMLkod .= "<h2>7. Képek feltöltése</h2>\n";
  $HTMLkod .= "<label for='file_KepFeltolt' class='label_1'>A 'kep.csv' kitallózása</label><br>\n";
  $HTMLkod .= "<input type='file' name='file' id='file_KepFeltolt' ><br>\n";
  if (($_POST['submit_KepFeltolt'] == 'Feltöltés') and ($UploadErr>'')) {$HTMLkod .= "<p class='ErrUzenet'>$UploadErr</p>\n";} 
  $HTMLkod .= "<input type='submit' name='submit_KepFeltolt' value='Feltöltés' style='float:right;'>\n";
  $HTMLkod .= "</form> </div>\n\n";




  $HTMLkod .= "</div> ";

  echo $HTMLkod; 
}


?>
