CREATE TABLE IF NOT EXISTS nmapScan(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text,
    vuln text
);

CREATE TABLE IF NOT EXISTS nmapIndividual(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text,
    vuln text
);

CREATE TABLE IF NOT EXISTS lastAnalyze(
    ip VARCHAR(40),
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text,
    vuln text
);


CREATE TABLE IF NOT EXISTS inspect(
    ip text
);

CREATE TABLE IF NOT EXISTS inspectIndividual(
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


INSERT INTO buttons (selection) VALUES ('daily');