drop table Gra cascade constraints;
drop table Rozgrywka cascade constraints;
drop table Uczestnicy cascade constraints;
drop table Ruch cascade constraints;
drop table Gracz cascade constraints;
drop table GraczLudzki cascade constraints;
drop table GraczSI cascade constraints;
drop table Znajomosc cascade constraints;
drop table SystemWyliczania cascade constraints;
drop table Ranking cascade constraints;
drop table HistoriaRankingu cascade constraints;
--drop sequence idrozgrywki_seq;

-- DDL projekt
-- Adrian Matwiejuk AM418419
-- Jakub Panasiuk JP418362

create table Gra
(
    nazwa     varchar2(100) primary key,
    minGraczy int not null check (minGraczy > 0),
    maxGraczy int not null
);

CREATE OR REPLACE TRIGGER sprawdzGre
    BEFORE INSERT
    ON Gra
    FOR EACH ROW
BEGIN
    IF (:NEW.maxGraczy < :NEW.minGraczy) THEN
        raise_application_error(-20001, 'maxGraczy musi byc >= minGraczy');
    END IF;
    :NEW.nazwa := UPPER(TRIM(:NEW.nazwa));
END;
/

CREATE OR REPLACE TRIGGER sprawdzNazweGry
    BEFORE INSERT OR UPDATE
    ON Gra
    FOR EACH ROW
BEGIN
    :NEW.nazwa := TRIM(:NEW.nazwa);
    IF (not REGEXP_LIKE(:NEW.nazwa, '[a-zA-Z\s]+') or INSTR(:NEW.nazwa, '  ') <> 0) THEN
        raise_application_error(-20002, 'Bledna nazwa gry!');
    END IF;
END;
/

create table Gracz
(
    login varchar2(30) primary key,
    email varchar2(100) not null unique
);

CREATE OR REPLACE TRIGGER sprawdzLogin
    BEFORE INSERT OR UPDATE
    ON Gracz
    FOR EACH ROW
BEGIN
    :NEW.login := TRIM(:NEW.login);
END;
/

CREATE OR REPLACE TRIGGER sprawdzEmail
    BEFORE INSERT OR UPDATE
    ON Gracz
    FOR EACH ROW
BEGIN
    :NEW.email := LOWER(TRIM(:NEW.email));

    IF NOT REGEXP_LIKE(:NEW.email, '[-a-z0-9.]*[-a-z0-9][@][-a-z0-9.]+[.][-a-z0-9]+') THEN
        raise_application_error(-20004, 'Bledny adres email');
    END IF;
END;
/

create table GraczLudzki
(
    login    varchar2(30) primary key references Gracz,
    imie     varchar2(50) not null,
    nazwisko varchar2(50) not null,
    poziom   varchar2(50) not null check (poziom in ('Amator', 'Średniozaawansowany', 'Profesjonalista'))
);

CREATE OR REPLACE TRIGGER sprawdzImieNazwisko
    BEFORE INSERT OR UPDATE
    ON GraczLudzki
    FOR EACH ROW
BEGIN
    IF (not REGEXP_LIKE(:NEW.imie, '[a-zA-Z]+')) THEN
        raise_application_error(-20005, 'Imie moze zawierac tylko litery!');
    END IF;
    IF (not REGEXP_LIKE(:NEW.nazwisko, '[a-zA-Z]+')) THEN
        raise_application_error(-20006, 'Nazwisko moze zawierac tylko litery!');
    END IF;
END;
/

create table GraczSI
(
    login        varchar2(30) primary key references Gracz,
    tworca       varchar2(50)     not null,
    mocProcesora double precision not null check (mocProcesora > 0)
);

create table Rozgrywka
(
    id             int primary key,
    gra            varchar2(100)    not null references Gra,
    ktoWygral      varchar2(30)     not null references Gracz,
    kiedyRozegrana TIMESTAMP             not null,
    waga           double precision not null check ( waga > 0 ),
    opis varchar2(30) not null
);

