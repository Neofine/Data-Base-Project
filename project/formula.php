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
    <TITLE> Formuła </TITLE>
  </HEAD>
  <BODY>
	  <?php
	  
	  $id = $_GET['id'];

	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
      $stmt = oci_parse($conn, "SELECT opis FROM SystemWyliczania where id = $id");
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      
      if ($row = oci_fetch_array($stmt, OCI_BOTH)) {
		echo "<H2>Formuła o id = ".$id."</H2>";
		echo "Opis - ".$row[0]."<BR><BR>";
	  } else {
		echo "Formuła o id = ".$id." nie istnieje<BR><BR>";
      }
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	</FORM>
  </BODY>
</HTML>
