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
    <TITLE> Profil </TITLE>
  </HEAD>
  <BODY>
	  <div class="center">
	  <?php
	  $conn = oci_pconnect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
        if (!$conn) {
			echo "oci_connect failed\n";
			$e = oci_error();
			echo $e['message'];
        } 
        
       $nazwa = $_REQUEST['NZW'];
       $nazwa = strtoupper(trim($nazwa));
	   $min = $_REQUEST['MIN'];
	   $max = $_REQUEST['MAX'];
	   
       $stmt = oci_parse($conn, "SELECT count(*) FROM GRA WHERE nazwa = '$nazwa'");
	   oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	   $row = oci_fetch_array($stmt, OCI_BOTH);
	   if ($row[0] > 0) {
		  echo 'Gra o tej nazwie już istnieje<BR>'; ?>
		  <FORM ACTION="strona_glowna.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
		  <?php
	   } else if ($min > $max) {
		  echo 'Min graczy nie może być większe od max<BR>'; ?>
		  <FORM ACTION="strona_glowna.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
		  <?php
	   } else if ($min < 1) {
		   echo 'Min graczy nie może być mniejsze od 1<BR>'; ?>
		  <FORM ACTION="strona_glowna.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
		  <?php
	  } else if (strlen($nazwa) > 100) {
		  echo 'Za długa nazwa gry<BR>'; ?>
		  <FORM ACTION="strona_glowna.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
		  <?php
		 
	  } else {
	   
       $stmt = oci_parse($conn, "INSERT INTO GRA VALUES ('$nazwa', $min, $max)");
	   oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

	    echo 'Dodano nową grę!<BR>'; ?>
		  <FORM ACTION="strona_glowna.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
	   <?php } ?>
	  </div>

  </BODY>
</HTML>