create table Uczestnicy
(
    id        int          not null references Rozgrywka,
    uczestnik varchar2(30) not null references Gracz,
    constraint uczestnicy_pk primary key (id, uczestnik)
);

CREATE OR REPLACE TRIGGER czyNiePrzekracza
    BEFORE INSERT
    ON Uczestnicy
    FOR EACH ROW
DECLARE
    liczbaUczestnikow    number := 0;
    maxLiczbaUczestnikow number := 0;
BEGIN
    select count(*) INTO liczbaUczestnikow from Uczestnicy where id = :NEW.id;
    select maxGraczy INTO maxLiczbaUczestnikow from Gra where nazwa = (select gra from Rozgrywka where id = :NEW.id);
    --select maxGraczy from Gra where nazwa = (select gra from Rozgrywka where id = 1);
    IF (liczbaUczestnikow = maxLiczbaUczestnikow) THEN
        raise_application_error(-20008, 'Zbyt duza ilosc graczy w tej samej rozgrywce');
    END IF;
END;
/

create table Ruch
(
    numer       number(4)    not null check (numer > 0),
    idRozgrywki int          not null references Rozgrywka,
    czyjRuch    varchar2(30) not null references Gracz,
    ruch        varchar2(150),
    constraint ruch_pk primary key (numer, idRozgrywki)
);

CREATE OR REPLACE TRIGGER sprawdzCzyjRuch
    BEFORE INSERT
    ON Ruch
    FOR EACH ROW
DECLARE
    czyUczestniczy number := 0;
BEGIN
    SELECT count(*)
    INTO czyUczestniczy
    FROM (SELECT uczestnik FROM Uczestnicy WHERE id = :NEW.idRozgrywki and uczestnik = :NEW.czyjRuch);

    if (czyUczestniczy = 0) THEN
        raise_application_error(-20009, 'Ten gracz nie uczestniczy w danej rozgrywce');
    END IF;
END;
/

CREATE OR REPLACE TRIGGER czyNieZaMalo
    BEFORE INSERT
    ON Ruch
    FOR EACH ROW
DECLARE
    liczbaUczestnikow    number := 0;
    minLiczbaUczestnikow number := 0;
    liczbaRuchow         number := 0;
BEGIN
    select count(*) INTO liczbaRuchow from Ruch where idRozgrywki = :NEW.idRozgrywki;
    IF (liczbaRuchow = 0) THEN
        select count(*) INTO liczbaUczestnikow from Uczestnicy where id = :NEW.idRozgrywki;
        select minGraczy
        INTO minLiczbaUczestnikow
        from Gra
        where nazwa = (select gra from Rozgrywka where id = :NEW.idRozgrywki);
        if (liczbaUczestnikow < minLiczbaUczestnikow) THEN
            raise_application_error(-20010, 'Zbyt mala liczba graczy by rozpoczac rozgrywke!');
        END IF;
    END IF;
END;
/

create table Znajomosc
(
    kto  varchar2(30) not null references Gracz,
    kogo varchar2(30) not null references Gracz,
    constraint znajomosc_pk primary key (kto, kogo)
);

CREATE OR REPLACE TRIGGER sprawdzZnajomosc
    BEFORE INSERT
    ON Znajomosc
    FOR EACH ROW
BEGIN
    IF (:NEW.kto = :NEW.kogo) THEN
        raise_application_error(-20011, 'Gracz nie moze znac samego siebie');
    END IF;
END;
/

