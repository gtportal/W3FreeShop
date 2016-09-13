<?php 


function FileNevTisztit($str){ 
  $arr = array(' ' => '',  'á' => 'a', 'Á' => 'A', 'é' => 'e', 'É' => 'e', 'ó' => 'o', 'Ó' => 'O', 'í' => 'i', 'Í' => 'I', 
               'Ú' => 'U', 'ú' => 'u', 'Ö' => 'O', 'ö' => 'o', 'Ő' => 'O', 'ő' => 'o', 'Ü' => 'U', 'ü' => 'u', 'Ű' => 'U', 
               'ű' => 'u');  
     $str1 = strtr($str ,$arr);
return $str1;
}


function URLTisztit($str){ 
  $arr = array(' ' => '_',    ',' => '',  ';' => '',  '"' => '',  "'" => '',  ':' => '',  '  ' => ' ', 'á' => 'a', 'Á' => 'A', 
               'é' => 'e', 'É' => 'e', 'ó' => 'o', 'Ó' => 'O', 'í' => 'i', 'Í' => 'I', 'Ú' => 'U', 'ú' => 'u', 'Ö' => 'O', 
               'ö' => 'o', 'Ő' => 'O', 'ő' => 'o', 'Ü' => 'U', 'ü' => 'u', 'Ű' => 'U', 'ű' => 'u');  
     $str1 = strtr($str ,$arr);
return $str1;
}



function karakter_csere($ForrasSzoveg){
        $arr = array( "'" => "!0!", '"' => "!1!", ";" => "!2!", "&" => "!3!", "=" => "!4!" , ":" => "!5!");
        $kimenet  = strtr($ForrasSzoveg ,$arr);
	return $kimenet;
}

function karakter_csere_vissza($ForrasSzoveg){
        $arr = array( "!0!" => "'", '!1!' => '"', "!2!" => ";", "!3!" => "&", "!4!" => "=",  "!5!" => ":", "(*" => "&lt;","*)" => "&gt;",
           "*T1" => "&nbsp;","*T2" => "&nbsp;&nbsp;", "*T3" => "&nbsp;&nbsp;&nbsp;","*T4" => "&nbsp;&nbsp;&nbsp;&nbsp;",
           "*T5" => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","{*" => "<br>","*}" => "</br>");
        $kimenet  = strtr($ForrasSzoveg ,$arr);
	return $kimenet;
} 

 function tiszta_szov($szov)
   {
     $szov = karakter_csere($szov);
     $szov = trim($szov);     
     return $szov;
   }

function karakter_cserepl($ekezetes_szoveg){
	$ezt =  array('"',";","'","=","&"); 
	$erre= array("¢",",",",",",",",");
	$kimenet = str_replace($ezt,$erre,$ekezetes_szoveg);
	return $kimenet;
}


function per_csere($szovg){
	
	$kimenet = stripcslashes($szovg);
	return $kimenet;
}


  function tiszta_szovpl ($szov)
   {
     $szov = karakter_cserepl($szov);
     $szov = trim($szov);     
     $szov = mysql_escape_string($szov);
     
   return $szov;
   }


  function tiszta_szo ($szov)
   {
     $szov = tiszta_szov($szov);     
   
   return $szov;
   }
   
   function tiszta_int($szov)
   {
     $szov = trim($szov);
     settype($szov,'integer');  if (!(is_int($szov))) {$szov=0;}
     return $szov;
   }

  function tiszta_val ($szov)
   {
     $szov = trim($szov);
     settype($szov,'double');
   return $szov;
   }

   function latogatasok_novel ()
   {
     global $MySqliLink;
     $UpdateStr = "UPDATE latogato_szamlalo SET latogatasok = latogatasok + 1  WHERE id=1";
     if (!mysqli_query($MySqliLink,$UpdateStr)) { die("MySqli hiba ");}
   }

   function latogatasok_szama ()
   {
    global $MySqliLink;
     $SelectStr = "SELECT latogatasok FROM latogato_szamlalo where (id = 1)";
     $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba ");
     $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
     $latogatasCT  = $row['latogatasok'];
     return $latogatasCT;
   }



function getip(){
if (@getenv("HTTP_X_FORWARDED_FOR")){
  $ip = @getenv("HTTP_X_FORWARDED_FOR");
}
else{
  $ip = @getenv("REMOTE_ADDR");
}
if (strstr( $ip, "," ) ){
  $elvalaszto = ",";
  $ip = strtok ($ip, $elvalaszto);
}
return $ip;
}

function getipk()
{
    if (isset($_SERVER))
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
        {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else
        {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    }
    else
    {
        if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
        {
            $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
        }
        elseif ( getenv( 'HTTP_CLIENT_IP' ) )
        {
            $realip = getenv( 'HTTP_CLIENT_IP' );
        }
        else
        {
            $realip = getenv( 'REMOTE_ADDR' );
        }
    }
    return $realip;
}


function latogatok()
{
global $MySqliLink;

  // A régi adatok törlése - Ha egy látogató 5 perce nem kért le oldalt, akkor nem tekintjük aktívnak
  $DeleteStr   = "DELETE FROM online WHERE( (INTERVAL 5 MINUTE + datum) < '".date("Y-m-d H:i:s")."')";
  if (!mysqli_query($MySqliLink,$DeleteStr)) {die("Hiba ");}

 
  $SelectStr = "SELECT * FROM online WHERE(ip='".getipk()."')";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba ");
  $rows      = mysqli_num_rows($result);  mysqli_free_result($result);

  if($rows > 0){
    $sql = "UPDATE online SET datum=NOW() WHERE (ip='".getipk()."')";
  }
  else{
    $sql = "INSERT INTO online (ip, datum) values ('".getipk()."',NOW())";  
  }
  if (!mysqli_query($MySqliLink,$sql)) {die("Hiba ");}
  $SelectStr = "SELECT COUNT(id) AS darab FROM online";
  $result    = mysqli_query($MySqliLink,$SelectStr) OR  die("Hiba ");
  $row       = mysqli_fetch_array($result, MYSQLI_ASSOC); mysqli_free_result($result);
  $online = $row['darab'];
  return $online;
}






?>
