#
# Table structure for table 'tx_mediapool_domain_model_video'
#
CREATE TABLE tx_mediapool_domain_model_video
(
	title           varchar(100) DEFAULT '' NOT NULL,
	slug            varchar(2048),
	description     text         DEFAULT '' NOT NULL,
	upload_date     int(11) unsigned DEFAULT '0' NOT NULL,
	link            varchar(100) DEFAULT '' NOT NULL,
	player_html     text         DEFAULT '' NOT NULL,
	video_id        varchar(100) DEFAULT '' NOT NULL,
	thumbnail       varchar(100) DEFAULT '' NOT NULL,
	thumbnail_large varchar(100) DEFAULT '' NOT NULL
);

#
# Table structure for table 'tx_mediapool_domain_model_playlist'
#
CREATE TABLE tx_mediapool_domain_model_playlist
(
	title       varchar(100) DEFAULT '' NOT NULL,
	slug        varchar(2048),
	link        varchar(100) DEFAULT '' NOT NULL,
	playlist_id varchar(100) DEFAULT '' NOT NULL,
	videos      int(11) unsigned DEFAULT '0' NOT NULL,
	categories  int(11) DEFAULT '0' NOT NULL,
	thumbnail   varchar(100) DEFAULT '' NOT NULL
);
