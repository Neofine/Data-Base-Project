<!DOCTYPE html>
<HTML>
  <HEAD>
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
    font-size: 36px;
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
img {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
  width: 150px;
}
  </STYLE>
    <TITLE> Kuchrnik </TITLE>
  </HEAD>
  <BODY>
	<div class="center">
    <H2>    Witaj na Kuchrniku! </H2>
    </div>
      <?php 

		session_start();
		if (!isset($_SESSION['LOGIN'])) {
          $_SESSION['LOGIN'] = '';
	    }
        ?>
        
      
	  <div class="center">
	  <?php if ($_SESSION['LOGIN'] != ''): ?>
      <FORM <?php echo 'ACTION="profil.php?gracz=', $_SESSION['LOGIN'],"\""?> METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Mój profil">
      </FORM>
      </div>
      
      <div class="center">
      <FORM ACTION="wyloguj.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Wyloguj się">
      </FORM>
      </div>
      


      <?php else: ?>
       <div class="center">
      <FORM ACTION="zaloguj.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Zaloguj się">
      </FORM>
      </div>
      <?php endif; ?>
      
      <div class="center">
	  <h3>Wyszukaj gracza</h3>
	  <FORM ACTION="profil.php" METHOD="GET">  
      <INPUT TYPE="TEXT" NAME="gracz" placeholder="Gracz.." VALUE="" required><BR>
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Szukaj">
      </FORM>
	  </div>

      
      <div class="center">
      <FORM ACTION="wybierz_gre_ranking.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2"  VALUE="Rankingi">
      </FORM>
      </div>
      
      <div class="center">
      <FORM ACTION="lista_graczy.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2"  VALUE="Lista graczy">
	  </FORM>
	  </div>
	  
	  
	  
	  <div class="center">
	  <FORM ACTION="wybierz_gre_historia.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Historia rozgrywek">
	  </FORM>
	  </div>
	  
	  <?php if ($_SESSION['LOGIN'] != ''): ?>
	  <div class="center">
	  <FORM ACTION="dodaj_gre_input.php" METHOD="POST">
      <INPUT TYPE="SUBMIT" class="button button2" VALUE="Dodaj nową grę">
	  </FORM>
	  <?php endif; ?>
	  </div>
  </BODY>
</HTML>
