CREATE TABLE IF NOT EXISTS nmap(
    ip varchar(32) not null,
    hostname varchar(100),
    port varchar(200),
    protocol text,
    service text,
    version text,
    vuln text,
    primary key (ip)
);


