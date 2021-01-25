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
    <TITLE> Rozgrywka </TITLE>
  </HEAD>
  <BODY>
<div class="center">
	  <?php
	  $id = $_GET['id'];
	 
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
      $stmt = oci_parse($conn, "SELECT * FROM Rozgrywka WHERE id = $id");
	  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  
	  $row = oci_fetch_array($stmt, OCI_BOTH);
	  $gra = $row[1];
      $ktoWygral = $row[2];
	  $kiedyRozegrana = substr($row[3], 0, -10).substr($row[3], -3, 3);
      
      
      ?>
	  <H2> Rozgrywka w grę <?php echo $gra, " id = ", $id; ?>  </H2>
	  <?php
	  echo "Zwycięzca: ", " <A HREF=\"profil.php?gracz=".$ktoWygral."\">".$ktoWygral."<A><BR>";
	  echo "Rozegrana: ", $kiedyRozegrana, "<BR><BR>";
	  
	  $stmt = oci_parse($conn, "SELECT uczestnik FROM Uczestnicy WHERE id = $id");
	  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
	  
	  $max_length = 0;
	  echo "Uczestnicy:<BR>";
	  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
		 $max_length = max($max_length, strlen($row[0]));
	     echo "<A HREF=\"profil.php?gracz=".$row[0]."\">".$row[0]."<A><BR>\n";
      }
      
      echo "<h3>Przebieg rozgrywki:</h3>";
      
	  $stmt = oci_parse($conn, "SELECT * FROM Ruch WHERE idRozgrywki = $id ORDER BY numer");
	  oci_execute($stmt, OCI_NO_AUTO_COMMIT);

	  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
		 echo "<pre>";
		 if ($row[0] < 10) 
			echo " ";
			
	     echo $row[0].": ".$row[2];
	     $len = strlen($row[2]);
	     while ($len < $max_length) {
			 echo " ";
			 $len++;
		 }
		 
		 echo " - ".$row[3]."</pre>";
      }
      
	  echo "<BR><BR>";
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
    </div>
	</FORM>
  </BODY>
</HTML>
