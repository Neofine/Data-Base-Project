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
  a {
	color: hotpink;
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
    <TITLE> Historia rozgrywek </TITLE>
  </HEAD>
  <BODY>
  
  
	  <?php
	  $gracz = $_GET['gracz'];
	  $gra = $_GET['gra'];
	  ?>
	  <div class="center">
	  <H2> Historia rozgrywek 
	    <?php 
		if ($gracz != '') 
			echo "gracza ", $gracz, " ";
		if ($gra != '')
			echo "w ", $gra;
		?> 
	  </H2>
	  </div>
	  <div class="center">
	  <FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	  
	  <?php
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
	  if ($gracz == '' and $gra == '') {
		  $stmt = oci_parse($conn, "SELECT * FROM Rozgrywka ORDER BY kiedyRozegrana");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  
		  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
			$date = substr($row[3], 0, -10).substr($row[3], -3, 3);
	        echo "<BR><A HREF=\"rozgrywka.php?id=".$row[0]."\">".$row[0]." - ".$row[1]." - ".$date."<A><BR>\n";
		  }
	  } else if ($gracz == '') {
		  $stmt = oci_parse($conn, "SELECT * FROM Rozgrywka WHERE gra = '$gra' ORDER BY kiedyRozegrana");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  
		  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
			$date = substr($row[3], 0, -10).substr($row[3], -3, 3);
	        echo "<BR><A HREF=\"rozgrywka.php?id=".$row[0]."\">".$row[0]." - ".$row[1]." ".$date."<A><BR>\n";
		  }
	  } else if ($gra == '') {
		  $stmt = oci_parse($conn, "SELECT * FROM Rozgrywka WHERE
							id IN (SELECT id from Uczestnicy where uczestnik = '$gracz')
							ORDER BY kiedyRozegrana");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);

		  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
			$date = substr($row[3], 0, -10).substr($row[3], -3, 3);
	        echo "<BR><A HREF=\"rozgrywka.php?id=".$row[0]."\">".$row[0]." - ".$row[1]." ".$date."<A><BR>\n";
		}
	  } else {
		  $stmt = oci_parse($conn, "SELECT * FROM Rozgrywka WHERE gra = '$gra' AND 
							id IN (SELECT id from Uczestnicy where uczestnik = '$gracz')
							ORDER BY kiedyRozegrana");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);

		  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
			$date = substr($row[3], 0, -10).substr($row[3], -3, 3);
	        echo "<BR><A HREF=\"rozgrywka.php?id=".$row[0]."\">".$row[0]." - ".$row[1]." ".$date."<A><BR>\n";
		  //todo ^ kolorowo zielono/czerwono
		  }	
	  }
	  
	  
	  
	  echo "<BR>";
	  ?>
    
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
    </div>
	</FORM>
  </BODY>
</HTML>
