CREATE TABLE <?php echo $table_name; ?>
(
  "timestamp" character varying(14) NOT NULL,
  description character varying(100) NOT NULL,
  "group" character varying(100) NOT NULL,
  applied smallint DEFAULT 0,
  CONSTRAINT minion_migrations_pkey PRIMARY KEY ("timestamp" , "group" ),
  CONSTRAINT minion_migrations_timestamp_description_key UNIQUE ("timestamp" , description )
);