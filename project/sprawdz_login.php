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
    <TITLE> Logowanie </TITLE>
  </HEAD>
  <BODY>
    <H2> Logowanie </H2>
	  <?php 
	  
	  $logn = $_REQUEST['LOGN'];
	  $pasw = $_REQUEST['PASW'];
	  
	  $conn = oci_connect("am418419", "rampampam", "labora.mimuw.edu.pl/LABS");
      if (!$conn) {
		echo "oci_connect failed\n";
		$e = oci_error();
		echo $e['message'];
      }
      
      $stmt = oci_parse($conn, "SELECT * FROM Gracz WHERE login = '$logn' and email = '$pasw'");
      
      oci_execute($stmt, OCI_NO_AUTO_COMMIT);
      
      if (oci_fetch_array($stmt, OCI_BOTH) != false) {
		  session_start();
		  $_SESSION['LOGIN'] = $logn;
		  header("Location: https://students.mimuw.edu.pl/~am418419/profil.php?gracz=$logn");
		  exit();
	  } else {
		  echo 'Błędne dane logowania<BR>'; ?>
		  <FORM ACTION="zaloguj.php">  
		  <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wróć">
		  <?php 
	  }
	  ?>
    </FORM>
  </BODY>
</HTML>
