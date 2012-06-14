CREATE TABLE users (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    oauth_provider varchar(10),
    oauth_uid text,
    oauth_token text,
    oauth_secret text,
    username text,
    PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  lookup (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    last_crawl DATE NOT NULL,
    PRIMARY KEY (username),
    UNIQUE (id)
) ENGINE=MyISAM;

CREATE TABLE  userDetails (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    userdata text NOT NULL
) ENGINE=MyISAM;