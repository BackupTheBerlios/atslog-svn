#!/usr/bin/perl

chdir("../libexec");
print("Creating index: ");
system("./genindex.sh");
print("DONE\n");
# processing libraries
opendir(DIRHANDLE, ".") || die "Cannot opendir .: $!";
while ($name = readdir(DIRHANDLE)) {
    if($name=~/^.+\.lib$/){
	openlib($name);
        #print "found file: $name\n";
    }
}
closedir(DIRHANDLE);

$output="\n";
# creating output
foreach $model (sort keys %models) {
     $output.="# $model:\n";
     @model_list = sort(split(',',$models{$model}));
    foreach $current_model(@model_list) {
         $output.="# \t\t$current_model\n";
    }
}


chdir("../include");

modconf("atslog.conf.default.in");
modconf("atslog.conf.default.rus.in");

sub modconf(){
    my $config=$_[0];
open(IN,$config) || die print("Can't open config $config\n");
open(OUT,">/tmp/conftmp");
$print_out=1;
while($line=<IN>) {
    if($line=~/^#$/){
	$print_out=1;
    }

    if($print_out) {print OUT $line;}
    if($line=~/^howmonth=/){
	$print_out=0;
	print OUT $output;
    }

}
close(IN);
close(OUT);
system("mv /tmp/conftmp ./$config");
}

sub openlib()
{
    my $vendor="";
    my $model="";
    my $filename=$_[0];
    open(IN,$filename) || die print("Can't open library: $filename\n");
while($line=<IN>) {
    if($line=~/# VENDOR: (.+)\n?/){
	$vendor=$1;
    }
    if($line=~/# MODELS: (.+)\n?/){
	if (exists($models{$vendor})) {	$models{$vendor}.=",".$1;}
	else  {	$models{$vendor}=$1;}
	#print "models=$1\n";
    }
}
 close(IN);
}
#$libs