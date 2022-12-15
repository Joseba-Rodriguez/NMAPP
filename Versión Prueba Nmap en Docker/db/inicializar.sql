create table nmapScan (
    ip varchar(32) not null,
    hostname varchar(100),
    ports varchar(200),
    cve text,
    time varchar(20) not null,
    primary key (ip)
);
