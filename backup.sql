-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Май 31 2011 г., 10:42
-- Версия сервера: 5.1.33
-- Версия PHP: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `web2face`
--

-- --------------------------------------------------------

--
-- Структура таблицы `wf_modules`
--

CREATE TABLE IF NOT EXISTS `wf_modules` (
  `id` varchar(127) NOT NULL,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `wf_modules`
--

INSERT INTO `wf_modules` (`id`, `name`) VALUES
('static', 'Текстовые блоки');

-- --------------------------------------------------------

--
-- Структура таблицы `wf_options`
--

CREATE TABLE IF NOT EXISTS `wf_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(127) NOT NULL,
  `name` varchar(127) NOT NULL,
  `value` text NOT NULL,
  `group` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `wf_options`
--

INSERT INTO `wf_options` (`id`, `title`, `name`, `value`, `group`) VALUES
(1, 'Заголовок сайта', 'title', 'Сайт Web2Face CMF', 'site');

-- --------------------------------------------------------

--
-- Структура таблицы `wf_pages`
--

CREATE TABLE IF NOT EXISTS `wf_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `url` varchar(127) NOT NULL,
  `title` varchar(127) NOT NULL,
  `name` varchar(127) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `wf_pages`
--

INSERT INTO `wf_pages` (`id`, `parent_id`, `url`, `title`, `name`, `order`) VALUES
(1, 0, '/homepage', 'Главная', 'default', 1),
(2, 0, '/news', 'Новости', 'news', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `wf_pages_widgets`
--

CREATE TABLE IF NOT EXISTS `wf_pages_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `title` varchar(127) NOT NULL,
  `options` text,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `wf_pages_widgets`
--

INSERT INTO `wf_pages_widgets` (`id`, `page_id`, `widget_id`, `title`, `options`, `order`) VALUES
(1, 1, 1, 'Первый текст', '{"id": "1"}', 1),
(2, 1, 1, 'Текст после первого, но всё ещё не второй', '{"id": "2"}', 2),
(6, 1, 3, 'Форма редактирования инфы о сайте', NULL, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `wf_widgets`
--

CREATE TABLE IF NOT EXISTS `wf_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` varchar(127) NOT NULL,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `wf_widgets`
--

INSERT INTO `wf_widgets` (`id`, `module_id`, `name`) VALUES
(1, 'static', 'static_block'),
(3, 'form', 'form');

-- --------------------------------------------------------

--
-- Структура таблицы `wf_widget_static`
--

CREATE TABLE IF NOT EXISTS `wf_widget_static` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `wf_widget_static`
--

INSERT INTO `wf_widget_static` (`id`, `name`, `content`) VALUES
(1, 'Test1', 'StaticWidget1<br />'),
(2, 'Test2', 'StaticWidget2');
