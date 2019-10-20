CREATE DATABASE IF NOT EXISTS `ethereum`;
USE `ethereum`;

CREATE TABLE IF NOT EXISTS account (
    id integer primary key auto_increment,
    number char(42)
);

CREATE TABLE IF NOT EXISTS transaction (
    id integer primary key auto_increment,
    hash char(66),
    transactionIndex char(4),
    blockHash char(66),
    blockNumber char(8),
    `from` char(42),
    `to` char(42),
    v char(4),
    value varchar(255),
    accountId integer,
    FOREIGN KEY (accountId)
        REFERENCES account(id)
)
COMMENT = 'Basic info on transations';


CREATE TABLE IF NOT EXISTS transaction_queue (
    id integer primary key auto_increment,
    hash char(66),
    status tinyint COMMENT '0 - ready to read, 1 - in progress, 2 - done, 3 - fail',
    accountId integer,
    FOREIGN KEY (accountId)
        REFERENCES account(id)
)
COMMENT = 'Queue of incoming transaction to handle';