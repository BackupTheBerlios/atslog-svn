
SET client_min_messages = 'PANIC';
CREATE USER @sqlmasteruser@ PASSWORD '@sqlmaspasswd@' CREATEDB CREATEUSER;
SET SESSION AUTHORIZATION '@sqlmasteruser@';
SET client_encoding = 'WIN';
SET check_function_bodies = false;
CREATE DATABASE @sqldatabase@;
GRANT ALL ON DATABASE @sqldatabase@ TO @sqlmasteruser@;
