CREATE TABLE calls
(
    timeofcall timestamp NOT NULL,
    forwarded char(3),
    internally int2,
    co int2,
    way char(3) NOT NULL,
    number numeric(100) NOT NULL DEFAULT 0,
    duration int4 NOT NULL DEFAULT 0,
    cost numeric(100,3) DEFAULT 0
);
COMMENT ON TABLE public.calls IS 'www.ATSlog.dp.ua';