# Uncomment ff you have to use libwrap
LIBWRAP=-lwrap

CC = gcc
CFLAGS += -s -Wall
RM = rm
NAME=atslogd

EXES = $(NAME)

all:	$(EXES)

$(NAME):	$(NAME).c
	$(CC) $(CFLAGS) -o $@ $(NAME).c $(LIBWRAP)

clean:
	-$(RM) $(EXES) *~