create table SystemWyliczania
(
    id      int                  not null primary key,
    formula varchar2(300) unique not null,
    --formula jako create function, tu trzymamy tylko nazwe
    --dynamicznie wywołujemy w php lub po stronie serwera (EXECUTE IMMEDIATE
    opis    varchar2(1000)       not null
);

create table Ranking
(
    kto              varchar2(30)     not null references Gracz,
    gra              varchar2(150)    references Gra,
    punktyRankingowe double precision not null,
    idFormuly        int              not null references SystemWyliczania,
    constraint ranking_pk primary key (kto, gra, idFormuly)
);

create table HistoriaRankingu
(
    kto              varchar2(30)     not null references Gracz,
    gra              varchar2(150)    references Gra,
    punktyRankingowe double precision not null,
    idFormuly        int              not null references SystemWyliczania,
    --CONSTRAINT ranking_fk FOREIGN KEY (kto, gra, idFormuly) REFERENCES RANKING,
    ktora int not null,
    constraint rankingHist_pk primary key (kto, gra, idFormuly, ktora)
);

-- zakladamy ze id tej formuly wynosi 1
create or replace procedure Weighted1v1(idRozgrywki number)
    IS
    loginOponenta  varchar2(30)  := null;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestOponent integer := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;
    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select uczestnik
    into loginOponenta
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 1;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 500 + 10 * wagaGry, 1);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + 10 * wagaGry
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 1;
    end if;

    select count(*) into czyJestOponent from Ranking where kto = loginOponenta and
                                                           nazwaGry = gra and
                                                           idFormuly = 1;

    if (czyJestOponent = 0) then
        insert into RANKING values (loginOponenta, nazwaGry, 500 - 10 * wagaGry, 1);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe - 10 * wagaGry
        where kto = loginOponenta
          and nazwaGry = gra
          and idFormuly = 1;
    end if;
END;
/

-- id formuly = 2
create or replace procedure Standard1v1(idRozgrywki number)
    IS
    loginOponenta  varchar2(30)  := null;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestOponent integer := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;
    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select uczestnik
    into loginOponenta
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 2;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 510, 2);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + 10
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 2;
    end if;

    select count(*) into czyJestOponent from Ranking where kto = loginOponenta and
                                                           nazwaGry = gra and
                                                           idFormuly = 2;

    if (czyJestOponent = 0) then
        insert into RANKING values (loginOponenta, nazwaGry, 490, 2);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe - 10
        where kto = loginOponenta
          and nazwaGry = gra
          and idFormuly = 2;
    end if;
END;
/

-- id formuly = 5
create or replace procedure Delta1v1(idRozgrywki number)
    IS
    loginOponenta  varchar2(30)  := null;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestOponent integer := 0;
    eloWygranego double precision := 0;
    eloOponenta double precision := 0;
    deltaWminusP double precision := 0;
    ileWygranemu double precision := 0;
    ileOponentowi double precision := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;
    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select uczestnik
    into loginOponenta
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 5;

    select count(*) into czyJestOponent from Ranking where kto = loginOponenta and
                                                           nazwaGry = gra and
                                                           idFormuly = 5;
    if (czyJestWygrany = 0) then
        eloWygranego := 1000;
    end if;

    if (czyJestOponent = 0) then
        eloOponenta := 1000;
    end if;

    deltaWminusP := eloWygranego - eloOponenta;

    if (eloWygranego > eloOponenta) then
        ileWygranemu := 20 * (eloWygranego / (eloOponenta + eloWygranego));
        ileOponentowi := 15 * (eloOponenta / (eloWygranego + eloOponenta));
    elsif (eloWygranego < eloOponenta) then
        ileWygranemu := 20 * (eloWygranego / (eloOponenta + eloWygranego));
        ileOponentowi := 30 * (eloOponenta / (eloWygranego + eloOponenta));
    else
        ileWygranemu := 15;
        ileOponentowi := 15;
    end if;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 500 + ileWygranemu, 5);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + ileWygranemu
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 5;
    end if;

    if (czyJestOponent = 0) then
        insert into RANKING values (loginOponenta, nazwaGry, 500 - ileOponentowi, 5);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe - ileOponentowi
        where kto = loginOponenta
          and nazwaGry = gra
          and idFormuly = 5;
    end if;
END;
/

