DROP TABLE IF EXISTS `session_storage`;
CREATE TABLE `session_storage` (
    `storage_key` VARCHAR(255) NOT NULL default '',
    `storage_value` TEXT,
    `storage_timestamp` TIMESTAMP,
    PRIMARY KEY (`storage_key`)
);
