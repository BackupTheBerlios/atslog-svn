# Uncomment ff you have to use libwrap
LIBWRAP=-lwrap

CC = gcc
CFLAGS += -s -Wall
RM = rm
NAME=atslogd


CDRR_VER_DOT = \"1.09\"
CDRR_VER     = 109

EXES = $(NAME)

all:	$(EXES)

$(NAME):	$(NAME).c
	$(CC) $(CFLAGS) -DCDRR_VER=$(CDRR_VER_DOT) -o $@ $(NAME).c $(LIBWRAP)

clean:
	-$(RM) $(EXES)

diff:
	diff -u cdr_read.orig.c atslogd.c > cdr_read.c.diff
