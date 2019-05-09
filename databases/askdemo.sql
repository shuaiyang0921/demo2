/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : ask123

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-03-18 12:26:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pre_account
-- ----------------------------
DROP TABLE IF EXISTS `pre_account`;
CREATE TABLE `pre_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(100) DEFAULT NULL COMMENT '用户名',
  `password` varchar(60) DEFAULT NULL COMMENT '密码',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `phone` varchar(80) DEFAULT NULL COMMENT '手机号码',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` varchar(40) DEFAULT NULL COMMENT '最后登录ip',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `type_id` int(10) unsigned NOT NULL COMMENT '账号类型外键',
  `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `city` varchar(150) DEFAULT NULL COMMENT '所在城市',
  PRIMARY KEY (`id`),
  KEY `fk_pre_account_pre_account_type_idx` (`type_id`),
  CONSTRAINT `fk_pre_account_pre_account_type` FOREIGN KEY (`type_id`) REFERENCES `pre_account_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账号表';

-- ----------------------------
-- Records of pre_account
-- ----------------------------

-- ----------------------------
-- Table structure for pre_account_log
-- ----------------------------
DROP TABLE IF EXISTS `pre_account_log`;
CREATE TABLE `pre_account_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `desc` text COMMENT '积分变动说明',
  `account_id` int(10) unsigned NOT NULL COMMENT '积分变动账户',
  `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分变动数量',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变动时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1、积分悬赏\n2、采纳得分\n3、签到\n4、充值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账目日志表';

-- ----------------------------
-- Records of pre_account_log
-- ----------------------------

-- ----------------------------
-- Table structure for pre_account_type
-- ----------------------------
DROP TABLE IF EXISTS `pre_account_type`;
CREATE TABLE `pre_account_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(160) DEFAULT NULL COMMENT '类型名称',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1、正常使用 0、禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账号类型表';

-- ----------------------------
-- Records of pre_account_type
-- ----------------------------

-- ----------------------------
-- Table structure for pre_ad
-- ----------------------------
DROP TABLE IF EXISTS `pre_ad`;
CREATE TABLE `pre_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) unsigned NOT NULL COMMENT '广告位置外键',
  `name` varchar(100) DEFAULT NULL COMMENT '广告名称',
  `link` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `code` text COMMENT '图片地址',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `link_man` varchar(100) DEFAULT NULL COMMENT '添加人',
  `link_email` varchar(60) DEFAULT NULL COMMENT '添加人邮箱',
  `link_phone` varchar(60) DEFAULT NULL COMMENT '添加人手机号码',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `orderby` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `target` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启浏览器新窗口',
  PRIMARY KEY (`id`),
  KEY `fk_pre_ad_pre_ad_position1_idx` (`pid`),
  CONSTRAINT `fk_pre_ad_pre_ad_position1` FOREIGN KEY (`pid`) REFERENCES `pre_ad_position` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告表';

-- ----------------------------
-- Records of pre_ad
-- ----------------------------

-- ----------------------------
-- Table structure for pre_ad_position
-- ----------------------------
DROP TABLE IF EXISTS `pre_ad_position`;
CREATE TABLE `pre_ad_position` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '广告位名称',
  `width` int(10) unsigned DEFAULT NULL COMMENT '宽度',
  `height` int(10) unsigned DEFAULT NULL COMMENT '高度',
  `desc` text COMMENT '描述',
  `style` text COMMENT '模板',
  `open` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0关闭1开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告位置表';

-- ----------------------------
-- Records of pre_ad_position
-- ----------------------------

