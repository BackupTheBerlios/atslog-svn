CC = gcc
CFLAGS = -Wall -O
EXEEXT =
RM = rm
NAME=atslogd


CDRR_VER_DOT = \"1.09\"
CDRR_VER     = 109

EXES = $(NAME)$(EXEEXT)

all:	$(EXES)

$(NAME)$(EXEEXT):	$(NAME).c
	$(CC) $(CFLAGS) -DCDRR_VER=$(CDRR_VER_DOT) -o $@ $(NAME).c
	strip $@

clean:
	-$(RM) $(EXES)

diff:
	diff -u cdr_read.orig.c atslogd.c > cdr_read.c.diff
