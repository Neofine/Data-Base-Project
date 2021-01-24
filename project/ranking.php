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
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: rgb(43, 82, 88);
}
tr:nth-child(odd) {
  background-color: rgb(134, 156, 155);
}
</style>
  <HEAD>
	<STYLE>
	table, th, td {
		border: 1px solid black;
	}
	</STYLE>
    <TITLE> Ranking </TITLE>
  </HEAD>
  <BODY>
	   
	  <?php
	  
	  $gra = $_GET['gra'];
	  $formula_id = $_GET['formula_id'];
	  
	  print"<table_border='2'>";
	  echo "<H2>Tabela rankingowa gry ".$gra;
	  if ($formula != '')
		echo "wyliczony formułą ".$formula;
	  echo "</H2>";
	  ?>
	  
	  <FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	  
	  <?php

	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 


	  if ($formula_id == '') { #wybrana gra ma jedną domyślną formułę
		  $stmt = oci_parse($conn, "SELECT * FROM Ranking where gra = '$gra' ORDER BY punktyRankingowe DESC");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  #$row = oci_fetch_array($stmt, OCI_BOTH);

	  } else { #wybrana formuła wybranej gry
		  $stmt = oci_parse($conn, "SELECT * FROM Ranking where gra = '$gra' 
									AND idFormuly = $formula_id ORDER BY punktyRankingowe DESC");
		  oci_execute($stmt, OCI_NO_AUTO_COMMIT);
		  #$row = oci_fetch_array($stmt, OCI_BOTH);

	  }
	  
	  ?>
	  
	  <div class="center">
	  <table style="width:50%">
	  <tr>
		<th>Pozycja</th>
		<th>Gracz</th>
	    <th>Punkty rankingowe</th>
	  </tr>
	
	  <?php
	  $pozycja = 0;
	  $ile = 1;
	  $punkty_poprzedni = -1;
	  
	  while (($row = oci_fetch_array($stmt, OCI_BOTH))) {
		if ($row[2] < $punkty_poprzedni or $punkty_poprzedni == -1) {
			$punkty_poprzedni = $row[2];
			$pozycja = $ile;
		}
		echo "<tr>";
		echo "<td>".$pozycja."</td>";
		echo "<td>".$row[0]."</td>";
		echo "<td>".$row[2]."</td>";
		echo "</tr>";
		$ile++;
      }
	  
	  echo "</table><BR>";
	  ?>
	  
	<FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
    </div>
	</FORM>
  </BODY>
</HTML>
