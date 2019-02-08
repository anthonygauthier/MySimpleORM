USE travis;
CREATE TABLE Users (
  IDUsers int(11) NOT NULL AUTO_INCREMENT,
  username varchar(45) DEFAULT NULL,
  description varchar(45) DEFAULT NULL,
  PRIMARY KEY (IDUsers)
);