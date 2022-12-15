create table nmapScan (
    ip varchar(32) not null check (ip > 0),
    mac varchar(32) not null,
    vendor varchar(50) not null,
    hostname varchar(100)not null,
    ports integer not null check (ports > 0),
    timestamp time not null,
    primary key (ip)
);
