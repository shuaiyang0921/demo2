/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : ask

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-03-19 12:27:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pre_admin
-- ----------------------------
DROP TABLE IF EXISTS `pre_admin`;
CREATE TABLE `pre_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(80) DEFAULT NULL COMMENT '用户名',
  `password` varchar(80) DEFAULT NULL COMMENT '密码',
  `salt` varchar(80) DEFAULT NULL COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `register_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(255) DEFAULT '1' COMMENT '状态 1启用 0禁用',
  `groupid` int(11) unsigned DEFAULT NULL COMMENT '权限组id',
  PRIMARY KEY (`id`),
  KEY `groupid` (`groupid`),
  CONSTRAINT `fk_pre_admin_pre_auth_group_1` FOREIGN KEY (`groupid`) REFERENCES `pre_auth_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of pre_admin
-- ----------------------------

-- ----------------------------
-- Table structure for pre_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `pre_auth_group`;
CREATE TABLE `pre_auth_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '角色名称',
  `status` tinyint(255) unsigned DEFAULT '1' COMMENT '状态：1启用 0禁用',
  `rules` text COMMENT '权限规则id列表',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限角色表';

-- ----------------------------
-- Records of pre_auth_group
-- ----------------------------

-- ----------------------------
-- Table structure for pre_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `pre_auth_rule`;
CREATE TABLE `pre_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(11) DEFAULT '0' COMMENT '父级id',
  `name` varchar(150) DEFAULT NULL COMMENT '规则唯一标识	',
  `title` varchar(100) DEFAULT NULL COMMENT '规则中文名称',
  `status` tinyint(255) DEFAULT '1' COMMENT '状态：1、正常 0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限验证规则';

-- ----------------------------
-- Records of pre_auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for pre_comment
-- ----------------------------
DROP TABLE IF EXISTS `pre_comment`;
CREATE TABLE `pre_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `postid` int(11) unsigned DEFAULT NULL COMMENT '帖子id',
  `userid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `like` text COMMENT '点赞人列表 1,2,3,4,5',
  `content` text COMMENT '评论内容',
  `register_time` int(11) DEFAULT '0' COMMENT '评论时间',
  `parentid` int(11) DEFAULT '0' COMMENT '上级id 无限极评论',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `postid` (`postid`),
  CONSTRAINT `fk_pre_comment_1` FOREIGN KEY (`postid`) REFERENCES `pre_post` (`id`),
  CONSTRAINT `fk_pre_comment` FOREIGN KEY (`userid`) REFERENCES `pre_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Records of pre_comment
-- ----------------------------

