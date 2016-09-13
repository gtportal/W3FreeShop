<?php
error_reporting(0);
//   ------------------------------- W3 PLÁZA --------------------------------------------
// Munkamenet indítása, munamenetazonosító lekérdezése
session_start();
$mm_azon   = session_id(); 
$FejlecStr =''; $VisszaHidden ='';
// Munkamenetváltozók kezelése
if (!isset($_SESSION['hozzaferesi_szint'])) {$_SESSION['hozzaferesi_szint'] = "1"; $feladat = "START";} 
$hozzaferes     = $_SESSION['hozzaferesi_szint'];
if (!isset($_SESSION['munkamenet_felhasznalo'])) {$_SESSION['munkamenet_felhasznalo'] = "";} 
$mm_felhasznalo = $_SESSION['munkamenet_felhasznalo'];
if (!isset($_SESSION['mm_captchaMut'])) {$_SESSION['mm_captchaMut'] = "";} 
$mm_captchaMut  = $_SESSION['mm_captchaMut'];
// A $_GET töbm elemeinek kezelése
$arr = array("'" => "", '"' => '', "," => "", ';' => '');
if (isset($_GET['f0'])) { $feladat = $_GET['f0'];} else { $feladat = '';} $feladat = strtr($feladat,$arr);
if (isset($_GET['f1'])) { $f1 = $_GET['f1']; } else { $f1 = '';} $f1 = strtr($f1,$arr);
if (isset($_GET['f2'])) { $f2 = $_GET['f2']; } else { $f2 = '';} $f2 = strtr($f2,$arr);
if (isset($_GET['f3'])) { $f3 = $_GET['f3']; } else { $f3 = '';} $f3 = strtr($f3,$arr);
if (isset($_GET['f4'])) { $f4 = $_GET['f4']; } else { $f4 = '';} $f4 = strtr($f4,$arr);
if (isset($_GET['f5'])) { $f5 = $_GET['f5']; } else { $f5 = '';} $f5 = strtr($f5,$arr);
// Adatbázis megnyitása, rendszerváltozók inicializálása
require_once("set/start.php");
// Az általános függvények beolvasása
require_once("set/w3_fgvek.php");
// A be- és kijelentkezés, valamint a regisztrációs űrlapok kezelése
require_once("oldalak/w3_regisztracio.php");  
require_once("oldalak/w3_bekijelentkezes.php");
     if ($_POST['form_RegUrlap'] > '')      {$Err_RegUrlap     = RegAdatModosit(); }
     if ($_POST['form_JelszModosit'] > '')  {$Err_JelszModosit = JelszoModosit();  }
// A megrendeléshez kapcsolódó függvények betöltése
require_once("oldalak/w3_rendeles.php");
// Az oldal adatainak globális tömbbe írása, oldalfüggő feljéctartalom összeállítása 
require_once("oldalak/w3_fejlec.php"); 
// A menü és a tartalom megjelenítéséhez szükséges függvények betöltése
require_once("oldalak/w3_menu.php");
require_once("oldalak/w3_tartalom.php");

if (!isset($_SESSION['szamlalo'])) {
    $_SESSION['szamlalo'] = 0;
    latogatasok_novel ();
} else {
    $_SESSION['szamlalo']++;   
}

?>

<!-- HTML 5 dokumentum -->
<!DOCTYPE html>
<html lang="hu">
<head>

<?php
// Ha a menu süti létezik, akkor étrékét beállítjuk az x javascript váltizóba
// Ha nem létezik, akkor létrehozzuk
// Ha x=1, akkor a menü látsszik. Ha x=0, akkor a menü nem látsszik.
if (isset($_COOKIE["menu"])) { 
  if (htmlspecialchars($_COOKIE["menu"])==1) { 
     $MenuStat = 'checked'; echo "<script>var x=1;</script>"; 
  } else {$MenuStat = ''; echo "<script>var x=0;</script>";  }
} else {
  $MenuStat = ''; $cookieInit = "onLoad=".'"'."document.cookie='menu=0';".'"';
  echo "<script>var x=0; </script>";
}
?>

<!-- A menu sütet kezelő  javascript kód megjelenítése-->
<script>
function SetMenuX()
{ if(x==0){x=1;}else{x=0;} document.cookie='menu='+x; }
</script>

<!-- Karakterkódolás megadása. Ikon, stíluslapok és fontkészlet illesztése-->
 <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 <meta name="author" content="Gál Tamás" >
<!-- GNU General Public License v3 </a> alatt használható, módosítható és továbbadható a forrás megjelölésével. -->
<!-- Karakterkódolás megadása. Ikon, stíluslapok és fontkészlet illesztése-->
 <link type="text/css" rel="stylesheet" media="all" href="css/w3_alap.css" />
 <link type="text/css" rel="stylesheet" media="all" href="css/w3_webshop.css" />
 <link  rel="icon" type="image/png" href="kepek/ikonok/kosari_2.png">
 <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:700italic,400&subset=latin,latin-ext' 
        rel='stylesheet' type='text/css'>
<!-- A fejléc oldalfüggő elemeinek megjelenítése-->
 <?php echo $FejlecStr;  ?>
</head>

<!-- A dokumentumtörzs. Betöltése után létrehozza a menu sütit, ha még nincs-->
 <?php echo "<body $cookieInit >"  ?>
  <div id="lap">
    <div id="fejlec">
       <!-- A fejlécbe kerül egy kép és az webáruház neve-->
       <img src="kepek/ikonok/kosar480W3.png" alt="Webaruház logó" id='webaruhazlogo' style="float:left;" height="60"> 
       <?php echo "<h1>$AruhazNev</h1>"; if ($hozzaferes > 1) { echo "Üdv. $mm_felhasznalo"; } ?>     
     </div>
     <!-- A menü checkbox nem látszik, de cimkéje igen -->
     <?php         
       echo "<input type='checkbox' name='chmenu' id='chmenu' value='chmenu' $MenuStat onClick=SetMenuX() >
       <label for='chmenu' id='chmenuL'><img src='kepek/ikonok/menu28p.png' alt='Menü' title='Menü' ><div>Menű</div> </label><br>";
     ?>
     <!-- A kezdőlap linkje-->
     <a href='./' id='akezdolap'><img src='kepek/ikonok/haz28p.png' alt='Kezdőlap' title='Kezdőlap' ><div>Kezdőlap</div> </a>	
     <?php 
       // Megfelelő jogosúltság esetén a szerkeszó oldal linkje is megjelenik
       if ($hozzaferes > 5) { 
          echo "<a href='?f0=szerkeszt' id='aszerkesztes'><img src='kepek/ikonok/ceruza28p.png' alt='Szerkesztés' title='Szerkesztés'>
                <div>Szerkesztés</div> </a>";
       }
       // Háttérkép és útvonal megjelenítése
       echo "<div id='HatterKep'></div>";
       echo $UtvonalStr;
       // A menű és a tertelom megjelenítése. Ha a menü checkbox nincs kiválasztva, akkor a menüt a CSS eldugja
       Kiir_Menu();
       Kiir_Tartalom();   
       // Az adatbázis és a munkamenet bezárása
       mysqli_close($MySqliLink); 
       session_write_close();
     ?>

  </div>   
  <!-- Lábléc megjelenítése.-->
  <?php echo "<footer id='Lablec'><p>$CegNev - $CegCim - Tel:$CegTel</p></footer>"; ?>


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-47717502-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>

</html>
