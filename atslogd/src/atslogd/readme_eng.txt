                            CDR Reader for PBXs
                    (C) Alexey V. Kuznetsov, 2001-2002
                               avk@gamma.ru
                                     
      This program is intented for reading of the call detail records from
PBX  and putting them in common text file or in different files on day-by-
day  or  month-by-month basis. In case day-by-day or month-by-month  basis
the  support  of  specific PBX is required. Now  the  following  PBXs  are
supported:

     Definity
     Merlin
     Panasonic KX-TD816, KX-TD1232, KX-TA308, KX-TA616, KX-TA624 and
others with similar SMDR format.
     Meridian.
     GHX-616/36/46

      This  program runs under Win32 (console application) and  UNIX  that
support termios.h API to serial device (the most of modern UNIXs do that).

     The format of command string is following:

          cdr_read [-D dir] [-L logfile] [-d] [-e] [-b] [-a] [-o]
          [-s speed] [-c csize] [-p parity] [-f sbits] [-t type]
                                serial_dev

-D dir        the  directory there the files with records will be put; the
              current directory by default;
-L logfile    the log file for writing the informational or error messages
              from program; stderr (i.e. screen) by default;
-F filename
-s speed      the speed of serial device; 9600bps by default;
-c char_size  the number of data bits in character; valid values from 5 to
              8; 8 by default;
-p parity     the parity of serial device; valid values:
              e – even parity;
              o – odd parity;
              n – no parity (default);
              m – mark parity (Win32 only);
              s – space parity (Win32 only);
-f stop_bits  the number of stop bits; valid values 1 or 2; 1 by default;
-t type       the type of PBX:
              raw (all call records are put in one file with name “raw” in
              the –D directory);
              definity (if the value of “CDR Date Format” in “ch sys  cdr”
              is  “day/month” the files with name “MM.DD” will be  created
              in  the –D directory (MM – two digits month’s number,  DD  –
              two digits day’s number); if not the names will be “DD.MM”);
              merlin (the names of files will be in “MM.DD” format);
              panasonic (the names of files will be in “MM.DD” format);
              meridian;
              ghx.
-d            the  additional  debug  output will  be  made  (in  case  of
              problems please run the program with this switch and mail me
              its output);
-e            output the messages from program to stderr additionaly to –L
              file;
-a            write  the date at the beginning of each file (for  Definity
              only);
-o            additionally output call detail records to stdout;
-m            write  log files on month-by-month basis; in this  case  the
              format of the file name will be MM.log;
-b            become daemon (for UNIX only).
serial_dev    the  name  of  serial device (com1, com2 etc. for  Win32  or
              /dev/cuaa0, /dev/cuaa1 etc. for UNIX).

      At  the  input the delimiters of strings are symbols CR  (0x0D),  LF
(0x0A) or any their combination. Null strings (i.e. containing CR,  LF  or
any their combination) and NULL (0x00) symbols are ignored.

              WHERE TO GET THE NEWEST VERSION OF THIS PROGRAM
                                     
                         http://www.gamma.ru/~avk
                            Sorry for design :)

                              COPYRIGHT NOTES

      This  program  can  be freely distributed and  used  for  any  legal
purposes if the information about my authorship is preserved.

   Sorry for my English – it’s not my native language as you can see :)
                                 Good by!
                                     
                             Alexey Kuznetsov

