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
    <TITLE> Panel gry </TITLE>
  </HEAD>
  <BODY>
	   <div class="center">
	  <?php
	  $gracz = $_GET['gracz'];
	  $gra = $_GET['gra'];
	  ?>
	  <H2> <?php echo $gra, ' - ', $gracz; ?> </H2>
	  <?php
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
	  
	  $stmt = oci_parse($conn, "SELECT count(*) FROM Rozgrywka where gra = '$gra' and ktoWygral = '$gracz'");
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      $row = oci_fetch_array($stmt, OCI_BOTH);
      $wygranych = $row[0];
      
      $stmt = oci_parse($conn, "SELECT count(*) FROM Uczestnicy where uczestnik = '$gracz' and id IN (SELECT id FROM Rozgrywka where gra = '$gra')");
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      $row = oci_fetch_array($stmt, OCI_BOTH);
      $zagranych = $row[0];
      
      echo "<b>Rozgrywki:</b> <BR>";
      echo "Wygrane: ", $wygranych, "<BR>";
      echo "Przegrane: ", $zagranych - $wygranych, "<BR>";
      echo "Sumarycznie: ", $zagranych, "<BR>";
      
      // TODO nemesis i dominacja
      
      echo "<BR><A HREF=\"historia_rozgrywek.php?gracz=".$gracz."&gra=".$gra."\">"."Historia rozgrywek gracza<A><BR>\n";
      
      echo "<BR><H3> Ranking </H3>";
        
      $r_stmt = oci_parse($conn, "SELECT punktyRankingowe, idFormuly FROM Ranking where gra = '$gra' and kto = '$gracz'");
      oci_execute($r_stmt, OCI_NO_AUTO_COMMIT);
      
	  while (($ranking_row = oci_fetch_array($r_stmt, OCI_BOTH))) {
	     $f_stmt = oci_parse($conn, "SELECT formula FROM SystemWyliczania where id = $ranking_row[1]");
	     oci_execute($f_stmt, OCI_NO_AUTO_COMMIT);
	     $formula_row = oci_fetch_array($f_stmt, OCI_BOTH);
	     
	     $m_stmt = oci_parse($conn, "SELECT count(*) FROM Ranking where gra = '$gra' 
									and idFormuly = $ranking_row[1] and punktyRankingowe > $ranking_row[0]");
	     oci_execute($m_stmt, OCI_NO_AUTO_COMMIT);
	     $miejsce_row = oci_fetch_array($m_stmt, OCI_BOTH);
	     
	     echo "Formuła: <A HREF=\"formula.php?id=".$ranking_row[1]."\">".$formula_row[0]."<A>";
	     echo "<BR>Pozycja: ", $miejsce_row[0] + 1, "<BR>Punkty: ", $ranking_row[0], "<BR><BR>";
      }
      
	  
	  echo "<BR>";
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
    </div>
	</FORM>
  </BODY>
</HTML>
