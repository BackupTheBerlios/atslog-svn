#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.com
# Copyright (C) 2003 Denis CyxoB www.yamiyam.dp.ua

BEGIN {
print "\nATSlog SQL database installer/updater\n\n";
}
use DBI;
use File::Copy; # copy/move functions

$config=$ARGV[0];
if ( ! -f $config) {
    die ("USAGE: install-sql <atslog_config>\n\nCant open \"$config\" file\n");
}

my $dbtype =input('Database type: (mysql or postgresql)',    'mysql');

my @drivers = DBI->available_drivers;
die "No drivers found!\n" unless @drivers; # should never happen

if ($dbtype !~ /^(mysql|postgresql)$/){
	die("Wrong database type '$dbtype'\n");
}

if ($dbtype eq "postgresql"){
	$sqltype="Pg";
	$dbname="template1";
}
else{ # mysql
	$sqltype="mysql";
	$dbname="mysql";
}

if (!grep { /^(${sqltype})$/ } @drivers) {
	die("Please install DBI:$sqltype driver\n");
}

my $root =input('Database manager',    'root');
my $rpsw =input('Manager\'s password', '');
my $dbhost =input('Database host',    'localhost');
my $atslogdb =input('Database name',       'atslog');
my $atslogdu =input('Database user',       'atslog');
my $atslogdp =input('User\'s password',    randomPassword(8));

my $dsn="DBI:$sqltype:database=$dbname;";
if($dbhost ne "localhost") { $dsn .= "host=$dbhost;";}

print "Connecting to '$dsn' as '$root'...\n";
my $db=DBI->connect($dsn,$root,$rpsw,{PrintError => 0})
        ||die("Could not connect to $sqltype as '$root'. ".$DBI::errstr);

print "Creating database...";
$db->do("CREATE DATABASE ${atslogdb};");
if($db->err){
	if($db->err==7 || $db->err==1007){ # mysql and Pg
		print "FAILED\n";
		print("WARNING: Database \"${atslogdb}\" already exists.\nInstaller will drop it and create a new one.\n");
		if(input('Continue (yes|no)?','no')!~/^yes$/i) {
			die("Please backup your existing database and try again.\n");
		}
		print "Drop database ${atslogdb}...";
		$db->do("DROP DATABASE  ${atslogdb};"); print $db->err ? $db->errstr : '';
		print "OK\n";
		$db->do("CREATE DATABASE ${atslogdb};");
	}
	elsif($db->err) {
		die($db->err.":".$db->errstr."\n");
	}
}
else {	print "OK\n";}
print "Creating user...";
if ($sqltype eq "mysql") {
	$db->do("delete from mysql.user where user=\'${atslogdu}\';"); print $db->err ? $db->errstr : '';
	$db->do("GRANT USAGE ON *.* TO \'${atslogdu}\'@\'${dbhost}' IDENTIFIED BY \'${atslogdp}\' WITH GRANT OPTION;"); print $db->err ? $db->errstr : '';
	$db->do("GRANT ALL PRIVILEGES ON ${atslogdb}.* TO \'${atslogdu}\'@\'localhost\'"); print $db->err ? $db->errstr : '';
	$db->do("FLUSH PRIVILEGES;"); print $db->err ? $db->errstr : '';
}
elsif($sqltype eq "Pg"){
	$db->do("SET client_min_messages = 'ERROR';"); print $db->err ? $db->errstr : '';
	$db->do("DROP USER ${atslogdu}");
	$db->do("CREATE USER ${atslogdu} PASSWORD '${atslogdp}' CREATEDB CREATEUSER;"); print $db->err ? $db->errstr : '';
	$db->do("SET SESSION AUTHORIZATION '${atslogdu}';"); print $db->err ? $db->errstr : '';
	$db->do("GRANT ALL ON DATABASE ${atslogdb} TO ${atslogdu};"); print $db->err ? $db->errstr : '';
}
print "OK\n";

if ($sqltype eq "mysql") {
	$db->do("USE ${atslogdb};"); print $db->err ? $db->errstr : '';
}
elsif ($sqltype eq "Pg") {
	$db->disconnect;
	$dsn="DBI:$sqltype:database=${atslogdb};";
	if($dbhost ne "localhost") { $dsn .= "host=$dbhost;";}
	print "Connecting to '$dsn' as '$root'...\n";
	$db=DBI->connect($dsn,$root,$rpsw,{PrintError => 0})
        ||die("Could not connect to $sqltype as '$root'. ".$DBI::errstr);
}

my $row;
my $cmd ='';
my $cmt ='';

open(DATA,"createsqltables.${sqltype}.sql") || die print("Can open SQL dump createsqltables.${sqltype}.sql");
readsql();
close(DATA);

exit();
open(DATA,"data.sql") || die print("Cant open SQL dump");
readsql();
close(DATA);

print("Patching configuration file...\n");
move($config,$config.".bak");
open IN,  $config.".bak" or die $!;
open OUT,  ">$config" or die $!;
while ($row =<IN>) {
    $row =~ s/^sqlhost=.*$/sqlhost=$dbhost/g;
    $row =~ s/^sqldatabase=.*$/sqldatabase=$atslogdb/g;
    $row =~ s/^sqlmasteruser=.*$/sqlmasteruser=$atslogdu/g;
    $row =~ s/^sqlmaspasswd=.*$/sqlmaspasswd=$atslogdp/g;
    print OUT $row;
}
close IN;close OUT;

print("Done :)\n");

sub input {
 my ($pr, $dv) =@_;
 print $pr, @_ >1 ? ' [' .(defined($dv) ? $dv : 'null') .']' :'', ': ';
 my $r =<STDIN>;
 chomp($r);
 $r eq '' ? $dv : $r
}

sub readsql {
while ($row =<DATA>) {
  chomp($row);
  if ($cmd && ($row =~/^#/ || ($cmd !~/^\s*\{/ && $cmd =~/;\s*$/) )) {
     my $v;
     chomp($cmd);
     #print $cmt ||$cmd, " -> ";
     if   ($cmd =~/^\s*\{/) {$v =eval($cmd);   print $@ ? $@ : ''}
     else {$v =$db->do($cmd); print $db->err ? $db->errstr : ''}
     #print ': ', defined($v) ? $v : 'null', "\n\n";
     $cmd ='';
     $cmt ='';
  }
  next if $row =~/^\s*#*\s*$/;
  if    ($row =~/^#/ && $cmd !~/^\s*\{/) {
        $cmt =$row;
  }
  elsif ($row =~/^\s*#/ || $row eq '') {
  }
  else {
        $cmd .=($cmd ? "\n" : '') .$row;
  }
}
}


sub randomPassword {
 my $password;
 my $_rand;

 my $password_length = $_[0];
 if (!$password_length) {
  $password_length = 10;
 }

 my @chars = split(" ",
 "a b c d e f g h i j k l m n o p q r s t u v w x y z 
  - _ % #
  0 1 2 3 4 5 6 7 8 9");

 srand;

 for (my $i=0; $i <= $password_length ;$i++) {
  $_rand = int(rand 41);
  $password .= $chars[$_rand];
 }
 return $password;
}
