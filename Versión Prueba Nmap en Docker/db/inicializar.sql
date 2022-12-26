CREATE TABLE IF NOT EXISTS nmapScan(
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

CREATE TABLE IF NOT EXISTS inspect(
    ip text
);

CREATE TABLE IF NOT EXISTS users(
    user VARCHAR(40),
    password varchar(100),
);



