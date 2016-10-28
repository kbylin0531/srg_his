/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50631
Source Host           : localhost:3306
Source Database       : soya

Target Server Type    : MYSQL
Target Server Version : 50631
File Encoding         : 65001

Date: 2016-07-03 22:09:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for syr_member_role
-- ----------------------------
DROP TABLE IF EXISTS `syr_member_role`;
CREATE TABLE `syr_member_role` (
  `role` int(11) NOT NULL COMMENT '角色ID',
  `member` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`role`,`member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syr_member_role
-- ----------------------------

-- ----------------------------
-- Table structure for syr_mgroup_member
-- ----------------------------
DROP TABLE IF EXISTS `syr_mgroup_member`;
CREATE TABLE `syr_mgroup_member` (
  `mgroup` int(11) NOT NULL,
  `member` int(11) NOT NULL,
  PRIMARY KEY (`mgroup`,`member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syr_mgroup_member
-- ----------------------------

-- ----------------------------
-- Table structure for syr_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `syr_role_permission`;
CREATE TABLE `syr_role_permission` (
  `role` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  PRIMARY KEY (`role`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syr_role_permission
-- ----------------------------

-- ----------------------------
-- Table structure for sy_member
-- ----------------------------
DROP TABLE IF EXISTS `sy_member`;
CREATE TABLE `sy_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'uid',
  `username` varchar(255) NOT NULL COMMENT '用户名称',
  `sex` enum('0','1') NOT NULL DEFAULT '1' COMMENT '性别，0为男，1为女',
  `nickname` varchar(255) NOT NULL DEFAULT 'Unnamed' COMMENT '昵称，不必唯一',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `reg_ip` varchar(32) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(32) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1' COMMENT '用户状态，0为禁用状态，1为可用状态',
  `birthday` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` mediumtext COMMENT '头像资料，可以是图片的base64码，也可以是<img>的src属性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `namekey` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_member
-- ----------------------------
INSERT INTO `sy_member` VALUES ('1', 'admin', '1', 'Administrator', '15658070289', '784855684@qq.com', null, null, null, null, '1', '1992-05-31', 'd93a5def7511da3d0f2d171d9c344e91', 'data:image/jpg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCABkAGQDASIAAhEBAxEB/8QAHAAAAgEFAQAAAAAAAAAAAAAAAAcGAgMEBQgB/8QAOxAAAQMDAwEFBgMFCQEAAAAAAQIDBAAFEQYSITEHE0FRYRQicYGRoTJCsRUWI4LBCCQ0Q1JikrLR8P/EABkBAAMBAQEAAAAAAAAAAAAAAAACAwEEBf/EACIRAAICAgICAgMAAAAAAAAAAAABAhEDIRIxBEEiMlFxkf/aAAwDAQACEQMRAD8A6IqtKaEp86uVyWdCVngGK9oooGCioHr7tEiaaWYkRtMu4495G7CGvLcfP0H2pQ3rtN1LcdyROMVs/ljJCPv1+9aoNmWdA6i1JbNPsoXcZAStw7W2U8uLOM8D5degqjSN7XqC2uTVRTGbLykNJUrcVJGOT4ZzngZ6da5LkNvzH3JDpcde/Gt1RKldepJ58a6J7IbheP2BDiXG17Ivd7mpLSSkEEk5WFdSc5ynI56CmlBJGXsY9FFFSGCqFp8aropgZYoqsoOeKKBKLlFFFKOWJ0tiDDelS3EtMNJK1rPQAUldW9q06S44xZECHG6d6sZdV6jwT9z8KlHbnc1RNOxYbasKlPZWPNCBn/sUUhcFxwk1SEfZoSnnJDynHVEqWrJUTkkk1tNGWFnUOq49qlvhiKpsuur3BKsDwGfEnFYJSEqbATuUSOM4wPOqZrcZ1a1ux3/c90upWlPT+aq/oWTS0Mtns3uUW+GPCWJVnkKS28+2tIPdbklQPkePDrTvisNxo7bDKQhltIQhI/KAMAVyxp2/3exo3WSZcWYqjlxD7iCk8cFIOST8qaOkO1dC2kN6lHdZIAkpZUkc+JTzx6j6VOUH2KpKxt0Vq7Xe4F1kut26S1JS2hK1LaO4DOeM+fFbSkHCiiilAKKKKACiqUqBqO9oGqomkNNSrnLUnvEpKWGs8uuEe6kf19M1tegsTPa3fP2xqiQ00smPDUY6PLKfxn/lx/LUDU93CgVj3Ckn5jwrIlNzopCLu2pqe4kSHEq6nvPfz8931yK1t6Kv2WXUkgtqCuKsl6G6jZdbk5Qt1R/ir6AflT6f/eNZ7JStwJWlKkMDG3w3eP0x/X4RiDIxsz7ygtISPM/iA+o+9bGMpToQyglIKilZPXg9Pnx9Ko9HL3s2zLrst5SgCGU9D/qPn8MnirjiUJIYIQ688tKVEjIGT+nh869jp/uJW2600UNqkbFODOxKTj1ySeB8T0xTC0FodUrSE+43RJafkxlCMFDBQMZC/moZ+FSllSRVYZe9GN2N3h+23dcFCyY76kFTSjwApW3I9QSAfPPpy/q5j7J0quWuoUgA7WSXloHkV4H0JzXTlJPs2HQUUUUgwUUUUAYzriGWluuKCG0AqUonAAHU0ndO2p7tQ1WdT3zcnT0B4t22GejpSeVqHlkDPmeOgpka5jyJWjL2xCBMlyG6lAT1J2ngfHpUX7JdRWL907FaI89gXDuikxicL3jKlcfU1RaViCu7dJjJ7Ty2wpO5EZtt30Vgn9CKyV6BlK7P5FzlNz/apSNsePGSgnaccrCuRu8MEH6mo7qVpU/UOpi1Gel3Jd4W2EJbUpwNp3cDHhyOB5DyphL/AHv1AhiVI7u2stELabkOlrZgce6nJGPXFPVUJPLxVdid0Owu262hxr405DfO8IS+NpS6UkNk56e9jBPjimS9oF6XPuTxje0SXEtyWN5yslKSlaMnx3AcetVvQdj8lq822JqCLIIVIdjhbjiMdCHQNycY6ZxTE0Ne7BEtMeBbrh3qEbi01KWA+gE5IyeFAZPIPTzqGeMpPnFnR43kKMeEo/0i409aZMt56e5cIzbqD7Qw7EdBWvd1GU4xjjAr24XbUljsLLaX4keDcVPKYEtspciNdUoJKsfhPAxx0pqMXaE9H75L6AnnhRwrgkHjr4VHdRSUXGI41EkGQtw7UBDeQlP5uByokAgdOvoTUXOqbKRfNtULvsqifuvIkzZkZp4v7EsvB4gITuVkAAHceM4zyNuDzTTRfp0hQUyqAyCMhp9RCz8SDx9KhVmtVweuq13WKmZ7xU2lxRTkcZWOCD1A6jgVJ7zarc9HQ3HjMszRy0lKAkk+SiPA+vx8KdZVJkckJY1smEB9ciMlbrZbc6KT4Z9D4ismo9o5p5MNbjjSmGVHDTROSBjr9c1IaqCCiiilAsVzb2jyrTB17Ld0y5i47cPPtJShqIMEL2bR7zhyfeOcZ455DK7cdWnTumPY4bymrjPyhC0KwW2xjerPhxwPjnwpWdgWl06iuj1xmbVQobiXFIPJdc6pSf8AaOvqa6IRpcmc05vlwiNbst0gbbbhcLiCJUgZQ0Rju0HwPjk9TzmmAIrO4EISkjjIFXaKk9u2UUUitLSEjASKjl50Np+7SRJlW1j2gKC+9QNqifXHX51JEKzwarpdopSZGBoiytFxUON7K6v8S2j1PmfP51guWV22d4VSO67z3faW/dBzxhaemfX9KmtRzXcjbYnYrA7yZJIaYaH4lKJ8PgMn4CsaT7MaraMO0NKTCcEZCUzCVNZAyEEEp3Y8hjNUxdJSXL2Jt3nImNJTtShTXPXI8cDHp1rfadgLt1pYYfKVP8rdUB1Uokn9a2dThjUCs5vJtngAAwBgV7RRVBAooooA5W7dLkbnrS4HduYgITHQnPiOVfcn6VOv7MBSjRNwcSNyjLG4DqB3aaiPahoi+KvGpbm3bCm2b1P94HAdyVHkjnPmSPCtb2EazhabRdbbdnlR2pYQUOd2XEpUMgggcjIIAOCOOa7JLlj+J5uKTWRuZ0Pc9WWWACZlwisqHBy6Mj6VGrl2t6ZgOKb9tcfdBALbTCiemepAH3pNXOW0uZdrg3JVOhNvYay4kqbQc7cA4HPPT4eFRK1Mvahv7bUZKRInPpaZRnoTgZJ8gMZP/tShh5bZaWRo6s09qtzUTDci22y4phuZ2SHG0IScdcZVn7Vs32L04n+AttPP+Y70HyTWbZrezabTDt8YYZitJaT6hIxms1JwfSpNHQl+TVx7RKypUu5ynVKPCUHYlPwxz9SayotqjMSfaNqnJGNoccUVEDxxnp0FbCilH4oKKKKDQoorwnApgPaKsqVzRRRllqSw1JiuMyG0uMuJKFoUMhQPUEUqe0bsu0zPjoubEZy3SkoP+CKW0nB4ykgiiirYvsc+b6i27NNB2nUM9K7k5MUPau6KEOhKVJx0PGacGleznT9s1Im5RGHUyWHni2CobU7UlIGMdAD9hRRVMhDH2MOiiiuY7StvpVyiilY6CqNxoorQPNxqkqJ60UUCs8ooorTD/9k=');
INSERT INTO `sy_member` VALUES ('7', 'sy_14672805526401', '1', '匿名用户_14672805526401', '', '', '::1', '2016-06-30 00:00:00', null, null, '1', null, 'd93a5def7511da3d0f2d171d9c344e91', null);
INSERT INTO `sy_member` VALUES ('8', 'lin', '1', '匿名用户_14672838720999', '', '', '::1', '2016-06-30 00:00:00', null, null, '0', null, 'd93a5def7511da3d0f2d171d9c344e91', null);

-- ----------------------------
-- Table structure for sy_member_group
-- ----------------------------
DROP TABLE IF EXISTS `sy_member_group`;
CREATE TABLE `sy_member_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'Unnamed' COMMENT '用户组名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '用户组描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_member_group
-- ----------------------------

-- ----------------------------
-- Table structure for sy_menu
-- ----------------------------
DROP TABLE IF EXISTS `sy_menu`;
CREATE TABLE `sy_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT 'Untitled' COMMENT '配置项名称',
  `order` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `value` varchar(16383) NOT NULL DEFAULT 'a:0:{}' COMMENT '菜单的配置值,默认为空数组的序列化值，最大程度为65535/4的下限值',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标 ',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '菜单项状态，1为可用，0为禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_menu
-- ----------------------------
INSERT INTO `sy_menu` VALUES ('1', '顶级菜单', '6500', 'a:2:{i:0;a:1:{s:2:\"id\";i:2;}i:1;a:2:{s:2:\"id\";i:5;s:8:\"children\";a:1:{i:0;a:2:{s:2:\"id\";i:6;s:8:\"children\";a:1:{i:0;a:2:{s:2:\"id\";i:7;s:8:\"children\";a:2:{i:0;a:1:{s:2:\"id\";i:3;}i:1;a:1:{s:2:\"id\";i:4;}}}}}}}}', '', '1');
INSERT INTO `sy_menu` VALUES ('2', '系统菜单', '0', 'a:2:{i:0;a:1:{s:2:\"id\";i:2;}i:1;a:1:{s:2:\"id\";i:1;}}', '', '1');
INSERT INTO `sy_menu` VALUES ('3', '3', '0', 'a:0:{}', '', '1');
INSERT INTO `sy_menu` VALUES ('4', '4', '0', 'a:0:{}', '', '1');
INSERT INTO `sy_menu` VALUES ('5', '其他', '0', 'a:0:{}', '', '1');
INSERT INTO `sy_menu` VALUES ('6', '5', '0', 'a:0:{}', '', '1');
INSERT INTO `sy_menu` VALUES ('7', '其他', '0', 'a:0:{}', 'dsds', '1');

-- ----------------------------
-- Table structure for sy_menu_item
-- ----------------------------
DROP TABLE IF EXISTS `sy_menu_item`;
CREATE TABLE `sy_menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT 'Untitled' COMMENT '菜单项标题',
  `value` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单项的值，一般是一个链接',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单项图标',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '菜单项状态，1为可用，0为禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_menu_item
-- ----------------------------
INSERT INTO `sy_menu_item` VALUES ('1', '菜单设置', '/Admin/System/menu', '', '1');
INSERT INTO `sy_menu_item` VALUES ('2', '后台首页', '/Admin/Index/index', '', '1');

-- ----------------------------
-- Table structure for sy_permission
-- ----------------------------
DROP TABLE IF EXISTS `sy_permission`;
CREATE TABLE `sy_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '权限名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '权限的描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_permission
-- ----------------------------

-- ----------------------------
-- Table structure for sy_permission_type
-- ----------------------------
DROP TABLE IF EXISTS `sy_permission_type`;
CREATE TABLE `sy_permission_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '权限名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_permission_type
-- ----------------------------

-- ----------------------------
-- Table structure for sy_role
-- ----------------------------
DROP TABLE IF EXISTS `sy_role`;
CREATE TABLE `sy_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '角色名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '用户描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sy_role
-- ----------------------------
INSERT INTO `sy_role` VALUES ('1', '管理员', '');
