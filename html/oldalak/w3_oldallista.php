<?php
    

function Kiir_Oladllista()
{
  global $mm_felhasznalo, $MySqliLink, $AktOldal; 
  global $f1, $f2, $f3, $f4;
  global $OldalTipusok;

  $RekordPerOldal = 15;
  $OldalMut = $f1;
  $ValTipus = $f2;
  $ValSzulo = $f3; 

  if ($_POST['OldalMut'] > '')  {$OldalMut = $_POST['OldalMut']; }

  if ($_POST['TipValszt'] > '') {$ValTipus = $_POST['TipValszt']; 
    if ($_POST['SzuloValaszt'] > '')  {$ValSzulo = $_POST['SzuloValaszt']; }
  }

  // A választott szülőoldal ID-jének lekérdezése
  $SelectStr  = "SELECT id FROM oldal WHERE ONev='$ValSzulo' LIMIT 1"; 
  $result     = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OL 01 ");
  $row        = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $ValSzuloID = $row['id'];   

  $HTMLkod .=  '<form action="?f0=szerkeszt&f1=0" method="post">';
  $HTMLkod .=  "<fieldset> <legend>Találatok szűkítésa:</legend>\n";

  // Az oldal típusának kiválasztása
  $HTMLkod .=  "<label for='TipValszt'>Típus: </label>
    <select name='TipValszt' id='TipValszt' size='1'>\n";
  if ($ValTipus=='Minden')      {$HTMLkod .=  "<option value='Minden' selected>Minden</option>\n";} 
      else {$HTMLkod .=  "<option value='Minden'>Minden</option>\n";} 
  if ($ValTipus=='Kezdolap')    {$HTMLkod .=  "<option value='Kezdolap' selected>Kezdőlap</option>\n";}
      else {$HTMLkod .=  '<option value="Kezdolap">Kezdőlap</option>';}
  if ($ValTipus=='Kategoria')   {$HTMLkod .=  "<option value='Kategoria' selected>Kategória</option>\n";}
      else  {$HTMLkod .=  "<option value='Kategoria'>Kategória</option>\n";}
  if ($ValTipus=='Alkategoria') {$HTMLkod .=  "<option value='Alkategoria' selected>Alkategória</option>\n";}
      else {$HTMLkod .=  "<option value='Alkategoria'>Alkategória</option>\n";}
  if ($ValTipus=='Termek')      {$HTMLkod .=  "<option value='Termek' selected>Termék</option>\n";}
      else {$HTMLkod .=  "<option value='Termek'>Termék</option>\n";}
  if ($ValTipus=='Hirkategoria'){$HTMLkod .=  "<option value='Hirkategoria' selected>Hírkategória</option>\n";}
      else {$HTMLkod .=  "<option value='Hirkategoria'>Hírkategória</option>\n";}
  if ($ValTipus=='HirOldal')    {$HTMLkod .=  "<option value='HirOldal' selected>Híroldal</option>\n";}
      else {$HTMLkod .=  "<option value='HirOldal'>Híroldal</option>\n";}

  $HTMLkod .=  "</select>\n";

  $TipMut = -1;
  if (2 < strlen($ValTipus)) {$TipMut = array_search($ValTipus, $OldalTipusok);}

  // A szülőlista összeállítása
  $SzuloTipMut = -1;
  if ($TipMut>0) {
    switch ($TipMut) {
      case "1":   $SzuloTipMut=0;  break;
      case "2":   $SzuloTipMut=1;  break;
      case "3":   $SzuloTipMut=2;  break;
      case "10":  $SzuloTipMut=0;  break;
      case "11":  $SzuloTipMut=10; break;
    }
  }

  $SzuloOk = 0; 
  if ($SzuloTipMut>-1) {
    $SzuloLista  = '';
    $SelectStr   = "SELECT * FROM oldal WHERE OTipus=$SzuloTipMut order by ONev "; 
    $result      = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OL 02 ");
    $SzuloLista .= "<option value='Mind' >Mind</option>\n";
    while($row = mysqli_fetch_array($result))
    {
     if ($row['ONev']==$ValSzulo) 
        {$SzuloLista .= "<option value='".$row['ONev']."' selected>".$row['ONev']."</option>\n"; $SzuloOk = 1;}
     else 
        {$SzuloLista .= "<option value='".$row['ONev']."' >".$row['ONev']."</option>\n";}
    }
    mysqli_free_result($result);
    $SzuloLista = "<label for='SzuloValaszt'>Szülő oldal: </label>
          <select name='SzuloValaszt' id='SzuloValaszt' size='1'>". $SzuloLista."</select>\n";
  }
  $HTMLkod .=  $SzuloLista;  
  $HTMLkod .=  "<input type='submit' name='submit1' value='Mehet!'>  </fieldset>\n";


// ----------------- LISTA ÖSSZEÁLLÍTÁSA
  $HTMLkod .=  '<fieldset> <legend>Oldallista:</legend>';
  $where = 'OTipus<50 ';
  if ($TipMut>-1) {$where .= "and OTipus=$TipMut";}
  if (($ValSzuloID>-1) and ($SzuloOk==1)) {$where .= " and OSzulo=$ValSzuloID";}
  if ($where>'') {$where = "WHERE ".$where;}

  $SelectStr = "SELECT count(*) FROM oldal $where"; 
  $result    = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OL 03 ");
  $row       = mysqli_fetch_row($result); mysqli_free_result($result);

  $MaxElemSzam  = $row[0];
  $MaxOldalSzam = ceil ($MaxElemSzam / $RekordPerOldal );
  $OldalElsoElemSzama = $OldalMut * $RekordPerOldal;  
  $AktXOldal    = (ceil (($OldalMut+1) / 10)-1);
// < 0 1 2 3 4 5 6 7 8 9 >

  if (($OldalElsoElemSzama+$RekordPerOldal) < $MaxElemSzam) {$db = $RekordPerOldal;} 
   else { $db = $MaxElemSzam - $OldalElsoElemSzama;}

  $OValasztHML = '';
  if (10 <= $OldalMut ) 
     {$Omin=$OldalMut-10; 
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$Omin&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut'> &lt;&lt; 
                           </a>"; }
  if (0 < $OldalMut ) 
     {$Omin=$OldalMut-1; 
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$Omin&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut' > &lt; </a> "; }
  for ($i=0; $i<10; $i++) {
     $AktOldalCT =   $i + $AktXOldal * $RekordPerOldal;
     if ($AktOldalCT < $MaxOldalSzam) {
       if ($AktOldalCT== $OldalMut ) {
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$AktOldalCT&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut' class='Kivalasztott'>  
                 <b><u>$AktOldalCT</u></b>  </a>";}
       else {
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$AktOldalCT&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut' >  
                 $AktOldalCT  </a>";}
     }
  }
  if ($MaxOldalSzam > $OldalMut+1 ) { $Okov=$OldalMut+1; 
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$Okov&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut' > <i>  &gt;</i> </a>"; }
  if ($MaxOldalSzam > $OldalMut+10 ) { $Okov=$OldalMut+10; 
        $OValasztHML .= "<a href='?f0=szerkeszt&f1=$Okov&f2=$ValTipus&f3=$ValSzulo&f4=$OldalMut' >
                        <i> &gt;&gt;</i> </a>"; }

  $HTMLkod = "<div id='DIVigazit'><div id='Div_OLista'><div id='LapValaszt'>".$OValasztHML."</div>". $HTMLkod;
  $HTMLkod .=  "<table><tr><th>Ssz</th><th>Oldal</th><th>Módosítás</th><th>Törlés</th><th>Másolás</th><th>Típus</th><th>Szülő</th></tr>\n";
  $SSz = $OldalElsoElemSzama;

  if (($OldalElsoElemSzama+$RekordPerOldal) < $MaxElemSzam) {$db = $RekordPerOldal;} else { $db = $MaxElemSzam - $OldalElsoElemSzama;}

  $SelectStr = "SELECT * FROM oldal $where order by ONev limit $OldalElsoElemSzama, $db "; 
  $result = mysqli_query($MySqliLink,$SelectStr) OR die("Hiba OL 04 ");
  while($row = mysqli_fetch_array($result))
  {
    $Oid      = $row['id']; 
    $ONev     = $row['ONev'];  
    $OURL     = $row['OURL']; 
    $OKep     = $row['OKep']; 
    $ORLeiras = $row['ORLeiras']; 
    $OKulcszsavak = $row['OKulcszsavak']; 
    $OTipus   = $row['OTipus']; 
    $OSzulo   = $row['OSzulo']; 
    $OPrioritas = $row['OPrioritas']; 
    $ODatum   = $row['ODatum'];

    if ($OSzulo>1) {
        $SelectStr1 = "SELECT ONev FROM oldal WHERE id=$OSzulo LIMIT 1"; 
        $result1    = mysqli_query($MySqliLink,$SelectStr1) OR die("Hiba OL 05 ");
        $row1       = mysqli_fetch_array($result1, MYSQLI_ASSOC);   mysqli_free_result($result1);
        $SzNev = $row1['ONev'];
    } else {
        $SzNev = 'Kezdőlap';
    }

    $HTMLkod .=  "<tr><td>".$SSz++.".
                  </td><td>$ONev
                  </td><td><a href='?f0=szerkeszt&f1=Modosit&f2=".$row['id']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                   class='aModosit' >Módosítás</a>
                  </td><td><a href='?f0=szerkeszt&f1=Torol&f2=".$row['ONev']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                   class='aTorol' >Törlés</a>
                  </td><td><a href='?f0=szerkeszt&f1=Masol&f2=".$row['id']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                   class='aMasol' >Másolás</a>
                  </td><td>".$OldalTipusok[$OTipus]."
                  </td><td>$SzNev
                  </td></tr>\n";
  }
  mysqli_free_result($result);
  $HTMLkod .=  "</table><br>";
  $HTMLkod .=  "<a href='?f0=szerkeszt&f1=UjOldal&f2=".$row['id']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                class='aUjo' >Új oldal</a>";

  $HTMLkod .=  "<a href='?f0=szerkeszt&f1=Feltotes&f2=".$row['id']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                class='aTomeges' >Tömeges beállítás</a>";

  $HTMLkod .=  "<a href='?f0=szerkeszt&f1=Mentes&f2=".$row['id']."&f3=$ValTipus&f4=$ValSzulo&f5=$OldalMut'
                class='aMentes' >Adatok mentése</a>";

  $HTMLkod .=  "</fieldset>\n";

  $HTMLkod .=  "</form></div></div>\n";
  echo $HTMLkod;
}

?>
