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
	font-size: 24px;
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
    <TITLE> Wybierz formułę </TITLE>
  </HEAD>
  <BODY>
	   <H2> Wybierz formułę, której odpowiadający ranking chcesz zobaczyć </H2>
	   <div class="center">
	  <?php
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
	  
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
	  $gra = $_GET['gra'];
      $id_stmt = oci_parse($conn, "SELECT DISTINCT idFormuly FROM Ranking where gra = '$gra'");
      oci_execute($id_stmt, OCI_NO_AUTO_COMMIT);
      

	  while (($id_row = oci_fetch_array($id_stmt, OCI_BOTH))) {
		  $f_stmt = oci_parse($conn, "SELECT formula FROM SystemWyliczania where id = $id_row[0]");
	      oci_execute($f_stmt, OCI_NO_AUTO_COMMIT);
	      $formula_row = oci_fetch_array($f_stmt, OCI_BOTH);
		  
		  
		  echo "<BR><A HREF=\"ranking.php?gra=".$gra."&formula_id=".$id_row[0]."\">".$formula_row[0]."<A><BR>\n";
      }
      
	  
	  echo "<BR>";
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	</FORM>
	</div>
  </BODY>
</HTML>
