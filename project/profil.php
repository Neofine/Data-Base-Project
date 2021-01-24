<!DOCTYPE html>
<HTML>
<STYLE>
  body {
    background-color: rgb(0, 135, 125);
    }
  .center {
	  text-align:center;
	  width: 100%;
	  padding: 10px;
  }
  h2 {
    color: rgb(152, 212, 197);
    text-align: center;
  }
  h3 {
    color: rgb(152, 212, 197);
    text-align: center;
  }
  .button {
  background-color: rgb(43, 82, 88);
  border: none;
  color: white;
  width: 200px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  border-radius: 4px;
  margin: 4px 2px;
  cursor: pointer;
}
.button2:hover {
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
}
</style>
  <HEAD>
  <STYLE>
  .center {
	  text-align:center;
	  width: 100%;
	  padding: 10px;
  }
  </STYLE>
    <TITLE> Profil </TITLE>
  </HEAD>
  <BODY>
	  <?php
	  session_start();
	  
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
	  $wybrany = $_GET['gracz']; 
	  $stmt = oci_parse($conn, "SELECT * FROM Gracz WHERE login = '$wybrany'");
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      
      if (!($row = oci_fetch_array($stmt, OCI_BOTH))) {
		  echo "Gracz o takim loginie nie istnieje<BR>"; ?>
		  <FORM ACTION="strona_glowna.php">
		  <INPUT TYPE="SUBMIT" VALUE="Wróć">
		  </FORM> 
	      <?php
	  } else {
		  ?>
		  <div class="center">
		  <H2> Profil gracza <?php echo $wybrany; ?> </H2>
		  </div>
		  <?php
		  
		  if ($_SESSION['LOGIN'] != '' and $_SESSION['LOGIN'] != $wybrany) {
			$ja = $_SESSION['LOGIN'];
			$stmt = oci_parse($conn, "SELECT kogo FROM Znajomosc WHERE kto = '$ja' and kogo = '$wybrany'");
			oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  
			if (!($row = oci_fetch_array($stmt, OCI_BOTH))) {
			?>
			  <div class="center">
			  <FORM <?php echo 'ACTION="dodaj_znajomego.php?kto=', $ja, '&kogo=', $wybrany, "\""?> METHOD="POST">
			  <INPUT TYPE="SUBMIT" VALUE="Dodaj znajomego">
			  </FORM>
			  </div>
			<?php
			} 
		  }
		  
		  $stmt = oci_parse($conn, "SELECT * FROM GraczLudzki WHERE login = '$wybrany'");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  echo "<div class="."center".">";
		  if ($row = oci_fetch_array($stmt, OCI_BOTH)) {
			echo 'Imię: ', $row[1], '<BR>Nazwisko: ', $row[2], '<BR>Poziom: ', $row[3], '<BR><BR>';
			
		  } else {
			$stmt = oci_parse($conn, "SELECT * FROM GraczSI WHERE login = '$wybrany'");
			oci_execute($stmt, OCI_NO_AUTO_COMMIT);
			$row = oci_fetch_array($stmt, OCI_BOTH);
			echo 'Twórca: ', $row[1], '<BR>Moc procesora: ', $row[2], '<BR><BR>';
		  }
		  echo "</div><div class="."center".">";
		  echo "<A HREF=\"panel_gier.php?gracz=".$wybrany."\">"."Panel gier<A>";
		  echo "</div><div class="."center".">";
		  echo "<A HREF=\"historia_rozgrywek.php?gracz=".$wybrany."\">"."Historia rozgrywek gracza<A>\n";
		  
		  $stmt = oci_parse($conn, "SELECT COUNT(*) FROM Znajomosc WHERE kto = '$wybrany'");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);

		  $row = oci_fetch_array($stmt, OCI_BOTH);
		  echo "</div><div class="."center".">";
		  echo "<A HREF=\"znajomi.php?gracz=".$wybrany."\">"."Znajomi: ".$row[0]."<A><BR><BR>";
	  	  echo "</div>";
	  ?>
	<div class="center"> 
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	</FORM>
	</div>
	<?php
	} 
	?>
  </BODY>
</HTML>
