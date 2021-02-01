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
input[type=text] {
  width: 200;
  padding: 12px 20px;
  margin: 8px 0;
  box-sizing: border-box;
  border-radius: 7px;
  font-size: 16px;
  border: 3px solid #ccc;
  background-color: #3CBC8D;
  color: white;
}
input[type=text]:focus {
  border: 3px solid #555;
}
input[type=password] {
  width: 200;
  padding: 12px 20px;
  margin: 8px 0;
  box-sizing: border-box;
  border-radius: 7px;
  font-size: 16px;
  border: 3px solid #ccc;
  background-color: #3CBC8D;
  color: white;
}
input[type=password]:focus {
  border: 3px solid #555;
}
  </STYLE>
  <HEAD>
    <TITLE> Dodawanie gry </TITLE>
  </HEAD>
  <BODY>
    <H2> Dodawanie gry </H2>
    <div class="center">
    <FORM ACTION="dodaj_gre.php" METHOD="POST">  
      <h3>Nazwa gry:</h3> <INPUT TYPE="TEXT" NAME="NZW" placeholder="Nazwa" VALUE="" required><BR><BR>
      <h3>Minimalna liczba graczy:</h3>  <INPUT TYPE="TEXT" NAME="MIN" placeholder="min graczy" VALUE="" required><BR><BR>
      <h3>Maksymalna liczba graczy:</h3>  <INPUT TYPE="TEXT" NAME="MAX" placeholder="max graczy" VALUE="" required><BR><BR>
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Dodaj!">
    </FORM>
    <FORM ACTION="strona_glowna.php">
    <INPUT TYPE="SUBMIT" class="button button2" VALUE="Strona główna">
	</FORM>
	</div>
  </BODY>
</HTML>
