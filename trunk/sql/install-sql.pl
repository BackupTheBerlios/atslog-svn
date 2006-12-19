#!/usr/bin/perl
# ATSlog version @version@ build @buildnumber@ www.atslog.dp.ua
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

# my $dbtype =input('Database type: (mysql or postgressql)',    'mysql');

my $root =input('Database manager',    'root');
my $rpsw =input('Manager\'s password', '');
my $dbhost =input('Database manager',    'localhost');
my $atslogdb =input('Database name',       'atslog');
my $atslogdu =input('Database user',       'atslog');
my $atslogdp =input('User\'s password',    randomPassword(8));

print "Connecting to 'DBI:mysql:mysql' as '$root'...\n";
my $mysql=DBI->connect("DBI:mysql:mysql",$root,$rpsw)
        ||die("Couls not connect to mysql as '$root'");
my $db   =$mysql;


$db->do("USE ${atslogdb}");
if(!$db->err){
    print("WARNING: Database \"${atslogdb}\" already exists.\nInstaller will drop it and create a new one.\n");
   if(input('Continue (yes|no)?','no')!~/^yes$/i) {
	die("Please backup your existing database and try again.\n");
    }
}

$db->do("DROP DATABASE IF EXISTS ${atslogdb}"); print $db->err ? $db->errstr : '';
$db->do("CREATE DATABASE ${atslogdb}"); print $db->err ? $db->errstr : '';
$db->do("USE ${atslogdb}"); print $db->err ? $db->errstr : '';



#die("GRANT USAGE ON *.* TO '${atslogdu}'@'localhost' IDENTIFIED BY '${atslogdp}'");
$db->do("delete from mysql.user where user=\'${atslogdu}\';"); print $db->err ? $db->errstr : '';
$db->do("GRANT USAGE ON *.* TO \'${atslogdu}\'@\'localhost' IDENTIFIED BY \'${atslogdp}\' WITH GRANT OPTION;"); print $db->err ? $db->errstr : '';
$db->do("GRANT ALL PRIVILEGES ON ${atslogdb}.* TO \'${atslogdu}\'@\'localhost\'"); print $db->err ? $db->errstr : '';
$db->do("FLUSH PRIVILEGES;"); print $db->err ? $db->errstr : '';

print "Creating tables...\n";
my $row;
my $cmd ='';
my $cmt ='';

open(DATA,"createsqltables.mysql.sql") || die print("Can open SQL");
readsql();
close(DATA);

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
  - _ % # |
  0 1 2 3 4 5 6 7 8 9");

 srand;

 for (my $i=0; $i <= $password_length ;$i++) {
  $_rand = int(rand 41);
  $password .= $chars[$_rand];
 }
 return $password;
}