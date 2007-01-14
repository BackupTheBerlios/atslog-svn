# Uncomment ff you have to use libwrap
LIBWRAP=-lwrap

CC = gcc
CFLAGS += -s -Wall
RM = rm
NAME=atslogd

CDRR_VER_DOT = \"@version@\"

EXES = $(NAME)

all:	$(EXES)

$(NAME):	$(NAME).c
	$(CC) $(CFLAGS) -DCDRR_VER=$(CDRR_VER_DOT) -o $@ $(NAME).c $(LIBWRAP)

clean:
	-$(RM) $(EXES) *~