-- ----------------------------
-- Table structure for pre_config
-- ----------------------------
DROP TABLE IF EXISTS `pre_config`;
CREATE TABLE `pre_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '配置中文名称',
  `title` varchar(255) DEFAULT NULL COMMENT '配置英文名称',
  `value` text COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of pre_config
-- ----------------------------
INSERT INTO `pre_config` VALUES ('1', '积分兑换比率', 'pointPay', '5');

-- ----------------------------
-- Table structure for pre_favorite
-- ----------------------------
DROP TABLE IF EXISTS `pre_favorite`;
CREATE TABLE `pre_favorite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `userid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `postid` int(11) unsigned DEFAULT NULL COMMENT '帖子id',
  `register_time` int(11) DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `postid` (`postid`),
  KEY `userid` (`userid`),
  CONSTRAINT `fk_pre_favorite_1` FOREIGN KEY (`userid`) REFERENCES `pre_user` (`id`),
  CONSTRAINT `fk_pre_favorite` FOREIGN KEY (`postid`) REFERENCES `pre_post` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏表';

-- ----------------------------
-- Records of pre_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for pre_post
-- ----------------------------
DROP TABLE IF EXISTS `pre_post`;
CREATE TABLE `pre_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '正文内容',
  `userid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `accept` int(255) DEFAULT '0' COMMENT '采纳用户id',
  `point` int(255) DEFAULT '0' COMMENT '悬赏积分',
  `finish` int(255) DEFAULT '0' COMMENT '是否完成 1完成 0未完成',
  `register_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `state` int(255) DEFAULT '0' COMMENT '0无状态 1置顶 2精帖',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `fk_pre_post` FOREIGN KEY (`userid`) REFERENCES `pre_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='帖子表';

-- ----------------------------
-- Records of pre_post
-- ----------------------------
INSERT INTO `pre_post` VALUES ('1', '习近平主持召开学校思想政治理论课教师座谈会', '<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	3月21日至26日，国家主席习近平将对意大利、摩纳哥、法国进行国事访问，也由此开启2019年的首次出访行程。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	习近平主席此次出访为何选择这三个欧洲国家？他又如何看待中国与这些国家的关系？\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	我们用4个“一”，和你一起领会习近平主席与欧洲各国的“交往之道”。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<span style=\"font-weight:bolder;\">一座桥梁</span>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	意大利是习近平本次欧洲之行的首站。此访也是中国国家元首时隔10年再次对意大利进行国事访问。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	对于两国关系，习近平主席曾这样概括，中意同为文明古国，古老的丝绸之路将两国紧密相连，架起一座东西方文明交流互鉴的桥梁。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	的确，中意传统友好，两国人民都为各自古老的文明感到自豪，彼此相互欣赏和借鉴。马可·波罗、利玛窦等“丝路使者”在中意交往和东西文明交流史上发挥过重要作用，成为连通东西方文明的桥梁和纽带。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234220/1000\" class=\"content-picture\" />\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2017年2月22日，国家主席习近平在北京人民大会堂同意大利总统马塔雷拉举行会谈。会谈后，两国元首集体会见中意企业家委员会第四次会议和中意文化合作机制大会与会代表。新华社记者谢环驰摄\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	万里丝路，跨越古今。“一带一路”建设又将两国紧紧联系在了一起。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2017年，中国举办首届“一带一路”国际合作高峰论坛之时，习近平主席对来华出席论坛的时任意大利总理真蒂洛尼表示欢迎，希望意方继续在欧盟发挥积极作用，维护中欧关系良好发展势头。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	不久前，意大利总理孔特明确表示，希望参加在北京举行的第二届“一带一路”国际合作高峰论坛，与中国深化经贸合作以获得更多发展机会。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	此外，中意在文化、科技、教育、卫生、创新等各领域务实合作也全面开花。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234221/1000\" class=\"content-picture\" />\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2016年5月2日，中意首次联合警务巡逻在意大利罗马启动。在意大利罗马君士坦丁凯旋门外，中方警员与意方警员合影。新华社记者金宇摄\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	今年是中意建立全面战略伙伴关系15周年，明年两国将迎来建交50周年。在这样的重要节点，两大文明的现代碰撞又将擦出怎样的火花？值得期待。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<span style=\"font-weight:bolder;\">一个典范</span>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	摩纳哥，以蓝色海岸的瑰丽风光、蒙特卡洛国际杂技节、一级方程式锦标赛（F1）等闻名世界。习近平主席称赞它具有独特魅力，长期以来，走出了一条富有自身特色的发展之路。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234222/1000\" class=\"content-picture\" /><i class=\"desc\">图为摩纳哥蒙特卡洛的游船港口。新华社记者刘作文摄</i>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	这个有魅力的欧洲国家，国土面积仅约2平方公里，是个不折不扣的“微型国家”。中摩两国的体量差异是否会成为交往的阻碍？\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	习近平主席在评价两国关系时这样说道：中国和摩纳哥虽然相距遥远，国情存在显著差异，但两国坚持相互尊重、平等相待、合作共赢，双边关系发展得很好，为大小国家友好相处、共同发展树立了榜样。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	中摩两国一直保持着频繁的友好交往。摩纳哥国家元首阿尔贝二世亲王曾经先后10次访华。他是国际奥委会委员，积极支持和参与北京奥运会、南京青奥会等在华举办的重大赛事。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	阿尔贝二世去年9月访华时，习近平主席对他和摩纳哥王室长期致力于发展中摩关系表示了赞赏，并邀请他来华出席2022年冬奥会。习近平主席特别强调，中方一贯主张，国家不论大小、贫富、强弱，都是国际社会的平等一员。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234223/1000\" class=\"content-picture\" />\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2018年9月7日，国家主席习近平在北京人民大会堂同摩纳哥元首阿尔贝二世亲王举行会谈。这是会谈前，习近平在人民大会堂北大厅为阿尔贝二世举行欢迎仪式。新华社记者刘卫兵摄\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	当前，中国正在大力推进生态文明建设，积极参与多边气候治理进程，而摩纳哥在发展过程中尊重自然和历史，形成了独具特色的城市发展理念。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	“大”有需求，“小”有经验，中摩两国交往相得益彰，已经成为大小国家友好交往的典范。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<span style=\"font-weight:bolder;\">一份责任</span>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	习近平主席此访的最后一站定在了法国。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	他曾将中法关系称为“世界大国关系中的一对特殊关系”。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	为什么“特殊”？习近平主席这样说过：\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	——法国是第一个同新中国正式建交的西方大国。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	——中法同为联合国安理会常任理事国和有重要国际影响的大国，对世界和平与发展负有特殊重要责任。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	——当今世界存在很多不确定性，中方主张构建人类命运共同体，法方也持相似的理念。两国可以超越社会制度、发展阶段、文化传统差异，增进政治互信，充分挖掘合作潜力。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	从合作打击恐怖主义到共建新型国际关系，从加强在气候变化问题上的合作到共同维护多边主义，两国这份“特殊”关系，既是互利共赢，也是为世界作贡献。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	“新时代中法关系大有作为。”2018年新年伊始，习近平主席对来华访问的法国总统马克龙这样说道。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234224/1000\" class=\"content-picture\" />\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2018年1月9日，国家主席习近平在北京人民大会堂同法国总统马克龙举行会谈。这是会谈前，习近平在人民大会堂北大厅为马克龙举行欢迎仪式。新华社记者王晔摄\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2014年3月，习近平主席在中法建交50周年之际对法国进行国事访问。五年后，在中法建交55周年之际，习近平主席将再赴法国，见证两国关系又一个“特殊”的历史时刻。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<span style=\"font-weight:bolder;\">一种力量</span>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	这是最近四个月内中国国家元首对欧洲交往的日程表：\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2018年11月底至12月初，习近平主席访问西班牙、葡萄牙。2018年元首外交的收官之作留下了欧洲印迹。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2019年1月，应习近平主席邀请，芬兰总统尼尼斯托访华，成为新年到访中国的第一位欧洲领导人，拉开了2019年中欧交往的序幕。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	2019年3月，习近平主席新年首访，聚焦意大利、摩纳哥和法国。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	习近平主席曾说，中国和欧洲虽然远隔万里，但都生活在同一个时间、同一个空间之内，生活息息相关。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	因为息息相关，所以常来常往。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	<img src=\"https://inews.gtimg.com/newsapp_bt/0/8192234225/1000\" class=\"content-picture\" /><i class=\"desc\">2014年4月1日，国家主席习近平在比利时布鲁日欧洲学院发表重要演讲。新华社记者庞兴雷摄</i>\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	对于当前形势下中欧合作契合点，习近平主席作出这样的精辟概括：\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	作为最大的发展中国家和最大的发达国家联合体，中欧是维护世界和平的“两大力量”；\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	作为世界上两个重要经济体，中欧是促进共同发展的“两大市场”；\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	作为东西方文化的重要发祥地，中欧是推动人类进步的“两大文明”。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	对于中欧关系前景，习近平主席也这样说过：\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	“中国和欧盟都在经历人类历史上前所未有的改革进程，都在走前人没有走过的路。”\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	“国际形势越复杂，中欧关系稳定发展越具有重要意义。”\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	世界潮流，浩浩荡荡。中欧合作已成为推动国际格局稳定发展的重要力量。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	习近平主席出访之日，正值春分节气。春风春雨润人心。在万物复苏的美好季节，相信中国外交必将取得新的更大收获。\r\n</p>\r\n<p class=\"one-p\" style=\"font-family:&quot;font-size:16px;\">\r\n	记者：温馨、马卓言\r\n</p>', '1', '0', '100', '0', '1552969351', '0');

-- ----------------------------
-- Table structure for pre_user
-- ----------------------------
DROP TABLE IF EXISTS `pre_user`;
CREATE TABLE `pre_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(100) DEFAULT NULL COMMENT '用户名',
  `password` varchar(60) DEFAULT NULL COMMENT '密码',
  `salt` varchar(60) DEFAULT NULL COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `phone` varchar(80) DEFAULT NULL COMMENT '手机号码',
  `sex` tinyint(255) DEFAULT '1' COMMENT '1男 0女',
  `content` text COMMENT '个人签名',
  `point` int(255) DEFAULT '0' COMMENT '积分',
  `register_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `status` int(255) DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of pre_user
-- ----------------------------
INSERT INTO `pre_user` VALUES ('1', 'demo', 'aa84f5812d2c59bf499c6a286f81f6f3', 'qJYc4Hslq2', 'uploads/201903190913284268.gif', '13512644553', '1', 'sdfghgfdsghgfd', '900', '1552957962', '0', '1');

-- ----------------------------
-- Table structure for pre_user_log
-- ----------------------------
DROP TABLE IF EXISTS `pre_user_log`;
CREATE TABLE `pre_user_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `desc` text COMMENT '描述内容',
  `userid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `point` int(255) DEFAULT '0' COMMENT '变动积分',
  `register_time` int(11) DEFAULT '0' COMMENT '注册时间',
  `status` int(255) DEFAULT '1' COMMENT '1、积分悬赏 2、采纳得分 3、签到 4、充值',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `fk_pre_user_log` FOREIGN KEY (`userid`) REFERENCES `pre_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='积分消费记录表';

