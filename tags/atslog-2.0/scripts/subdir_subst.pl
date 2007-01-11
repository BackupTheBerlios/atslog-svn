#!/usr/local/bin/perl
# find or replace string expression in text files matching wildcard in sub-directory tree
# $Id: subdir_subst.pl,v 1.1 2005/04/21 16:29:05 mbole Exp $

use File::Find;
use Getopt::Std;
use Cwd;
use FileHandle;

#use diagnostics;
use warnings;
use strict;

my(
$BASEDIR,
$NEWSTRING,
$OLDSTRING,
$SUBST_EXPR,
$SUFFIX,
$WILDCARD,
);

our($opt_h, $opt_d, $opt_w, $opt_x, $opt_v, $opt_s);
getopts('hd:w:xvs');

# for tracking each time we change directories
my($PrevDir) = '';

&Init;
   
find(\&wanted, $BASEDIR);

exit;

#====begin subroutines==========

sub Syntax {

    return <<"EndOfSyntax";
subdir_subst -- find and optionally replace strings in files matching
               wildcard in subdirectory tree
Syntax:
   $0 [-h]
   $0 [-x] [-d directory] [-w wildcard] [-v] oldstring newstring
where
   -h = help
   -v = verbose
   -d = base directory of subtree (default = current)
   -w = regular expression to select filenames
   -s = include RCS/CVS directories (source code control)
   -x = execute mode

By default this script only prints what would be done but does not do it,
use the -x option to actually make the changes and backup original files
EndOfSyntax
}

sub wanted {

    my($old,            # original line in file
       $new,            # changed line in file
       $condition,      # 's///' expression to evaluate
       $found,          # did we find a match?
       @stat,           # file attributes to preserve
       $fname,          # the file we're working on
    );

    return if (   (! $opt_s)
               && ($File::Find::dir =~ /\b(RCS|CVS|.svn|text-base)$/) );

    return unless /$WILDCARD/o;
#    return unless (-f && -T _);
    return unless (-f);
    $fname = $_;
    if ($opt_v && ($PrevDir ne $File::Find::dir)) {
        print("Directory: $File::Find::dir\n");
        $PrevDir = $File::Find::dir;
    }

    open(THISFILE, "<$fname") or die "can't open $fname, $!\n";
    while ($new = $old = <THISFILE>) {
        $condition = '$new =~' . $SUBST_EXPR;
        if (eval $condition) {
            print $found ? "" : "Filename : $File::Find::name\n", "   old:", $old, "   new:", $new;
            $found++;
        }
    }
    close(THISFILE) or die "can't close $fname.$SUFFIX, $!\n";

    if ($opt_x && $found) {

        @stat = stat(_);
        print "changing $fname to $fname$SUFFIX\n" if $opt_v;
        rename($fname , $fname . $SUFFIX) or die "can't rename $fname, $!\n";
        open(THISFILE, "<".$fname.$SUFFIX) or die "can't read '$fname.$SUFFIX', $!\n";
        open(NEWFILE, ">$fname") or die "can't write $fname, $!\n";
        while (<THISFILE>) {
            eval $SUBST_EXPR;
            print NEWFILE $_;
        }
        close(THISFILE) or die "can't close $fname.$SUFFIX, $!\n";
        close(NEWFILE) or die "can't close $fname, $!\n";
        chown(@stat[4,5], $fname) or die "can't chown $fname, $!\n";
        chmod($stat[2], $fname) or die "can't chmod $fname, $!\n";
    }
}

sub Init {

    if (defined($opt_h)) {
        print Syntax();
        exit;
    }
    
        STDOUT->autoflush;

    $BASEDIR  = $opt_d || cwd();
    $WILDCARD = $opt_w || '.';  # match any filename
    # 'sds_sav' is just an arbitrary string associated with this script
    $SUFFIX = '.' . $$ . '.sds_sav';

    
    ($OLDSTRING = shift(@ARGV))
       or die("must supply at least one argument\n", Syntax());
    
    if (@ARGV == 0) {
        die("Must supply second arg if using -x option\n", Syntax()) if $opt_x;
        # if not using -x, then just search on the one string provided
        $NEWSTRING = $OLDSTRING;
    } elsif (@ARGV != 1) {
       die("requires two arguments: oldstring newstring\n", Syntax());
    } else {
       $NEWSTRING = shift(@ARGV);
    }
    
    $SUBST_EXPR = 's/'.$OLDSTRING.'/'.$NEWSTRING.'/g';
    
    print STDERR ($opt_x ? "" : "NON-", "EXECUTE MODE\n");
    if ($opt_v) {
        print "Base dir  = '$BASEDIR'\n";
        print "Wildcard  = '$WILDCARD'\n";
        print "Oldstring = '$OLDSTRING'\n";
        print "Newstring = '$NEWSTRING'\n";
    }
}
