--
-- Tabellstruktur `vent`
--

CREATE TABLE IF NOT EXISTS `vent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobil` varchar(20) NOT NULL,
  `port` int(6) unsigned NOT NULL,
  `timeout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
