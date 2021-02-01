#include <bits/stdc++.h>
using namespace std;
string str;
int line;

string gameSource;

bool getGame(string game) {
    return game[0] == '1';
}

bool getPlayer(string player) {
    return player[0] == '[' && (player[6] == ' ' && (player[1] == 'W' || player[1] == 'B'));
}

bool getWinner(string winner) {
    return winner[1] == 'R';
}

//bool getDate(string date) {
//    return date[1] == 'R';
//}

string getNick(string str) {
    string out;
    for (auto x: str) {
        if (x == '"' && out.empty())
            out.push_back(x);
        else if (x == '"') {
            reverse(out.begin(), out.end());
            out.pop_back();
            reverse(out.begin(), out.end());
            return out;
        }
        else if (!out.empty())
            out.push_back(x);
    }
    return out;
}

void generatePlayers() {
    ifstream logins("/home/neofine/CLionProjects/BD/logins.txt");
    ifstream emails("/home/neofine/CLionProjects/BD/emails.txt");
    ofstream players("/home/neofine/CLionProjects/BD/players.txt");

    while(getline(logins, str)) {
        line++;

        string mail;
        emails >> mail;
        if (!isalpha(mail.back()))
            mail.pop_back();
        players << str << " " << mail << endl;
    }
    logins.close();
    emails.close();
    players.close();
}

void generateNicks() {
    ifstream plik("/home/neofine/CLionProjects/BD/cropped.txt");
    ofstream plikwynik("/home/neofine/CLionProjects/BD/logins.txt");
    map<string, bool> added;

    while(getline(plik, str)) {
        line++;
        if (getPlayer(str)) {
            string nick = getNick(str);
            if (!added.contains(nick)) {
                plikwynik << nick << endl;
                added[nick] = true;
            }

        }
    }
    plikwynik.close();
    plik.close();
}

void playersToSql() {
    ifstream player("/home/neofine/CLionProjects/BD/players.txt");
    ofstream sqlplayer("/home/neofine/CLionProjects/BD/playersInsert.sql");

    string nick, email;
    while(player >> nick && player >> email) {
        sqlplayer << "INSERT INTO GRACZ VALUES ('" <<nick <<"', '"<< email<<"');"<<endl;
    }
    player.close();
    sqlplayer.close();
}

void cropNames() {
    ifstream nameNC("/home/neofine/CLionProjects/BD/namesNC.txt");
    ofstream nameC("/home/neofine/CLionProjects/BD/namesC.txt");

    string nick, email;
    while(nameNC >> nick) {
        string cropped;
        for (auto u: nick) {
            if (u == ',')
                break;
            cropped.push_back(u);
        }
        nameC << cropped <<endl;
    }
    nameNC.close();
    nameC.close();
}

void cropNamesF() {
    ifstream nameNC("/home/neofine/CLionProjects/BD/namesNCF.txt");
    ofstream nameC("/home/neofine/CLionProjects/BD/namesCF.txt");

    string nick, email;
    while(nameNC >> nick) {
        string cropped;
        for (auto u: nick) {
            if (u == ',')
                break;
            cropped.push_back(u);
        }
        nameC << cropped <<endl;
    }
    nameNC.close();
    nameC.close();
}

void cropSurnames() {
    ifstream nameNC("/home/neofine/CLionProjects/BD/surnamesNC.txt");
    ofstream nameC("/home/neofine/CLionProjects/BD/surnamesC.txt");

    string nick, email;
    while(nameNC >> nick) {
        string cropped;
        for (auto u: nick) {
            if (u == ',')
                break;
            cropped.push_back(u);
        }
        nameC << cropped <<endl;
    }
    nameNC.close();
    nameC.close();
}

void cropSurnamesF() {
    ifstream nameNC("/home/neofine/CLionProjects/BD/surnamesNCF.txt");
    ofstream nameC("/home/neofine/CLionProjects/BD/surnamesCF.txt");

    string nick, email;
    while(nameNC >> nick) {
        string cropped;
        for (auto u: nick) {
            if (u == ',')
                break;
            cropped.push_back(u);
        }
        nameC << cropped <<endl;
    }
    nameNC.close();
    nameC.close();
}

void appendNames() {
    ifstream name("/home/neofine/CLionProjects/BD/namesC.txt");
    ifstream surname("/home/neofine/CLionProjects/BD/surnamesC.txt");
    ofstream all("/home/neofine/CLionProjects/BD/fullNames.txt");

    string nick;
    while(name >> nick) {
        string sur;
        surname >> sur;
        all << nick <<" "<< sur<<endl;
    }
    name.close();
    surname.close();
    all.close();
}

