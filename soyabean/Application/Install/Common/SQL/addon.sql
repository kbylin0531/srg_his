/*
Navicat MySQL Data Transfer

Source Server         : bcc
Source Server Version : 50631
Source Host           : 182.61.39.187:3306
Source Database       : wp

Target Server Type    : MYSQL
Target Server Version : 50631
File Encoding         : 65001

Date: 2016-07-08 21:01:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wp_addons
-- ----------------------------
DROP TABLE IF EXISTS `wp_addons`;
CREATE TABLE `wp_addons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `type` tinyint(1) DEFAULT '0' COMMENT '插件类型 0 普通插件 1 微信插件 2 易信插件',
  `cate_id` int(11) DEFAULT NULL,
  `is_show` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `sti` (`status`,`is_show`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8 COMMENT='微信插件表';

-- ----------------------------
-- Records of wp_addons
-- ----------------------------
INSERT INTO `wp_addons` VALUES ('18', 'Wecome', '欢迎语', '用户关注公众号时发送的欢迎信息，支持文本，图片，图文的信息', '1', '{\"type\":\"1\",\"title\":\"\",\"description\":\"欢迎关注，请<a href=\"[follow]\">绑定帐号</a>后体验更多功能\",\"pic_url\":\"\",\"url\":\"\"}', '地下凡星', '0.1', '1389620372', '0', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('19', 'UserCenter', '微信用户中心', '实现3G首页、微信登录，微信用户绑定，微信用户信息初始化等基本功能', '1', '{\"score\":\"100\",\"experience\":\"100\",\"need_bind\":\"1\",\"bind_start\":\"0\",\"jumpurl\":\"\"}', '地下凡星', '0.1', '1390660425', '1', '0', '8', '1');
INSERT INTO `wp_addons` VALUES ('56', 'CustomMenu', '自定义菜单', '自定义菜单能够帮助公众号丰富界面，让用户更好更快地理解公众号的功能', '1', 'null', '凡星', '0.1', '1398264735', '1', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('111', 'ConfigureAccount', '帐号配置', '配置共众帐号的信息', '1', 'null', 'manx', '0.1', '1430730412', '1', '0', '4', '0');
INSERT INTO `wp_addons` VALUES ('101', 'CardVouchers', '微信卡券', '在微信平台创建卡券后，可配置到这里生成素材提供用户领取，它既支持电视台自己公众号发布的卡券，也支持由商家公众号发布的卡券', '1', 'null', '凡星', '0.1', '1421981659', '1', '0', '1', '1');
INSERT INTO `wp_addons` VALUES ('39', 'WeiSite', '微官网', '微官网', '1', 'null', '凡星', '0.1', '1395326578', '0', '0', '7', '1');
INSERT INTO `wp_addons` VALUES ('42', 'Leaflets', '微信宣传页', '微信公众号二维码推广页面，用作推广或者制作广告易拉宝，可以发布到QQ群微博博客论坛等等...', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1396056935', '0', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('48', 'CustomReply', '自定义回复', '这是一个临时描述', '1', 'null', '凡星', '0.1', '1396578089', '1', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('50', 'Survey', '微调研', '这是一个临时描述', '1', 'null', '凡星', '0.1', '1396883644', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('91', 'Invite', '微邀约', '微邀约用于邀约朋友一起消费优惠券,一起参加活动', '1', 'null', '无名', '0.1', '1418047849', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('93', 'Game', '互动游戏', '互动游管理中心，用于微游戏接入，管理绑定等', '1', 'null', '凡星', '0.1', '1418526180', '0', '0', '2', '0');
INSERT INTO `wp_addons` VALUES ('97', 'Ask', '微抢答', '用于电视互动答题', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1420680633', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('100', 'Forms', '通用表单', '管理员可以轻松地增加一个表单用于收集用户的信息，如活动报名、调查反馈、预约填单等', '1', 'null', '凡星', '0.1', '1421981648', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('106', 'RedBag', '微信红包', '实现微信红包的金额设置，红包领取，红包素材下载等', '1', 'null', '凡星', '0.1', '1427683711', '1', '0', '1', '1');
INSERT INTO `wp_addons` VALUES ('107', 'Guess', '竞猜', '节目竞猜 有奖竞猜 竞猜项目配置', '1', 'null', '无名', '0.1', '1428648367', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('108', 'WishCard', '微贺卡', 'Diy贺卡 自定贺卡内容 发给好友 后台编辑', '1', 'null', '凡星', '0.1', '1429344990', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('110', 'RealPrize', '实物奖励', '实物奖励设置', '1', 'null', 'aManx', '0.1', '1429514311', '1', '0', '1', '1');
INSERT INTO `wp_addons` VALUES ('126', 'DeveloperTool', '开发者工具箱', '开发者可以用来调试，监控运营系统的参数', '1', 'null', '凡星', '0.1', '1438830685', '1', '0', '7', '1');
INSERT INTO `wp_addons` VALUES ('128', 'BusinessCard', '微名片', '', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1438914856', '1', '0', '7', '1');
INSERT INTO `wp_addons` VALUES ('130', 'AutoReply', '自动回复', 'WeiPHP基础功能，能实现配置关键词，用户回复此关键词后自动回复对应的文件，图文，图片信息', '1', 'null', '凡星', '0.1', '1439194276', '1', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('133', 'Payment', '支付通', '微信支付,财付通,支付宝', '1', '{\"isopen\":\"1\",\"isopenwx\":\"1\",\"isopenzfb\":\"0\",\"isopencftwap\":\"0\",\"isopencft\":\"0\",\"isopenyl\":\"0\",\"isopenload\":\"1\"}', '拉帮姐派(陌路生人)', '0.1', '1439364373', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('134', 'Vote', '投票', '支持文本和图片两类的投票功能', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1439433311', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('137', 'Comment', '评论互动', '可放到手机界面里进行评论，显示支持弹屏方式', '1', '{\"min_time\":\"30\",\"limit\":\"15\"}', '凡星', '0.1', '1441593187', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('140', 'NoAnswer', '没回答的回复', '当用户提供的内容或者关键词系统无关识别回复时，自动把当前配置的内容回复给用户', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1442905427', '0', '0', '4', '1');
INSERT INTO `wp_addons` VALUES ('141', 'Servicer', '工作授权', '关注公众号后，扫描授权二维码，获取工作权限', '1', 'null', 'jacy', '0.1', '1443079386', '1', '0', '8', '1');
INSERT INTO `wp_addons` VALUES ('142', 'Coupon', '优惠券', '配合粉丝圈子，打造粉丝互动的运营激励基础', '1', 'null', '凡星', '0.1', '1443094791', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('143', 'HelpOpen', '帮拆礼包', '可创建一个帮拆活动，指定需要多个好友帮拆开才能得到礼包里的礼品', '1', 'null', '凡星', '0.1', '1443108219', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('144', 'Card', '会员卡', '提供会员卡基本功能：会员卡制作、会员管理、通知发布、优惠券发布等功能，用户可在此基础上扩展自己的具体业务需求，如积分、充值、签到等', '1', '{\"background\":\"1\",\"title\":\"\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97VIP\\u4f1a\\u5458\\u5361\",\"length\":\"80001\",\"instruction\":\"1\\u3001\\u606d\\u559c\\u60a8\\u6210\\u4e3a\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97VIP\\u4f1a\\u5458;\\r\\n2\\u3001\\u7ed3\\u5e10\\u65f6\\u8bf7\\u51fa\\u793a\\u6b64\\u5361\\uff0c\\u51ed\\u6b64\\u5361\\u53ef\\u4eab\\u53d7\\u4f1a\\u5458\\u4f18\\u60e0;\\r\\n3\\u3001\\u6b64\\u5361\\u6700\\u7ec8\\u89e3\\u91ca\\u6743\\u5f52\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97\\u6240\\u6709\",\"address\":\"\",\"phone\":\"\",\"url\":\"\",\"background_custom\":null}', '凡星', '0.1', '1443144735', '0', '0', '1', '1');
INSERT INTO `wp_addons` VALUES ('145', 'SingIn', '签到', '粉丝每天签到可以获得积分。', '1', '{\"random\":\"1\",\"score\":\"1\",\"score1\":\"1\",\"score2\":\"2\",\"hour\":\"0\",\"minute\":\"0\",\"continue_day\":\"3\",\"continue_score\":\"5\",\"share_score\":\"1\",\"share_limit\":\"1\",\"notstart\":\"\\u4eb2\\uff0c\\u4f60\\u8d77\\u5f97\\u592a\\u65e9\\u4e86,\\u7b7e\\u5230\\u4ece[\\u5f00\\u59cb\\u65f6\\u95f4]\\u5f00\\u59cb,\\u73b0\\u5728\\u624d[\\u5f53\\u524d\\u65f6\\u95f4]\\uff01\",\"done\":\"\\u4eb2\\uff0c\\u4eca\\u5929\\u5df2\\u7ecf\\u7b7e\\u5230\\u8fc7\\u4e86\\uff0c\\u8bf7\\u660e\\u5929\\u518d\\u6765\\u54e6\\uff0c\\u8c22\\u8c22\\uff01\",\"reply\":\"\\u606d\\u559c\\u60a8,\\u7b7e\\u5230\\u6210\\u529f\\r\\n\\r\\n\\u672c\\u6b21\\u7b7e\\u5230\\u83b7\\u5f97[\\u672c\\u6b21\\u79ef\\u5206]\\u79ef\\u5206\\r\\n\\r\\n\\u5f53\\u524d\\u603b\\u79ef\\u5206[\\u79ef\\u5206\\u4f59\\u989d]\\r\\n\\r\\n[\\u7b7e\\u5230\\u65f6\\u95f4]\\r\\n\\r\\n\\u60a8\\u4eca\\u5929\\u662f\\u7b2c[\\u6392\\u540d]\\u4f4d\\u7b7e\\u5230\\r\\n\\r\\n\\u7b7e\\u5230\\u6392\\u884c\\u699c\\uff1a\\r\\n\\r\\n[\\u6392\\u884c\\u699c]\",\"content\":\"\"}', '淡然', '1.11', '1444304566', '1', '0', '2', '1');
INSERT INTO `wp_addons` VALUES ('146', 'Reserve', '微预约', '微预约是商家利用微营销平台实现在线预约的一种服务，可以运用于汽车、房产、酒店、医疗、餐饮等一系列行业，给用户的出行办事、购物、消费带来了极大的便利！且操作简单， 响应速度非常快，受到业界的一致好评！', '1', 'null', '凡星', '0.1', '1444909657', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('151', 'Sms', '短信服务', '短信服务，短信验证，短信发送', '1', '{\"random\":\"1\"}', 'jacy', '0.1', '1446103430', '0', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('152', 'Exam', '微考试', '主要功能有试卷管理，题目录入管理，考生信息和考分汇总管理。', '1', 'null', '凡星', '0.1', '1447383107', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('153', 'Test', '微测试', '主要功能有问卷管理，题目录入管理，用户信息和得分汇总管理。', '1', 'null', '凡星', '0.1', '1447383593', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('162', 'Weiba', '微社区', '打造公众号粉丝之间沟通的社区，为粉丝运营提供更多服务', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1463801487', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('163', 'QrAdmin', '扫码管理', '在服务号的情况下，可以自主创建一个二维码，并可指定扫码后用户自动分配到哪个用户组，绑定哪些标签', '1', 'null', '凡星', '0.1', '1463999217', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('156', 'Draw', '比赛抽奖', '功能主要有奖品设置，抽奖配置和抽奖统计', '1', 'null', '凡星', '0.1', '1447389122', '1', '0', null, '1');
INSERT INTO `wp_addons` VALUES ('164', 'PublicBind', '一键绑定公众号', '', '1', '{\"random\":\"1\"}', '凡星', '0.1', '1465981270', '0', '0', null, '1');
SET FOREIGN_KEY_CHECKS=1;