-- id formuly = 6
create or replace procedure Elo1v1(idRozgrywki number)
    IS
    loginOponenta  varchar2(30)  := null;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestOponent integer := 0;
    eloWygranego double precision := 0;
    eloOponenta double precision := 0;
    deltaWminusP double precision := 0;
    ileWygranemu double precision := 0;
    ileOponentowi double precision := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;
    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select uczestnik
    into loginOponenta
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 6;

    select count(*) into czyJestOponent from Ranking where kto = loginOponenta and
                                                           nazwaGry = gra and
                                                           idFormuly = 6;
    if (czyJestWygrany = 0) then
        eloWygranego := 1000;
    end if;

    if (czyJestOponent = 0) then
        eloOponenta := 1000;
    end if;

    deltaWminusP := eloWygranego - eloOponenta;

    if (eloWygranego > eloOponenta) then
        ileWygranemu := 32 * (eloWygranego / (eloOponenta + eloWygranego));
        ileOponentowi := 32 * (eloOponenta / (eloWygranego + eloOponenta));
    elsif (eloWygranego < eloOponenta) then
        ileWygranemu := 32 * (eloWygranego / (eloOponenta + eloWygranego));
        ileOponentowi := 32 * (eloOponenta / (eloWygranego + eloOponenta));
    else
        ileWygranemu := 32;
        ileOponentowi := 32;
    end if;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 500 + ileWygranemu, 6);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + ileWygranemu
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 6;
    end if;

    if (czyJestOponent = 0) then
        insert into RANKING values (loginOponenta, nazwaGry, 500 - ileOponentowi, 6);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe - ileOponentowi
        where kto = loginOponenta
          and nazwaGry = gra
          and idFormuly = 6;
    end if;
END;
/

