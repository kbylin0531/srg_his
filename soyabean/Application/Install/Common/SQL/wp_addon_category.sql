/*
Navicat MySQL Data Transfer

Source Server         : bcc
Source Server Version : 50631
Source Host           : 182.61.39.187:3306
Source Database       : wp

Target Server Type    : MYSQL
Target Server Version : 50631
File Encoding         : 65001

Date: 2016-07-08 21:06:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wp_addon_category
-- ----------------------------
DROP TABLE IF EXISTS `wp_addon_category`;
CREATE TABLE `wp_addon_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `title` varchar(255) DEFAULT NULL COMMENT '分类名',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='插件分类表';

-- ----------------------------
-- Records of wp_addon_category
-- ----------------------------
INSERT INTO `wp_addon_category` VALUES ('1', null, '奖励功能', '4');
INSERT INTO `wp_addon_category` VALUES ('2', null, '互动功能', '3');
INSERT INTO `wp_addon_category` VALUES ('7', '0', '高级功能', '10');
INSERT INTO `wp_addon_category` VALUES ('4', null, '公众号管理', '20');
INSERT INTO `wp_addon_category` VALUES ('8', '0', '用户管理', '1');
SET FOREIGN_KEY_CHECKS=1;
