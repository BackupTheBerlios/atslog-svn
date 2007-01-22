#!/usr/local/bin/perl

if (@ARGV[0] eq ''){
    die("USAGE: libtest <libname>\n");
}
require @ARGV[0];
open(PBX_DATA,"-");
$incoming_total=0;
$outgoing_total=0;
$duration_total=0;
parsecurcalls();
print("total: out: $incoming_total, in: $outgoing_total, dur.: $duration_total\n");
sub WriteRecord{
    my $time_of_call = $_[0];
    my $fwd = $_[1];
    my $int = $_[2];
    my $co = $_[3];
    my $way = $_[4];
    my $number = $_[5];
    my $duration = $_[6];
    $duration_total+=$duration;
print("LOG: ".$str."OUT: `$time_of_call`,`$fwd`,`$int`,`$co`,`$way`,`$number`,`$duration`\n\n");
    if($way==2) {$incoming_total++;}
    elsif($way==1) {$outgoing_total++;}
}

