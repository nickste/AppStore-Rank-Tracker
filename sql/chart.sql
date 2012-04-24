
--
-- Table structure for table `chart`
--
--NOTE: You sould change chart_name to something that describes the chart you are watching.

CREATE TABLE IF NOT EXISTS `chart_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=801 ;
