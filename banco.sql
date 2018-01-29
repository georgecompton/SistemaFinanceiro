SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dbfinanceiro`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbcontapagar`
--

CREATE TABLE `tbcontapagar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `documento` varchar(255) NOT NULL,
  `dataPublicacao` date NOT NULL,
  `dataVencimento` date NOT NULL,
  `dataPagamento` date NOT NULL,
  `desconto` int(3) default 0,
  `valor` decimal(15,2) NOT NULL default 0,
  `valorPago` decimal(15,2) NOT NULL default 0,
  `status` varchar(255) NOT NULL,
  `duplicata` char(1),
  
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `tbcontareceber`
--

CREATE TABLE `tbcontareceber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `documento` varchar(255) NOT NULL,
  `dataPublicacao` date NOT NULL,
  `dataVencimento` date NOT NULL,
  `dataPagamento` date NOT NULL,
  `desconto` int(3) default 0,
  `valor` decimal(15,2) NOT NULL default 0,
  `valorRecebido` decimal(15,2) NOT NULL default 0,
  `status` varchar(255) NOT NULL,
  `duplicata` char(1),
  
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

