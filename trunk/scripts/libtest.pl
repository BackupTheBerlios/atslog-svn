#!/usr/local/bin/perl

if (@ARGV[0] eq ''){
    die("USAGE: libtest <libname>\n");
}

open(IN,@ARGV[0]) || die print("Can't open library.\n");
while($line=<IN>) {
    if($line=~/# TESTLOG: (.+)\n?/){
	print "@ARGV[0] $1\n";
	open(PBX_DATA,"../textlogs/$1") || warn ("cannot open:$1");
	readlog(@ARGV[0]);
    }
}
close(IN);

sub readlog{
    require $_[0];
    $incoming_total=0;
    $outgoing_total=0;
    $duration_total=0;
    parsecurcalls();
    print("total: out: $incoming_total, in: $outgoing_total, dur.: $duration_total\n\n");
}

sub WriteRecord{
    my $time_of_call = $_[0];
    my $fwd = $_[1];
    my $int = $_[2];
    my $co = $_[3];
    my $way = $_[4];
    my $number = $_[5];
    my $duration = $_[6];
    $duration_total+=$duration;
    # print("LOG: ".$str."OUT: `$time_of_call`,`$fwd`,`$int`,`$co`,`$way`,`$number`,`$duration`\n\n");
    if($way==2) {$incoming_total++;}
    elsif($way==1) {$outgoing_total++;}
}

# ampto24 with russian and english language support
sub AmPmTo24(){
	my $return24='';
	my $hour=$_[0];
	my $AmPm=$_[1];
	if ($AmPm eq 'PM' || $AmPm eq '��'){
		if($hour < 12){
			$return24=$hour+12;
		}elsif($hour == 12){
			$return24=12;
		}
	}elsif($AmPm eq 'AM' || $AmPm eq '��'){
		if($hour < 12){
			$return24=$hour;
		}elsif($hour == 12){
			$return24=0;
		}
	}
	return $return24;
}
