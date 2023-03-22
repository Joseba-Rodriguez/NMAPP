CREATE TABLE IF NOT EXISTS nmapIndividual(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text
);

CREATE TABLE IF NOT EXISTS nmapNow(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text
);

CREATE TABLE IF NOT EXISTS lastAnalyze(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text
);


CREATE TABLE IF NOT EXISTS inspectIndividual(
    idIpIndividual SERIAL PRIMARY KEY,
    ip text
);

CREATE TABLE IF NOT EXISTS inspectNow(
    idIpIndividual SERIAL PRIMARY KEY,
    ip text
);

CREATE TABLE IF NOT EXISTS users(
    userID VARCHAR(40),
    password varchar(100)
);

CREATE TABLE buttons (
    id SERIAL PRIMARY KEY,
    selection TEXT NOT NULL
);

CREATE TABLE stats (
    idTime SERIAL PRIMARY KEY,
    summary text
);

INSERT INTO buttons (selection) VALUES ('2Weeks');
CREATE EXTENSION IF NOT EXISTS pgcrypto;
INSERT INTO users (userID, password) 
VALUES ('NmappAdmin', 
        crypt('Tr4b4j0F1n!', gen_salt('bf'))
       );