void insertHumans() {
    ifstream nameMale("/home/neofine/CLionProjects/BD/namesC.txt");
    ifstream surnameMale("/home/neofine/CLionProjects/BD/surnamesC.txt");

    ifstream nameFemale("/home/neofine/CLionProjects/BD/namesCF.txt");
    ifstream surnameFemale("/home/neofine/CLionProjects/BD/surnamesCF.txt");

    ifstream logins("/home/neofine/CLionProjects/BD/logins.txt");
    ofstream humanPlayer("/home/neofine/CLionProjects/BD/humanPlayers.sql");
    mt19937 rng(chrono::steady_clock::now().time_since_epoch().count());
    string nameC, surnameC, login;
    while(nameMale >> nameC) {
        surnameMale >> surnameC;
        logins >> login;
        humanPlayer << "INSERT INTO GRACZLUDZKI VALUES ('" << login <<"', '"<< nameC <<"', '"<< surnameC;
        int level = uniform_int_distribution<int>(0, 100)(rng);
        if (level <= 50) {
            humanPlayer<<"', 'Amator');"<<endl;
        }
        else if (level <= 90) {
            humanPlayer<<"', 'Średniozaawansowany');"<<endl;
        }
        else {
            humanPlayer<<"', 'Profesjonalista');"<<endl;
        }
    }

    while(nameFemale >> nameC && logins >> login) {
        surnameFemale >> surnameC;
        humanPlayer << "INSERT INTO GRACZLUDZKI VALUES ('" << login <<"', '"<< nameC <<"', '"<< surnameC;
        int level = uniform_int_distribution<int>(0, 100)(rng);
        if (level <= 50) {
            humanPlayer<<"', 'Amator');"<<endl;
        }
        else if (level <= 90) {
            humanPlayer<<"', 'Średniozaawansowany');"<<endl;
        }
        else {
            humanPlayer<<"', 'Profesjonalista');"<<endl;
        }
    }
    nameMale.close();
    surnameMale.close();

    nameFemale.close();
    surnameFemale.close();

    logins.close();
    humanPlayer.close();

}

void cropIn(int amount) {
    ifstream in("/home/neofine/CLionProjects/BD/in1.txt");
    ofstream outx("/home/neofine/CLionProjects/BD/cropped.txt");
    while((amount - 17) % 18 != 0)
        amount++;

    while(getline(in, str)) {
        line++;
        if (line < amount) {
            outx << str << endl;
        }
    }
    in.close();
    outx.close();
}

bool dateOrTime(string date) {
    return date[1] == 'U';
}

int y, m, d;

string generateDate(mt19937 xd) {
    int to_add = uniform_int_distribution<int>(0, 2)(xd);
    d += to_add;
    if (m == 1 && d > 31) {
        m++, d-=31;
    }
    else if (m == 2 && d > 28) {
        m++, d-=28;
    }
    else if (m == 3 && d > 31) {
        m++, d-=31;
    }
    else if (m == 4 && d > 30) {
        m++, d-=30;
    }
    else if (m == 5 && d > 31) {
        m++, d-=31;
    }
    else if (m == 6 && d > 30) {
        m++, d-=30;
    }
    else if (m == 7 && d > 31) {
        m++, d-=31;
    }
    else if (m == 8 && d > 31) {
        m++, d-=31;
    }
    else if (m == 9 && d > 30) {
        m++, d-=30;
    }
    else if (m == 10 && d > 31) {
        m++, d-=31;
    }
    else if (m == 11 && d > 30) {
        m++, d-=30;
    }
    else if (m == 12 && d > 31) {
        m++, d-=31;
    }
    return to_string(y) + "." + to_string(m) + "." + to_string(d);
}

void gameInfo() {
    ifstream game("/home/neofine/CLionProjects/BD/cropped.txt");
    ofstream info("/home/neofine/CLionProjects/BD/gameInsert.sql");
    string white, black;
    mt19937 rng(chrono::steady_clock::now().time_since_epoch().count());
    y = 2019, m = 1, d = 1;

    while(getline(game, str)) {
        line++;
        if (getGame(str) || getPlayer(str) || getWinner(str) || dateOrTime(str)) {
            if (getPlayer(str)) {
                string nick = getNick(str);
                if (str[1] == 'W')
                    white = nick;
                else black = nick;
                info << nick << endl;
            }
            else if (getWinner(str)) {
                if (str[9] == '1')
                 info << white << endl;
                else info << black <<endl;
            }
            else if (dateOrTime(str)) {
                info << generateDate(rng) << endl;
            }
            else info << str <<endl;
        }
    }

    game.close();
    info.close();
}

int main() {
    gameSource = "/home/neofine/CLionProjects/BD/logins.txt";
    cropIn(15000);
    //generateNicks();
    //generatePlayers();
    //playersToSql();
    //cropNames();
    //cropSurnames();
    //cropNamesF();
    //cropSurnamesF();
    //appendNames();
    //insertHumans();
    //gameInfo();
}