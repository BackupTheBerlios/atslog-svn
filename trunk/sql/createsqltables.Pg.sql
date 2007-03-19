CREATE TABLE calls (
    timeofcall timestamp without time zone NOT NULL,
    forwarded character(3),
    internally smallint,
    co smallint,
    way character(3),
    number varchar(65),
    duration integer DEFAULT 0 NOT NULL,
    cost numeric(100,3) DEFAULT 0
);
CREATE INDEX calls_co ON calls (co);
CREATE INDEX calls_internally ON calls (internally);
CREATE INDEX calls_timeofcall ON calls (timeofcall);
CREATE INDEX calls_cost ON calls (cost);
COMMENT ON TABLE calls IS 'www.ATSlog.dp.ua';



CREATE TABLE usersgroups (
    login character varying(25),
    groups character varying(25)
);
CREATE INDEX usersgroups_login ON usersgroups (login);
CREATE INDEX usersgroups_groups ON usersgroups (groups);
COMMENT ON TABLE usersgroups IS 'Permissions';
	
	
CREATE TABLE users (
    internally character varying(25) DEFAULT 0 NOT NULL,
    login character varying(25) NOT NULL,
    "password" character varying(100),
    firstname character varying(25),
    secondname character varying(25),
    lastname character varying(25),
    PRIMARY KEY ("internally")
);
CREATE INDEX users_login ON users (login);
COMMENT ON TABLE users IS 'Personnels';

				
CREATE TABLE extlines (
    line character varying(25) DEFAULT 0 NOT NULL,
    name character varying(25) NOT NULL
);
CREATE UNIQUE INDEX extlines_line ON extlines (line);
CREATE INDEX extlines_name ON extlines (name);
COMMENT ON TABLE extlines IS 'Names of external lines';
					
CREATE TABLE intphones (
  intnumber character varying(25) DEFAULT 0 NOT NULL,
  name character varying(25) NOT NULL
);
CREATE UNIQUE INDEX intphones_intnumber ON intphones (intnumber);
CREATE INDEX intphones_name ON intphones (name);
COMMENT ON TABLE intphones IS 'Names of internally phones';

				
CREATE TABLE unauth (
    username character varying(64),
    pass character varying(64),
    ip character varying(64),
    x_forwardeded_for character varying(64),
    logintime timestamp without time zone
);
CREATE INDEX unauth_username ON unauth (username);
CREATE INDEX unauth_pass ON unauth (pass);
CREATE INDEX unauth_ip ON unauth (ip);
CREATE INDEX unauth_logintime ON unauth (logintime);
COMMENT ON TABLE unauth IS 'Attempts of authentifications';
								    
								    
CREATE TABLE phonebook (
    login character varying(25),
    number numeric(100,0) DEFAULT 0,
    description character varying(255)
);
CREATE INDEX phonebook_login ON phonebook (login);
CREATE UNIQUE INDEX phonebook_number ON phonebook (number);
CREATE INDEX phonebook_description ON phonebook (description);
COMMENT ON TABLE phonebook IS 'Phone book';
	

CREATE TABLE country (
    id smallint NOT NULL,
    name character varying(50),
    PRIMARY KEY ("id")
);
COMMENT ON TABLE country IS 'List of countries with tel codes';
		
CREATE TABLE towns (
    id integer,
    id_country smallint,
    name character varying(50)
);
CREATE INDEX towns_ip ON towns (id);
CREATE INDEX towns_id_country ON towns (id_country);
COMMENT ON TABLE towns IS 'Towns codes with the links to country';