-- ----------------------------
-- Records of pre_user_log
-- ----------------------------
INSERT INTO `pre_user_log` VALUES ('1', '发布悬赏帖子：习近平主持召开学校思想政治理论课教师座谈会', '1', '100', '1552969351', '1');

-- ----------------------------
-- Table structure for pre_user_pay
-- ----------------------------
DROP TABLE IF EXISTS `pre_user_pay`;
CREATE TABLE `pre_user_pay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `point` int(255) unsigned DEFAULT NULL COMMENT '充值积分',
  `userid` int(10) unsigned DEFAULT NULL COMMENT '用户id',
  `register_time` int(11) DEFAULT '0' COMMENT '充值时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '1审核通过 0正在审核 -1未通过',
  `adminid` int(11) DEFAULT NULL COMMENT '管理员id',
  `content` text COMMENT '审核未通过的原因',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `adminid` (`adminid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pre_user_pay
-- ----------------------------
INSERT INTO `pre_user_pay` VALUES ('1', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('2', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('3', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('4', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('5', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('6', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('7', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('8', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('9', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('10', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('11', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('12', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('13', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('14', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('15', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('16', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('17', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('18', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('19', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('20', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('21', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('22', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('23', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('24', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('25', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('26', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('27', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('28', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('29', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('30', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('31', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('32', '150', '1', '1552960664', '0', null, null);
INSERT INTO `pre_user_pay` VALUES ('33', '150', '1', '1552960664', '-1', null, null);
INSERT INTO `pre_user_pay` VALUES ('34', '100', '1', '1552963854', '1', null, null);
