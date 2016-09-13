<?php
  require_once("set/w3_fgvek.php");
  require_once("oldalak/w3_setup.php");
?>

<!DOCTYPE html>
<html lang="hu">
<head>
 <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 <link type="text/css" rel="stylesheet" media="all" href="css/w3_alap.css" />
 <link type="text/css" rel="stylesheet" media="all" href="css/w3_webshop_setup.css" />
 <link rel="icon" type="image/png" href="kepek/ikonok/kosari_2.png">
 <link href='http://fonts.googleapis.com/css?family=Alegreya+Sans:700italic,400&subset=latin,latin-ext' 
             rel='stylesheet' type='text/css'>
</head>
<body>

  <div id="lap">
    <div id="fejlec">
      <img src="kepek/ikonok/kosar480W3.png" alt="Webaruház logó" id='webaruhazlogo' style="float:left;" height="60" >
      <h1>Setup</h1>
    </div>
    <div id='HatterKep'></div>	
    <?php 
      Kiir_Tartalom();   
      echo "<div id='Lablec'>$CegNev - $CegCim - Tel:$CegTel</div>";
      mysqli_close($MySqliLink); 
    ?>
  </div>   
</body>
</html>