-- ----------------------------
-- Table structure for pre_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `pre_auth_group`;
CREATE TABLE `pre_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '角色名称',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1启用 0禁用',
  `rules` text COMMENT '权限规则id列表',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限角色表';

-- ----------------------------
-- Records of pre_auth_group
-- ----------------------------

-- ----------------------------
-- Table structure for pre_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `pre_auth_group_access`;
CREATE TABLE `pre_auth_group_access` (
  `account_id` int(10) unsigned NOT NULL COMMENT '用户外键',
  `group_id` int(10) unsigned NOT NULL COMMENT '权限组group_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每个用户对应的权限规则';

-- ----------------------------
-- Records of pre_auth_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for pre_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `pre_auth_rule`;
CREATE TABLE `pre_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) unsigned NOT NULL COMMENT '父级id',
  `name` varchar(150) DEFAULT NULL COMMENT '规则唯一标识',
  `title` varchar(100) DEFAULT NULL COMMENT '规则中文名称',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1、正常 0禁用',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `condition` varchar(150) DEFAULT NULL COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- ----------------------------
-- Records of pre_auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for pre_comment
-- ----------------------------
DROP TABLE IF EXISTS `pre_comment`;
CREATE TABLE `pre_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `post_id` int(10) unsigned NOT NULL COMMENT '是哪个帖子的评论',
  `account_id` int(10) unsigned NOT NULL COMMENT '是哪个用户留言',
  `like` text COMMENT '点赞人的account_id 列表',
  `content` text,
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论时间',
  PRIMARY KEY (`id`),
  KEY `fk_pre_comment_pre_post1_idx` (`post_id`),
  CONSTRAINT `fk_pre_comment_pre_post1` FOREIGN KEY (`post_id`) REFERENCES `pre_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子评论表';

-- ----------------------------
-- Records of pre_comment
-- ----------------------------

-- ----------------------------
-- Table structure for pre_favorite
-- ----------------------------
DROP TABLE IF EXISTS `pre_favorite`;
CREATE TABLE `pre_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `post_id` int(10) unsigned NOT NULL COMMENT '帖子id',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `fk_pre_favorite_pre_post1_idx` (`post_id`),
  CONSTRAINT `fk_pre_favorite_pre_post1` FOREIGN KEY (`post_id`) REFERENCES `pre_post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏表';

-- ----------------------------
-- Records of pre_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for pre_friends
-- ----------------------------
DROP TABLE IF EXISTS `pre_friends`;
CREATE TABLE `pre_friends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account_id` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '账户id',
  `friends_id` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '好友id',
  `register_time` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='好友表';

-- ----------------------------
-- Records of pre_friends
-- ----------------------------

-- ----------------------------
-- Table structure for pre_link
-- ----------------------------
DROP TABLE IF EXISTS `pre_link`;
CREATE TABLE `pre_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '链接名称',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- ----------------------------
-- Records of pre_link
-- ----------------------------

-- ----------------------------
-- Table structure for pre_post
-- ----------------------------
DROP TABLE IF EXISTS `pre_post`;
CREATE TABLE `pre_post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cate_id` int(10) unsigned NOT NULL COMMENT '分类外键',
  `title` varchar(200) DEFAULT NULL COMMENT '帖子标题',
  `content` text COMMENT '帖子正文内容',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0无状态 1置顶 2精帖',
  `account_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `accept` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0：未采纳\n若采纳就填写评论id',
  `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '悬赏积分',
  `finish` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 未完成 1 已完成',
  PRIMARY KEY (`id`),
  KEY `fk_pre_post_pre_posts_cate1_idx` (`cate_id`),
  CONSTRAINT `fk_pre_post_pre_posts_cate1` FOREIGN KEY (`cate_id`) REFERENCES `pre_posts_cate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子表';

-- ----------------------------
-- Records of pre_post
-- ----------------------------

-- ----------------------------
-- Table structure for pre_posts_cate
-- ----------------------------
DROP TABLE IF EXISTS `pre_posts_cate`;
CREATE TABLE `pre_posts_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(200) DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发帖分类表';

-- ----------------------------
-- Records of pre_posts_cate
-- ----------------------------

-- ----------------------------
-- Table structure for pre_system
-- ----------------------------
DROP TABLE IF EXISTS `pre_system`;
CREATE TABLE `pre_system` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '配置中文名称',
  `key` varchar(255) DEFAULT NULL COMMENT '配置英文名称',
  `value` text COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of pre_system
-- ----------------------------