-- zakladamy ze id tej formuly wynosi 3
create or replace procedure StandardGrupowe(idRozgrywki number)
    IS
    iloscGraczy    number        := 0;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestUczestnik integer := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;

    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select count(*)
    into iloscGraczy
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 3;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 510, 3);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + 10
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 3;
    end if;

    FOR row IN (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
        LOOP
            IF (row.uczestnik <> loginWygranego) THEN
                select count(*) into czyJestUczestnik from Ranking where kto = row.uczestnik and
                                                           nazwaGry = gra and
                                                           idFormuly = 3;

                if (czyJestUczestnik = 0) then
                    insert into RANKING values (row.uczestnik, nazwaGry, 490, 3);
                else
                    update RANKING
                    SET punktyRankingowe = punktyRankingowe - (10 / iloscGraczy)
                    where kto = row.uczestnik
                      and nazwaGry = gra
                      and idFormuly = 3;
                end if;
            END IF;
        END LOOP;
END;
/

-- id formuly = 4
create or replace procedure WeightedGrupowe(idRozgrywki number)
    IS
    iloscGraczy    number        := 0;
    loginWygranego varchar2(30)  := null;
    nazwaGry       varchar2(100) := null;
    wagaGry        number        := 0;
    czyJestWygrany integer := 0;
    czyJestUczestnik integer := 0;
BEGIN
    select gra into nazwaGry from Rozgrywka where idRozgrywki = id;

    select waga into wagaGry from Rozgrywka where idRozgrywki = id;

    select count(*)
    into iloscGraczy
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) != uczestnik;

    select uczestnik
    into loginWygranego
    from (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
    where (select ktoWygral from Rozgrywka where Rozgrywka.id = idRozgrywki) = uczestnik;

    select count(*) into czyJestWygrany from Ranking where kto = loginWygranego and
                                                           nazwaGry = gra and
                                                           idFormuly = 4;
    iloscGraczy := iloscGraczy - 1;

    if (czyJestWygrany = 0) then
        insert into RANKING values (loginWygranego, nazwaGry, 500 + 10 * wagaGry, 4);
    else
        update RANKING
        SET punktyRankingowe = punktyRankingowe + 10 * wagaGry
        where kto = loginWygranego
          and nazwaGry = gra
          and idFormuly = 4;
    end if;

    FOR row IN (select uczestnik from Uczestnicy where idRozgrywki = Uczestnicy.id)
        LOOP
            IF (row.uczestnik <> loginWygranego) THEN
                select count(*) into czyJestUczestnik from Ranking where kto = row.uczestnik and
                                                           nazwaGry = gra and
                                                           idFormuly = 4;

                if (czyJestUczestnik = 0) then
                    insert into RANKING values (row.uczestnik, nazwaGry, 500 - 10 * wagaGry, 4);
                else
                    update RANKING
                    SET punktyRankingowe = punktyRankingowe - (10 / iloscGraczy) * wagaGry
                    where kto = row.uczestnik
                      and nazwaGry = gra
                      and idFormuly = 4;
                end if;
            END IF;
        END LOOP;
END;
/

CREATE OR REPLACE TRIGGER aktualizujRanking
    BEFORE INSERT OR UPDATE
    ON Ruch
    FOR EACH ROW
DECLARE
    jakaFormula varchar2(50) := '';
    jakaGra varchar2(30);
    liczbaRuchow number(15);
    maxLudzi number(15);
BEGIN
    select count(*) INTO liczbaRuchow from Ruch where idRozgrywki = :NEW.idRozgrywki;
    IF (liczbaRuchow = 0) THEN
        select gra into jakaGra from Rozgrywka where :NEW.idRozgrywki = Rozgrywka.id;
        select maxGraczy into maxLudzi from Gra where nazwa = jakaGra;
        if (maxLudzi = 2) THEN
            Standard1v1(:NEW.idRozgrywki);
            Weighted1v1(:NEW.idRozgrywki);
            Delta1v1(:NEW.idRozgrywki);
            if (jakaGra = 'SZACHY') then
               Elo1v1(:NEW.idRozgrywki);
            end if;
        else
            StandardGrupowe(:NEW.idRozgrywki);
            WeightedGrupowe(:NEW.idRozgrywki);
        end if;
    END IF;
END;
/

CREATE OR REPLACE TRIGGER aktualizujHistorie
    BEFORE INSERT OR UPDATE
    ON Ranking
    FOR EACH ROW
DECLARE
     ktora integer := 0;
BEGIN
    select count(*) into ktora from HistoriaRankingu where kto = :NEW.kto and gra = :NEW.gra and :NEW.idFormuly = idFormuly;
    ktora := ktora + 1;
    insert into HistoriaRankingu values (:NEW.kto, :NEW.gra, :NEW.punktyRankingowe, :NEW.idFormuly, ktora);
END;
/

insert into gra values ('szachy', 2, 2);
insert into gra values ('warcaby', 2, 2);
insert into gra values ('gomoku', 2, 2);
insert into gra values ('4-szachy', 4, 4);

insert into SystemWyliczania values (1, '+10/-10 * waga', 'Daje wygranemu +10 * wagaGry punktow rankingowych, natomiast przegranemu odejmuje 10 * wagaGry');
insert into SystemWyliczania values (2, '+10/-10', 'Wygranemu daje +10 punktow rankingowych, przegranemu odejmuje 10, nie patrzy na wage rozgrywki');
insert into SystemWyliczania values (3, '+10/(-10/ilosc przegranych)', 'Daje wygranemu +10 punktow rankingowych, natomiast przegranemu odejmuje 10 podzielone przez ilosc graczy ktorzy przegrali');
insert into SystemWyliczania values (4, '+10/(-10/ilosc przegranych) * waga', 'Daje wygranemu + 10 * wagaGry punktow rankingowych, natomiast przegranemu odejmuje 10 * wagaGry podzielone przez ilosc graczy ktorzy przegrali');
insert into SystemWyliczania values (5, 'Uproszczony system ELO', 'Patrzy po roznicy punktow rankingowych między graczami i stosownie zmienia ranking');
insert into SystemWyliczania values (6, 'Uproszczony system ELO desygnowany do szachow', 'Patrzy po roznicy punktow rankingowych między graczami i stosownie zmienia ranking, napisany specjalnie do szachow');
