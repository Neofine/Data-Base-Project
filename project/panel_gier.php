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
    <TITLE> Panel gier </TITLE>
  </HEAD>
  <BODY>
	   <H2> Panel gier gracza <?php echo $_GET['gracz']?> </H2>
	   <div class="center">
	  <?php
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
	  
	  $stmt = oci_parse($conn, "SELECT nazwa FROM Gra");
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      $gracz = $_GET['gracz'];
      
	  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
	     echo "<BR><A HREF=\"panel_gry.php?gracz=".$gracz."&gra=".$row[0]."\">".$row[0]."<A><BR>\n";
      }
      
	  
	  echo "<BR>";
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
    </div>
	</FORM>
  </BODY>
</HTML>
