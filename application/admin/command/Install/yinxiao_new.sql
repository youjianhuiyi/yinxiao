/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : yinxiao_new

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 08/04/2020 13:45:01
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yin_admin
-- ----------------------------
DROP TABLE IF EXISTS `yin_admin`;
CREATE TABLE `yin_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '失败次数',
  `logintime` int(10) NULL DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '登录IP',
  `login_url` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '唯一登录链接',
  `token` varchar(59) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '级别',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_admin
-- ----------------------------
INSERT INTO `yin_admin` VALUES (1, 0, 'admin', '平台级总账号', '89dfa0769f58a7521715d4b39a982fd9', '871ba7', '/assets/img/avatar.png', '', 'admin@admin.com', 0, 1586278227, '127.0.0.1', NULL, '9e27f0f9-b511-427a-9fe4-d17d1471c821', 'normal', 0, '', 1492186163, 1586278227, NULL, 0);
INSERT INTO `yin_admin` VALUES (2, 0, 'boss1', '老板11', 'b0638043e0d0543cb474d5827c252cfa', 'AFQr8N', '/assets/img/avatar.png', '15207331234', 'boos2@email.com', 0, 1586318032, '127.0.0.1', NULL, '9a0ab6ac-6f63-4807-ba1e-a3778bf72c7f', 'normal', 1, '团队1', 1585381395, 1586318032, NULL, 1);
INSERT INTO `yin_admin` VALUES (3, 2, 'zuzhang1', '组长1', '8f76c504349831a884496e30992bf942', 'IJM8Kf', '/assets/img/avatar.png', '18812341234', 'zuzhang@qq.com', 0, NULL, NULL, NULL, '', 'normal', 1, '团队1', 1585381425, 1585560925, NULL, 2);
INSERT INTO `yin_admin` VALUES (4, 2, 'zuzhang2', '组长2', 'b0cf0994956a3e4327efe2542745a79b', '0OoWzP', '/assets/img/avatar.png', '15212341234', 'zz2@email.com', 0, NULL, NULL, NULL, '', 'normal', 1, '团队1', 1585553729, 1585558029, NULL, 0);
INSERT INTO `yin_admin` VALUES (5, 3, 'yewuyuan1', '业务员1', '9089a516c53f7570d8ebd5bf7513bb83', '30xpuF', '/assets/img/avatar.png', '17742530022', 'yuwuyuan1@email.com', 0, NULL, NULL, NULL, '', 'normal', 1, '团队1', 1585553940, 1585561068, NULL, 3);
INSERT INTO `yin_admin` VALUES (7, 4, 'ywy02', '业务员2', '4fb015513e938625322b110843d065a4', 'HiJw28', '/assets/img/avatar.png', '153123456', 'ywy02@eamil.com', 0, NULL, NULL, NULL, '', 'normal', 0, '未知团队', 1585561117, 1585561117, NULL, 3);

-- ----------------------------
-- Table structure for yin_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `yin_admin_log`;
CREATE TABLE `yin_admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 194 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_admin_log
-- ----------------------------
INSERT INTO `yin_admin_log` VALUES (1, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"a95d961a4f7050d6a1011b1c537fc158\",\"username\":\"admin\",\"captcha\":\"HTLM\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585484082, 0);
INSERT INTO `yin_admin_log` VALUES (2, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"4ba14d289a0df63f6abcab64c9a8c3f3\",\"group\":[\"2\"],\"row\":{\"username\":\"boss\",\"email\":\"boos@email.com\",\"nickname\":\"\\u8001\\u677f\\u53f7\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585491220, 0);
INSERT INTO `yin_admin_log` VALUES (3, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/3?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"12db32cbd351c29db9b57061fb57c70f\",\"group\":[\"3\"],\"row\":{\"username\":\"zuzhang\",\"email\":\"zuzhang@qq.com\",\"nickname\":\"\\u7ec4\\u957f\\u53f7\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585491229, 0);
INSERT INTO `yin_admin_log` VALUES (4, 1, 'admin', '/aLYPMkZvnQ.php/general.profile/update', '常规管理 个人资料 更新个人信息', '{\"__token__\":\"2e0d2f6fcd2488f7fefb4a101008805f\",\"row\":{\"avatar\":\"\\/assets\\/img\\/avatar.png\",\"email\":\"admin@admin.com\",\"nickname\":\"\\u5e73\\u53f0\\u7ea7\\u603b\\u8d26\\u53f7\",\"password\":\"\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585491251, 0);
INSERT INTO `yin_admin_log` VALUES (5, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585499898, 0);
INSERT INTO `yin_admin_log` VALUES (6, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"pid\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585499909, 0);
INSERT INTO `yin_admin_log` VALUES (7, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585499913, 0);
INSERT INTO `yin_admin_log` VALUES (8, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"pid\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585499921, 0);
INSERT INTO `yin_admin_log` VALUES (9, 1, 'admin', '/aLYPMkZvnQ.php/team/team/add?dialog=1', '团队管理 团队管理 添加', '{\"dialog\":\"1\",\"row\":{\"name\":\"\\u56e2\\u961f1\",\"admin_username\":\"\\u8001\\u677f1\",\"phone\":\"18812341234\",\"team_productions\":\"1\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585500035, 0);
INSERT INTO `yin_admin_log` VALUES (10, 1, 'admin', '/aLYPMkZvnQ.php/team/team/add?dialog=1', '团队管理 团队管理 添加', '{\"dialog\":\"1\",\"row\":{\"name\":\"\\u56e2\\u961f2\",\"admin_username\":\"\\u8001\\u677f2\",\"phone\":\"15207331234\",\"team_productions\":\"1\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585552703, 0);
INSERT INTO `yin_admin_log` VALUES (11, 1, 'admin', '/aLYPMkZvnQ.php/team/team/add?dialog=1', '团队管理 团队管理 添加', '{\"dialog\":\"1\",\"row\":{\"name\":\"\\u56e2\\u961f3\",\"admin_username\":\"\\u8001\\u677f3\",\"phone\":\"15207331235\",\"team_productions\":\"1\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585553496, 0);
INSERT INTO `yin_admin_log` VALUES (12, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"1f6d9192900db2cc1dd5c8d5a1da1764\",\"group\":[\"2\"],\"row\":{\"team\":\"1\",\"username\":\"boss1\",\"email\":\"boos@email.com\",\"nickname\":\"\\u8001\\u677f1\",\"password\":\"123456\",\"status\":\"normal\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585553533, 0);
INSERT INTO `yin_admin_log` VALUES (13, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"74ba9a4f395d2f74aeedfa108a9ddd55\",\"group\":[\"2\"],\"row\":{\"team_id\":\"1\",\"username\":\"boss1\",\"email\":\"boo1s@email.com\",\"nickname\":\"\\u8001\\u677f1\",\"password\":\"123456\",\"status\":\"normal\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585553928, 0);
INSERT INTO `yin_admin_log` VALUES (14, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"5734151d71cfdf51a4b442a0eda6e3bd\",\"group\":[\"2\"],\"row\":{\"team_id\":\"1\",\"username\":\"boss12\",\"email\":\"boo1s@email.com\",\"nickname\":\"\\u8001\\u677f1\",\"password\":\"123456\",\"status\":\"normal\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585553936, 0);
INSERT INTO `yin_admin_log` VALUES (15, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"f43b7817f080741685a57f63566d9991\",\"group\":[\"2\"],\"row\":{\"team_id\":\"1\",\"username\":\"boss12\",\"email\":\"boo11s@email.com\",\"nickname\":\"\\u8001\\u677f1\",\"password\":\"123456\",\"status\":\"normal\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585553940, 0);
INSERT INTO `yin_admin_log` VALUES (16, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/4?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"e1c63935488b8e2d9456f4a9486aff83\",\"row\":{\"pid\":\"0\",\"team_id\":\"2\",\"username\":\"boss2\",\"email\":\"boss2@email.com\",\"nickname\":\"\\u8001\\u677f2\",\"password\":\"123456\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585556302, 0);
INSERT INTO `yin_admin_log` VALUES (17, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/4?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"d1508962c617bf973aaa7d05dc74b0c3\",\"row\":{\"pid\":\"0\",\"team_id\":\"2\",\"username\":\"boss2\",\"email\":\"boss2@email.com\",\"nickname\":\"\\u8001\\u677f2\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585556435, 0);
INSERT INTO `yin_admin_log` VALUES (18, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"d8f67c17da32f12779004987c6ffbf4f\",\"row\":{\"pid\":\"0\",\"team_id\":\"0\",\"username\":\"boss\",\"email\":\"boos@email.com\",\"nickname\":\"\\u8001\\u677f\\u53f7\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585556836, 0);
INSERT INTO `yin_admin_log` VALUES (19, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"f1ef5e74683d01a99e3f988dcf7edfcc\",\"row\":{\"pid\":\"0\",\"team_id\":\"1\",\"username\":\"boss\",\"email\":\"boos@email.com\",\"nickname\":\"\\u8001\\u677f1\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585556855, 0);
INSERT INTO `yin_admin_log` VALUES (20, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/3?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"a458cd6df395c22a5e256cf1d52fddb1\",\"row\":{\"pid\":\"2\",\"team_id\":\"1\",\"username\":\"zuzhang1\",\"email\":\"zuzhang@qq.com\",\"nickname\":\"\\u7ec4\\u957f1\",\"phone\":\"18812341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557218, 0);
INSERT INTO `yin_admin_log` VALUES (21, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/4?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"9313349cd66fba541a50aadda5ab98a4\",\"row\":{\"pid\":\"0\",\"team_id\":\"2\",\"username\":\"boss2\",\"email\":\"boss2@email.com\",\"nickname\":\"\\u8001\\u677f2\",\"phone\":\"15212341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557295, 0);
INSERT INTO `yin_admin_log` VALUES (22, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/3?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"e726170902b438ddc9844339bcb68363\",\"row\":{\"pid\":\"2\",\"team_id\":\"1\",\"username\":\"zuzhang1\",\"email\":\"zuzhang@qq.com\",\"nickname\":\"\\u7ec4\\u957f1\",\"phone\":\"18812341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557352, 0);
INSERT INTO `yin_admin_log` VALUES (23, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/3?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"c4ed3f4eb4cc2425cd331d3196e45a9f\",\"row\":{\"pid\":\"2\",\"team_id\":\"1\",\"username\":\"zuzhang1\",\"email\":\"zuzhang@qq.com\",\"nickname\":\"\\u7ec4\\u957f1\",\"phone\":\"18812341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557572, 0);
INSERT INTO `yin_admin_log` VALUES (24, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/5?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"cd692c5e815a0fea08c639127eaa064d\",\"row\":{\"pid\":\"0\",\"team_id\":\"1\",\"username\":\"yewuyuan1\",\"email\":\"yuwuyuan1@email.com\",\"nickname\":\"\\u4e1a\\u52a1\\u54581\",\"phone\":\"17742530022\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"5\"],\"ids\":\"5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557621, 0);
INSERT INTO `yin_admin_log` VALUES (25, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/5?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"14e2304b5dda5e9129e82572bfc5f3dd\",\"row\":{\"pid\":\"3\",\"team_id\":\"1\",\"username\":\"yewuyuan1\",\"email\":\"yuwuyuan1@email.com\",\"nickname\":\"\\u4e1a\\u52a1\\u54581\",\"phone\":\"17742530022\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"5\"],\"ids\":\"5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557814, 0);
INSERT INTO `yin_admin_log` VALUES (26, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"3e28e6e92d1e41e0b9989d10a7533914\",\"row\":{\"pid\":\"0\",\"team_id\":\"2\",\"username\":\"boss2\",\"email\":\"boos2@email.com\",\"nickname\":\"\\u8001\\u677f2\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557885, 0);
INSERT INTO `yin_admin_log` VALUES (27, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"19bc29df48e366f766f842bbdcf489de\",\"row\":{\"pid\":\"0\",\"team_id\":\"2\",\"username\":\"bosss2\",\"email\":\"boos2@email.com\",\"nickname\":\"\\u8001\\u677f2\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557894, 0);
INSERT INTO `yin_admin_log` VALUES (28, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"eb507b8618fc6374a45652040d71aed6\",\"row\":{\"pid\":\"0\",\"team_id\":\"1\",\"username\":\"boss1\",\"email\":\"boos2@email.com\",\"nickname\":\"\\u8001\\u677f11\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585557981, 0);
INSERT INTO `yin_admin_log` VALUES (29, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/4?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"ee50c2741727a3bef30680b9a1c0b368\",\"row\":{\"pid\":\"2\",\"team_id\":\"2\",\"username\":\"zuzhang2\",\"email\":\"zz2@email.com\",\"nickname\":\"\\u7ec4\\u957f2\",\"phone\":\"15212341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558014, 0);
INSERT INTO `yin_admin_log` VALUES (30, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/4?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"94c82afba7179875dfef598d3359709a\",\"row\":{\"pid\":\"2\",\"team_id\":\"1\",\"username\":\"zuzhang2\",\"email\":\"zz2@email.com\",\"nickname\":\"\\u7ec4\\u957f2\",\"phone\":\"15212341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558029, 0);
INSERT INTO `yin_admin_log` VALUES (31, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"91e4b10e50911de5e41e3a8af897ef7c\",\"row\":{\"pid\":\"4\",\"team_id\":\"1\",\"username\":\"ywy01\",\"email\":\"ywy@email.com\",\"nickname\":\"\\u4e1a\\u52a1\\u54582\",\"phone\":\"19978787878\",\"password\":\"123456\",\"status\":\"normal\"},\"group\":[\"5\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558083, 0);
INSERT INTO `yin_admin_log` VALUES (32, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558425, 0);
INSERT INTO `yin_admin_log` VALUES (33, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558437, 0);
INSERT INTO `yin_admin_log` VALUES (34, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"e5d978d972e5376478e32c80ca5d0bf9\",\"row\":{\"rules\":\"1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558465, 0);
INSERT INTO `yin_admin_log` VALUES (35, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"3\",\"pid\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558474, 0);
INSERT INTO `yin_admin_log` VALUES (36, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/3?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"c8cb1ed85e368d17103732d8855d27e4\",\"row\":{\"rules\":\"1,8,13,14,15,16,17,29,30,31,32,33,34,120,130,140,2,119,118,129,128,139,138\",\"pid\":\"2\",\"name\":\"\\u7ec4\\u957f\",\"status\":\"normal\"},\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558504, 0);
INSERT INTO `yin_admin_log` VALUES (37, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"5\",\"pid\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558513, 0);
INSERT INTO `yin_admin_log` VALUES (38, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"5\",\"pid\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558518, 0);
INSERT INTO `yin_admin_log` VALUES (39, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/5?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"4a22ae1b36ac4a012f1e6b917b075375\",\"row\":{\"rules\":\"13,14,16,17,29,30,31,32,34,118,119,120,128,129,130,138,139,140,1,8,2\",\"pid\":\"3\",\"name\":\"\\u4e1a\\u52a1\\u5458\",\"status\":\"normal\"},\"ids\":\"5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558545, 0);
INSERT INTO `yin_admin_log` VALUES (40, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/del/ids/6', '权限管理 管理员管理 删除', '{\"action\":\"del\",\"ids\":\"6\",\"params\":\"\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585558573, 0);
INSERT INTO `yin_admin_log` VALUES (41, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/2?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"3d9e1b596abf79d4d645588d39a955f2\",\"row\":{\"pid\":\"0\",\"team_id\":\"1\",\"username\":\"boss1\",\"email\":\"boos2@email.com\",\"nickname\":\"\\u8001\\u677f11\",\"phone\":\"15207331234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"2\"],\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585560893, 0);
INSERT INTO `yin_admin_log` VALUES (42, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/3?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"b16b63f1dc0c9910ca486cf220e4d492\",\"row\":{\"pid\":\"2\",\"team_id\":\"1\",\"username\":\"zuzhang1\",\"email\":\"zuzhang@qq.com\",\"nickname\":\"\\u7ec4\\u957f1\",\"phone\":\"18812341234\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"3\"],\"ids\":\"3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585560925, 0);
INSERT INTO `yin_admin_log` VALUES (43, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/edit/ids/5?dialog=1', '权限管理 管理员管理 编辑', '{\"dialog\":\"1\",\"__token__\":\"4ede65617c8f144543d2a808fbdc42c4\",\"row\":{\"pid\":\"3\",\"team_id\":\"1\",\"username\":\"yewuyuan1\",\"email\":\"yuwuyuan1@email.com\",\"nickname\":\"\\u4e1a\\u52a1\\u54581\",\"phone\":\"17742530022\",\"password\":\"\",\"loginfailure\":\"0\",\"status\":\"normal\"},\"group\":[\"5\"],\"ids\":\"5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585561068, 0);
INSERT INTO `yin_admin_log` VALUES (44, 1, 'admin', '/aLYPMkZvnQ.php/auth/admin/add?dialog=1', '权限管理 管理员管理 添加', '{\"dialog\":\"1\",\"__token__\":\"144397a1acc4af914012fb07ac0725b2\",\"row\":{\"pid\":\"4\",\"team_id\":\"0\",\"username\":\"ywy02\",\"email\":\"ywy02@eamil.com\",\"nickname\":\"\\u4e1a\\u52a1\\u54582\",\"phone\":\"153123456\",\"password\":\"123456\",\"status\":\"normal\"},\"group\":[\"5\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585561117, 0);
INSERT INTO `yin_admin_log` VALUES (45, 1, 'admin', '/aLYPMkZvnQ.php/team/team/add?dialog=1', '团队管理 团队管理 添加', '{\"dialog\":\"1\",\"row\":{\"name\":\"team3\",\"admin_username\":\"\\u8001\\u677f3\",\"phone\":\"187123456\",\"team_productions\":\"1,2\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585561185, 0);
INSERT INTO `yin_admin_log` VALUES (46, 1, 'admin', '/aLYPMkZvnQ.php/team/team/del/ids/4', '团队管理 团队管理 删除', '{\"action\":\"del\",\"ids\":\"4\",\"params\":\"\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585561190, 0);
INSERT INTO `yin_admin_log` VALUES (47, 1, 'admin', '/aLYPMkZvnQ.php/team/team/restore/ids/4', '团队管理 团队管理 还原', '{\"ids\":\"4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585561205, 0);
INSERT INTO `yin_admin_log` VALUES (48, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"name\":\"pro1\",\"sales_price\":\"79\",\"pay_mode\":\"online\",\"title\":\"pro1\",\"is_comment\":\"0\",\"is_complain\":\"0\",\"is_search\":\"0\",\"wx_qr\":\"1\",\"phone1\":\"120\",\"phone2\":\"110\",\"work_time\":\"2020-03-30 19:53:57\",\"online_chat\":\"12\",\"lead_order_word\":\"1212\",\"sub_order_word\":\"12\",\"offline_pay\":\"1212\",\"tips\":\"121221\",\"production_desc\":\"121212\",\"paid_finished\":\"121212\",\"is_sms\":\"1\",\"sms_text\":\"21212121\",\"status\":\"up\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585569331, 0);
INSERT INTO `yin_admin_log` VALUES (49, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Fselect%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/select?ref=addtabs\",\"__token__\":\"e9fa164499a47c50dbb04f311a74d55a\",\"username\":\"admin\",\"captcha\":\"MKUS\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585631859, 0);
INSERT INTO `yin_admin_log` VALUES (50, 1, 'admin', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585635569, 0);
INSERT INTO `yin_admin_log` VALUES (51, 1, 'admin', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"shoes.html\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636052, 0);
INSERT INTO `yin_admin_log` VALUES (52, 1, 'admin', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"shoes.html\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636090, 0);
INSERT INTO `yin_admin_log` VALUES (53, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"production_name\":\"\\u978b\\u5b50\",\"sales_price\":\"799\",\"discount\":\"720\",\"true_price\":\"79\",\"phone1\":\"120\",\"phone2\":\"119\",\"qr_image\":\"\'\'\",\"modulefile\":\"\\/uploads\\/20200331\\/2cb883566a7d43614af3d72835edd42a.html\",\"special_code\":\"sdfsfadsfads\",\"tongji\":\"dafsfdsafd\",\"status\":\"0\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636377, 0);
INSERT INTO `yin_admin_log` VALUES (54, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"production_name\":\"\\u978b\\u5b50\",\"sales_price\":\"799\",\"discount\":\"720\",\"true_price\":\"79\",\"phone1\":\"120\",\"phone2\":\"119\",\"qr_image\":\"\'\'\",\"modulefile\":\"\\/uploads\\/20200331\\/2cb883566a7d43614af3d72835edd42a.html\",\"special_code\":\"sdfsfadsfads\",\"tongji\":\"dafsfdsafd\",\"status\":\"0\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636386, 0);
INSERT INTO `yin_admin_log` VALUES (55, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"production_name\":\"\\u978b\\u5b50\",\"sales_price\":\"799\",\"discount\":\"720\",\"true_price\":\"79\",\"phone1\":\"120\",\"phone2\":\"119\",\"qr_image\":\"\'\'\",\"modulefile\":\"\\/uploads\\/20200331\\/2cb883566a7d43614af3d72835edd42a.html\",\"special_code\":\"sdfsfadsfads\",\"tongji\":\"dafsfdsafd\",\"status\":\"0\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636419, 0);
INSERT INTO `yin_admin_log` VALUES (56, 1, 'admin', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"shoes.html\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636578, 0);
INSERT INTO `yin_admin_log` VALUES (57, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"production_name\":\"\\u978b\\u5b50\",\"sales_price\":\"799.99\",\"discount\":\"720\",\"true_price\":\"79.99\",\"phone1\":\"120\",\"phone2\":\"119\",\"qr_image\":\"\'\'\",\"modulefile\":\"\\/uploads\\/20200331\\/2cb883566a7d43614af3d72835edd42a.html\",\"special_code\":\"dsfsdfdsfdsf\",\"tongji\":\"sfdsfdsfdsfdfd\",\"status\":\"0\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585636586, 0);
INSERT INTO `yin_admin_log` VALUES (58, 1, 'admin', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"shoes.html\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585637829, 0);
INSERT INTO `yin_admin_log` VALUES (59, 1, 'admin', '/aLYPMkZvnQ.php/production/production/add?dialog=1', '产品管理 产品管理 添加', '{\"dialog\":\"1\",\"row\":{\"production_name\":\"\\u978b\\u5b501212\",\"sales_price\":\"888\",\"discount\":\"800\",\"true_price\":\"88\",\"phone1\":\"119\",\"phone2\":\"110\",\"qr_image\":\"\'\'\",\"modulefile\":\"\\/uploads\\/20200331\\/2cb883566a7d43614af3d72835edd42a.html\",\"special_code\":\"drteteyrsfdsf\",\"tongji\":\"qrewewtr\",\"status\":\"0\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585637836, 0);
INSERT INTO `yin_admin_log` VALUES (60, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"26b036776d76f527f6bc7c4d65ed2b21\",\"username\":\"boss1\",\"captcha\":\"zn4g\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585638427, 0);
INSERT INTO `yin_admin_log` VALUES (61, 2, 'boss1', '/aLYPMkZvnQ.php/index/login', '登录', '{\"__token__\":\"8518aaee36cdcd27b641be548a06cc11\",\"username\":\"boss1\",\"captcha\":\"7hnb\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585638434, 0);
INSERT INTO `yin_admin_log` VALUES (62, 1, 'admin', '/aLYPMkZvnQ.php/index/login', '登录', '{\"__token__\":\"1df6ae8658231685676426b97403f839\",\"username\":\"admin\",\"captcha\":\"AKFD\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585638563, 0);
INSERT INTO `yin_admin_log` VALUES (63, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585638602, 0);
INSERT INTO `yin_admin_log` VALUES (64, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"ae58ba48173398074ef68dcf48a28b7a\",\"row\":{\"rules\":\"1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585638619, 0);
INSERT INTO `yin_admin_log` VALUES (65, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"a66eefb2d481fda9d68b0108a977f291\",\"username\":\"boss1\",\"captcha\":\"KQPX\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585638655, 0);
INSERT INTO `yin_admin_log` VALUES (66, 2, 'boss1', '/aLYPMkZvnQ.php/production/select/add?dialog=1', '产品管理 产品文案选择 添加', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"789\",\"discount\":\"700\",\"true_price\":\"89\",\"phone1\":\"123\",\"phone2\":\"111\",\"special_code\":\"1231223312\",\"tongji\":\"21232312323231\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585640838, 0);
INSERT INTO `yin_admin_log` VALUES (67, 2, 'boss1', '/aLYPMkZvnQ.php/production/select/add?dialog=1', '产品管理 产品文案选择 添加', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"789\",\"discount\":\"700\",\"true_price\":\"89\",\"phone1\":\"123\",\"phone2\":\"111\",\"special_code\":\"1231223312\",\"tongji\":\"21232312323231\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585640856, 0);
INSERT INTO `yin_admin_log` VALUES (68, 2, 'boss1', '/aLYPMkZvnQ.php/production/select/edit/ids/1?dialog=1', '产品管理 产品文案选择 编辑', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"789.00\",\"discount\":\"700.00\",\"true_price\":\"89.00\",\"phone1\":\"123\",\"phone2\":\"111\",\"special_code\":\"1231223312\",\"tongji\":\"21232312323231\"},\"ids\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585641784, 0);
INSERT INTO `yin_admin_log` VALUES (69, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fgroup%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/group?ref=addtabs\",\"__token__\":\"db9b97e02681fa0dfc56a60811e3858f\",\"username\":\"admin\",\"captcha\":\"gnz2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585712555, 0);
INSERT INTO `yin_admin_log` VALUES (70, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Fselect%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/select?ref=addtabs\",\"__token__\":\"c2f1592951881265f346b112eb680893\",\"username\":\"boss1\",\"captcha\":\"JJQU\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585712577, 0);
INSERT INTO `yin_admin_log` VALUES (71, 1, 'admin', '/aLYPMkZvnQ.php/sysconfig/pay/add?dialog=1', '系统设置 支付设置 添加', '{\"dialog\":\"1\",\"row\":{\"pay_name\":\"\\u5e73\\u53f0\\u7ea7\\u652f\\u4ed8\\u8d26\\u53f7\",\"app_id\":\"qwetyuiopasdfgjkl\",\"app_secret\":\"etuiodfjklcvbnmjkefshkdsa\",\"business_code\":\"3456789345678\",\"pay_secret\":\"dsafiwiqpoeurjfewlkuewqhfkshfwekf\",\"secure_secret\":\"woiqroiwqrqjpowrjqwpfewpojqpofjqwpojfqopfjqpofjqpo\",\"pay_domain1\":\"fdaqfwefqwef.com\",\"pay_domain2\":\"qtewiuyroiqw.com\",\"pay_domain3\":\"iquyewoiqouewqoi.com\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585715679, 0);
INSERT INTO `yin_admin_log` VALUES (72, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Fselect%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/select?ref=addtabs\",\"__token__\":\"f33db4c4b1a662d548e988259ab99510\",\"username\":\"boss1\",\"captcha\":\"AIYS\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585720230, 0);
INSERT INTO `yin_admin_log` VALUES (73, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fsysconfig%2Fpay%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/sysconfig\\/pay?ref=addtabs\",\"__token__\":\"a8f6629fb9b6a00876b6024c7d143456\",\"username\":\"admin\",\"captcha\":\"tvun\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585720252, 0);
INSERT INTO `yin_admin_log` VALUES (74, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585720499, 0);
INSERT INTO `yin_admin_log` VALUES (75, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"8159d2b26b7db13998631c83da9b2fcb\",\"row\":{\"rules\":\"1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585720508, 0);
INSERT INTO `yin_admin_log` VALUES (76, 2, 'boss1', '/aLYPMkZvnQ.php/sysconfig/pay/add?dialog=1', '系统设置 支付设置 添加', '{\"dialog\":\"1\",\"row\":{\"pay_name\":\"\\u56e2\\u961f1-\\u8001\\u677f1\\u7684\\u5fae\\u4fe1\\u652f\\u4ed8\",\"app_id\":\"fghsdwqytiurhwj\",\"app_secret\":\"345ftewuj7tfguytrfgytrdcvyredcvbnjuytfvb\",\"business_code\":\"234567865434567\",\"pay_secret\":\"rtyuiofdsdfhjknbvcxsertyujvcdrtyu\",\"secure_secret\":\"rtyuikmnbvcdrtyuikjhgfrtyujnbvdtyhvcftyhbvfhb\",\"pay_domain1\":\"twieqhrje.com\",\"pay_domain2\":\"woiqrew.com\",\"pay_domain3\":\"qweorusdf.com\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1585720579, 0);
INSERT INTO `yin_admin_log` VALUES (77, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Findex%2Findex', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/index\\/index\",\"__token__\":\"634cc0481ebde078bfcc9175b5681407\",\"username\":\"admin\",\"captcha\":\"kpuj\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585743352, 0);
INSERT INTO `yin_admin_log` VALUES (78, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Forder%2Forder%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/order\\/order?ref=addtabs\",\"__token__\":\"d3dbbd59fa8cf960bbec6eae4d470a08\",\"username\":\"admin\",\"captcha\":\"dmgy\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585760409, 0);
INSERT INTO `yin_admin_log` VALUES (79, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"7031ce0cf7e42a811167c7de72ece330\",\"username\":\"boss1\",\"captcha\":\"62ps\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585896993, 0);
INSERT INTO `yin_admin_log` VALUES (80, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"fb3bd9ce786e5872752d26df8a87b091\",\"username\":\"boss1\",\"captcha\":\"usz8\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1585897000, 0);
INSERT INTO `yin_admin_log` VALUES (81, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"6ae04bfdd6255547c01d4285009b1784\",\"username\":\"admin\",\"captcha\":\"CEQX\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586079626, 0);
INSERT INTO `yin_admin_log` VALUES (82, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"96e4e37e83385a6b0a6028fb8ca8cdfc\",\"username\":\"admin\",\"captcha\":\"HAWR\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586164412, 0);
INSERT INTO `yin_admin_log` VALUES (83, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fadmin%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/admin?ref=addtabs\",\"__token__\":\"d760b6e9486d47d8362cba4070c04ffb\",\"username\":\"admin\",\"captcha\":\"k7le\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586186641, 0);
INSERT INTO `yin_admin_log` VALUES (84, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\",\"__token__\":\"c8c89b6ecca0416bccd3b7124c4bb1ae\",\"username\":\"boss1\",\"captcha\":\"vhp6\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586187481, 0);
INSERT INTO `yin_admin_log` VALUES (85, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586187890, 0);
INSERT INTO `yin_admin_log` VALUES (86, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"2dd9dc8b9f31e795c3b6710fb961558f\",\"row\":{\"rules\":\"1,8,9,13,14,15,16,17,29,30,31,32,33,34,40,41,42,43,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,158,159,160,161,162,163,164,165,166,2,5,118,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586187924, 0);
INSERT INTO `yin_admin_log` VALUES (87, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fgroup%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/group?ref=addtabs\",\"__token__\":\"941646a3089f21be69584dd60b1ad2f1\",\"username\":\"admin\",\"captcha\":\"IKZS\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586238614, 0);
INSERT INTO `yin_admin_log` VALUES (88, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fgroup%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/group?ref=addtabs\",\"__token__\":\"1a6e193967b3e48b59013b38da9892c3\",\"username\":\"admin\",\"captcha\":\"CLJV\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586238620, 0);
INSERT INTO `yin_admin_log` VALUES (89, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Findex%2Findex', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/index\\/index\",\"__token__\":\"d8ebe4f13de80d5b3e6c840410dad00d\",\"username\":\"admin\",\"captcha\":\"IFT7\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586242719, 0);
INSERT INTO `yin_admin_log` VALUES (90, 1, 'admin', '/aLYPMkZvnQ.php/production/select/add?dialog=1', '产品管理 产品文案选择 添加', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"123\",\"discount\":\"100\",\"true_price\":\"23\",\"phone1\":\"120\",\"phone2\":\"119\",\"special_code\":\"123123123\",\"tongji\":\"45456456456\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586244678, 0);
INSERT INTO `yin_admin_log` VALUES (91, 1, 'admin', '/aLYPMkZvnQ.php/production/select/add?dialog=1', '产品管理 产品文案选择 添加', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"123\",\"discount\":\"100\",\"true_price\":\"23\",\"phone1\":\"120\",\"phone2\":\"119\",\"special_code\":\"123123123\",\"tongji\":\"45456456456\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586244927, 0);
INSERT INTO `yin_admin_log` VALUES (92, 1, 'admin', '/aLYPMkZvnQ.php/production/select/edit/ids/2?dialog=1', '产品管理 产品文案选择 编辑', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"123.00\",\"discount\":\"100.00\",\"true_price\":\"23.00\",\"phone1\":\"120\",\"phone2\":\"119\",\"special_code\":\"123123123\",\"tongji\":\"45456456456\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586245224, 0);
INSERT INTO `yin_admin_log` VALUES (93, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fadmin%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/admin?ref=addtabs\",\"__token__\":\"a298b448f7970a6cdb0abc59e4729f23\",\"username\":\"boss1\",\"captcha\":\"yp3c\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586245823, 0);
INSERT INTO `yin_admin_log` VALUES (94, 2, 'boss1', '/aLYPMkZvnQ.php/production/select/edit/ids/1?dialog=1', '产品管理 产品文案选择 编辑', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"789.00\",\"discount\":\"700.00\",\"true_price\":\"89.00\",\"phone1\":\"123\",\"phone2\":\"111\",\"special_code\":\"1231223312\",\"tongji\":\"21232312323231\"},\"ids\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586245910, 0);
INSERT INTO `yin_admin_log` VALUES (95, 2, 'boss1', '/aLYPMkZvnQ.php/production/select/edit/ids/1?dialog=1', '产品管理 产品文案选择 编辑', '{\"dialog\":\"1\",\"row\":{\"production_id\":\"1\",\"sales_price\":\"789.00\",\"discount\":\"700.00\",\"true_price\":\"89.00\",\"phone1\":\"123\",\"phone2\":\"111\",\"special_code\":\"1231223312\",\"tongji\":\"21232312323231\"},\"ids\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586245976, 0);
INSERT INTO `yin_admin_log` VALUES (96, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fteam%2Fteam%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/team\\/team?ref=addtabs\",\"__token__\":\"2fe58657830239f869e0199f7229c5b8\",\"username\":\"admin\",\"captcha\":\"eetd\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586247331, 0);
INSERT INTO `yin_admin_log` VALUES (97, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fteam%2Fteam%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/team\\/team?ref=addtabs\",\"__token__\":\"3cd81eae954aa9810a87049d38451ad6\",\"username\":\"admin\",\"captcha\":\"NPWJ\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586247336, 0);
INSERT INTO `yin_admin_log` VALUES (98, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fadmin%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/admin?ref=addtabs\",\"__token__\":\"c57685789f7bc3248adffa1e2a88fdee\",\"username\":\"admin\",\"captcha\":\"txff\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586257773, 0);
INSERT INTO `yin_admin_log` VALUES (99, 1, 'admin', '/aLYPMkZvnQ.php/general/config/check', '常规管理 系统配置', '{\"row\":{\"name\":\"file\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586258955, 0);
INSERT INTO `yin_admin_log` VALUES (100, 1, 'admin', '/aLYPMkZvnQ.php/general.config/add', '常规管理 系统配置 添加', '{\"__token__\":\"c3135896006e6ddd878d22a9289a1048\",\"row\":{\"type\":\"files\",\"group\":\"example\",\"name\":\"file\",\"title\":\"aa\",\"value\":\"\",\"content\":\"value1|title1\\r\\nvalue2|title2\",\"tip\":\"\",\"rule\":\"\",\"extend\":\"\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586258958, 0);
INSERT INTO `yin_admin_log` VALUES (101, 1, 'admin', '/aLYPMkZvnQ.php/general/config/del', '常规管理 系统配置 删除', '{\"name\":\"file\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586258967, 0);
INSERT INTO `yin_admin_log` VALUES (102, 1, 'admin', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"821cb24c6119b1a40f83446e4ffbaa0d\",\"username\":\"admin\",\"captcha\":\"QRHD\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586260983, 0);
INSERT INTO `yin_admin_log` VALUES (103, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"e2abc481143f2749fc1d66d5a2449b74\",\"username\":\"admin\",\"captcha\":\"x525\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261115, 0);
INSERT INTO `yin_admin_log` VALUES (104, 1, 'admin', '/aLYPMkZvnQ.php/index/login', '登录', '{\"__token__\":\"fce9e1754fd97c7f1269fa50fa86a4c5\",\"username\":\"admin\",\"captcha\":\"7ttn\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261121, 0);
INSERT INTO `yin_admin_log` VALUES (105, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"cfa696cb6fb9c0fd099c1fb45743618b\",\"username\":\"ywy02\",\"captcha\":\"Y4EF\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261143, 0);
INSERT INTO `yin_admin_log` VALUES (106, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"bd7c7cf2e7d32d6ba924b1a2fc26cf3f\",\"username\":\"ywy02\",\"captcha\":\"Y4EF\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261185, 0);
INSERT INTO `yin_admin_log` VALUES (107, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"770223f1f8c27468883b5c3201acaa57\",\"username\":\"boss1\",\"captcha\":\"kuwn\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261198, 0);
INSERT INTO `yin_admin_log` VALUES (108, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"1d36c7d8ca132613624a22997cc5e345\",\"username\":\"boss1\",\"captcha\":\"kuwn\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261210, 0);
INSERT INTO `yin_admin_log` VALUES (109, 1, 'admin', '/aLYPMkZvnQ.php/index/login', '登录', '{\"__token__\":\"605182efd42751d2aa1c8e6a27bf5de1\",\"username\":\"admin\",\"captcha\":\"BRKQ\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261317, 0);
INSERT INTO `yin_admin_log` VALUES (110, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?sn=eXd5MDI%3D', '', '{\"sn\":\"eXd5MDI=\",\"__token__\":\"f16d4ec890f23919d13dd268015105d1\",\"username\":\"ADMIN\",\"captcha\":\"CIJW\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586261339, 0);
INSERT INTO `yin_admin_log` VALUES (111, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?sn=eXd5MDI%3D', '', '{\"sn\":\"eXd5MDI=\",\"__token__\":\"d33a39f306555ea36bcd8b2abce23fef\",\"username\":\"admin\",\"captcha\":\"CIJW\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586261348, 0);
INSERT INTO `yin_admin_log` VALUES (112, 1, 'admin', '/aLYPMkZvnQ.php/index/login?sn=eXd5MDI%3D', '登录', '{\"sn\":\"eXd5MDI=\",\"__token__\":\"fecede577d6cad798d35b6cf9a37274d\",\"username\":\"admin\",\"captcha\":\"AFUG\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586261357, 0);
INSERT INTO `yin_admin_log` VALUES (113, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login', '', '{\"__token__\":\"5181248a5d9268a915dd0310eecb4e77\",\"username\":\"boss1\",\"captcha\":\"DJD5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586261494, 0);
INSERT INTO `yin_admin_log` VALUES (114, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fadmin%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/admin?ref=addtabs\",\"__token__\":\"4f0d0a3d69d0d0237f74b9da8f5c33d6\",\"username\":\"ADMIN\",\"captcha\":\"ZSKT\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261519, 0);
INSERT INTO `yin_admin_log` VALUES (115, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fadmin%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/admin?ref=addtabs\",\"__token__\":\"e50cc85788ff1f4b7c464cc48caad0c7\",\"username\":\"ADMIN\",\"captcha\":\"yhsp\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261524, 0);
INSERT INTO `yin_admin_log` VALUES (116, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"8cdbba01acddc6f0152700cf73682139\",\"username\":\"boss1\",\"captcha\":\"umva\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586261575, 0);
INSERT INTO `yin_admin_log` VALUES (117, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261624, 0);
INSERT INTO `yin_admin_log` VALUES (118, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"df1f5c5c614abe732c340dafe489bcea\",\"row\":{\"rules\":\"1,8,13,14,15,16,17,29,30,31,32,33,34,40,41,42,43,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,158,159,160,161,162,163,164,165,166,195,196,2,9,5,118,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586261634, 0);
INSERT INTO `yin_admin_log` VALUES (119, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/108?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"86cae62040c4b57647b8875b81d09707\",\"row\":{\"ismenu\":\"1\",\"pid\":\"0\",\"name\":\"team\",\"title\":\"\\u56e2\\u961f\\u7ba1\\u7406\",\"icon\":\"fa fa-sitemap\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"108\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262603, 0);
INSERT INTO `yin_admin_log` VALUES (120, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262604, 0);
INSERT INTO `yin_admin_log` VALUES (121, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/118?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"b51ce58d4d56b02768a4b1253c21d78b\",\"row\":{\"ismenu\":\"1\",\"pid\":\"0\",\"name\":\"production\",\"title\":\"\\u4ea7\\u54c1\\u7ba1\\u7406\",\"icon\":\"fa fa-cubes\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"118\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262661, 0);
INSERT INTO `yin_admin_log` VALUES (122, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262662, 0);
INSERT INTO `yin_admin_log` VALUES (123, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/128?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"0d80110590a2da98cbe26978ac6d86f4\",\"row\":{\"ismenu\":\"1\",\"pid\":\"0\",\"name\":\"order\",\"title\":\"\\u8ba2\\u5355\\u7ba1\\u7406\",\"icon\":\"fa fa-first-order\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"128\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262688, 0);
INSERT INTO `yin_admin_log` VALUES (124, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262689, 0);
INSERT INTO `yin_admin_log` VALUES (125, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/138?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"aca7056ba533862fd3b61f63b33b9d9b\",\"row\":{\"ismenu\":\"1\",\"pid\":\"0\",\"name\":\"express\",\"title\":\"\\u5feb\\u9012\\u7ba1\\u7406\",\"icon\":\"fa fa-space-shuttle\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"138\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262778, 0);
INSERT INTO `yin_admin_log` VALUES (126, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262778, 0);
INSERT INTO `yin_admin_log` VALUES (127, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/157?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"1c48cc3b7314d4fbb1375552f9d023c2\",\"row\":{\"ismenu\":\"1\",\"pid\":\"0\",\"name\":\"sysconfig\",\"title\":\"\\u7cfb\\u7edf\\u8bbe\\u7f6e\",\"icon\":\"fa fa-cog\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"157\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262809, 0);
INSERT INTO `yin_admin_log` VALUES (128, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262809, 0);
INSERT INTO `yin_admin_log` VALUES (129, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/109?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"b8558905ea115d127306aee9117da8ae\",\"row\":{\"ismenu\":\"1\",\"pid\":\"108\",\"name\":\"team\\/team\",\"title\":\"\\u56e2\\u961f\\u7ba1\\u7406\",\"icon\":\"fa fa-address-book-o\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"109\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262832, 0);
INSERT INTO `yin_admin_log` VALUES (130, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262833, 0);
INSERT INTO `yin_admin_log` VALUES (131, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/119?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"6ae7355d74f4dc4cdb0eb87c3c66c540\",\"row\":{\"ismenu\":\"1\",\"pid\":\"118\",\"name\":\"production\\/production\",\"title\":\"\\u4ea7\\u54c1\\u7ba1\\u7406\",\"icon\":\"fa fa-cube\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"119\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262863, 0);
INSERT INTO `yin_admin_log` VALUES (132, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262863, 0);
INSERT INTO `yin_admin_log` VALUES (133, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/148?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"3564c8c8ee839f0c0709deb782c32abe\",\"row\":{\"ismenu\":\"1\",\"pid\":\"118\",\"name\":\"production\\/select\",\"title\":\"\\u4ea7\\u54c1\\u6587\\u6848\\u9009\\u62e9\",\"icon\":\"fa fa-file-word-o\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"148\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262880, 0);
INSERT INTO `yin_admin_log` VALUES (134, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262881, 0);
INSERT INTO `yin_admin_log` VALUES (135, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/167?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"d7c964aae043e9b3968e1e7da6fba808\",\"row\":{\"ismenu\":\"1\",\"pid\":\"118\",\"name\":\"production\\/url\",\"title\":\"\\u5546\\u54c1\\u94fe\\u63a5\",\"icon\":\"fa fa-link\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"167\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262907, 0);
INSERT INTO `yin_admin_log` VALUES (136, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262907, 0);
INSERT INTO `yin_admin_log` VALUES (137, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/129?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"e782adcf4c558f1c9c31eafce3b6132b\",\"row\":{\"ismenu\":\"1\",\"pid\":\"128\",\"name\":\"order\\/order\",\"title\":\"\\u8ba2\\u5355\\u7ba1\\u7406\",\"icon\":\"fa fa-reorder\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"129\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262927, 0);
INSERT INTO `yin_admin_log` VALUES (138, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262928, 0);
INSERT INTO `yin_admin_log` VALUES (139, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/139?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"991c7fd5b2dbd4f8517cbe52345e05a2\",\"row\":{\"ismenu\":\"1\",\"pid\":\"138\",\"name\":\"express\\/express\",\"title\":\"\\u5feb\\u9012\\u4fe1\\u606f\\u7ba1\\u7406\",\"icon\":\"fa fa-fast-forward\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"139\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262957, 0);
INSERT INTO `yin_admin_log` VALUES (140, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262957, 0);
INSERT INTO `yin_admin_log` VALUES (141, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/158?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"b0e330da9202de20b88bf8cb7f3ced76\",\"row\":{\"ismenu\":\"1\",\"pid\":\"157\",\"name\":\"sysconfig\\/pay\",\"title\":\"\\u652f\\u4ed8\\u8bbe\\u7f6e\",\"icon\":\"fa fa-weixin\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"158\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262988, 0);
INSERT INTO `yin_admin_log` VALUES (142, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586262989, 0);
INSERT INTO `yin_admin_log` VALUES (143, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/176?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"8a1456fd27f929e98e2b1138bc8982d0\",\"row\":{\"ismenu\":\"1\",\"pid\":\"157\",\"name\":\"sysconfig\\/consumables\",\"title\":\"\\u70ae\\u7070\\u57df\\u540d\",\"icon\":\"fa fa-bomb\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"176\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586263015, 0);
INSERT INTO `yin_admin_log` VALUES (144, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586263015, 0);
INSERT INTO `yin_admin_log` VALUES (145, 1, 'admin', '/aLYPMkZvnQ.php/auth/rule/edit/ids/185?dialog=1', '权限管理 菜单规则 编辑', '{\"dialog\":\"1\",\"__token__\":\"de4ea3f7cce4947b0244387a34678d40\",\"row\":{\"ismenu\":\"1\",\"pid\":\"157\",\"name\":\"sysconfig\\/ground\",\"title\":\"\\u843d\\u5730\\u57df\\u540d\",\"icon\":\"fa fa-diamond\",\"weigh\":\"0\",\"condition\":\"\",\"remark\":\"\",\"status\":\"normal\"},\"ids\":\"185\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586263062, 0);
INSERT INTO `yin_admin_log` VALUES (146, 1, 'admin', '/aLYPMkZvnQ.php/index/index', '', '{\"action\":\"refreshmenu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586263062, 0);
INSERT INTO `yin_admin_log` VALUES (147, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Furl%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/url?ref=addtabs\",\"__token__\":\"b7007346b8d827c770a17e1a7e3382e9\",\"username\":\"admin\",\"captcha\":\"GVQH\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586265126, 0);
INSERT INTO `yin_admin_log` VALUES (148, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Fselect%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/select?ref=addtabs\",\"__token__\":\"b1f3fa9e8a2102bc846af09bc18538c3\",\"username\":\"boss1\",\"captcha\":\"QQUT\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586265768, 0);
INSERT INTO `yin_admin_log` VALUES (149, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"e0b3b05833bfed985814c29004935331\",\"username\":\"boss1\",\"captcha\":\"WBOP\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586265778, 0);
INSERT INTO `yin_admin_log` VALUES (150, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"d45ac76e3e85638c3f2d1b84a3fbf5e5\",\"username\":\"boss1\",\"captcha\":\"h4cx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586265783, 0);
INSERT INTO `yin_admin_log` VALUES (151, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586265804, 0);
INSERT INTO `yin_admin_log` VALUES (152, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"ceb46d038200e225348cddb1d5e964db\",\"row\":{\"rules\":\"1,8,13,14,15,16,17,29,30,31,32,33,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,195,196,2,9,5,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586265811, 0);
INSERT INTO `yin_admin_log` VALUES (153, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586265827, 0);
INSERT INTO `yin_admin_log` VALUES (154, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"c7a68549d9fab341baf398e27885cbfc\",\"row\":{\"rules\":\"1,8,13,14,15,16,17,29,30,31,32,33,34,40,41,42,43,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,158,159,160,161,162,163,164,165,166,168,195,196,199,200,2,9,5,118,157,167\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586265849, 0);
INSERT INTO `yin_admin_log` VALUES (155, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586267225, 0);
INSERT INTO `yin_admin_log` VALUES (156, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"88ff0a531ecf34c11d3e8304cf8c3a70\",\"row\":{\"rules\":\"1,8,13,14,15,16,17,29,30,31,32,33,34,40,41,42,43,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,168,195,196,199,200,2,9,5,118,158,157,167\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586267255, 0);
INSERT INTO `yin_admin_log` VALUES (157, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586267258, 0);
INSERT INTO `yin_admin_log` VALUES (158, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"1d98f45eeca861a014d93a5277bea4a7\",\"row\":{\"rules\":\"1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,199,200,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586267317, 0);
INSERT INTO `yin_admin_log` VALUES (159, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Furl%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/url?ref=addtabs\",\"__token__\":\"c1021dfb8a0943f3b88831dc94ed3950\",\"username\":\"admin\",\"captcha\":\"NFRU\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586268960, 0);
INSERT INTO `yin_admin_log` VALUES (160, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fproduction%2Furl%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/production\\/url?ref=addtabs\",\"__token__\":\"18fc1e25335a14d3c4c6f8c9016bbca9\",\"username\":\"admin\",\"captcha\":\"ae6r\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586268966, 0);
INSERT INTO `yin_admin_log` VALUES (161, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"8fdc90ab17bd31df96399e2cae4ccd6a\",\"username\":\"boss1\",\"captcha\":\"pttj\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586270318, 0);
INSERT INTO `yin_admin_log` VALUES (162, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586270354, 0);
INSERT INTO `yin_admin_log` VALUES (163, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"c3c2f0a3ad3a8001cde6cd0ddfc5e9e5\",\"row\":{\"rules\":\"1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,195,196,199,200,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586270390, 0);
INSERT INTO `yin_admin_log` VALUES (164, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586270479, 0);
INSERT INTO `yin_admin_log` VALUES (165, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"d03e1153d269394cada1d3fecd3a9423\",\"row\":{\"rules\":\"13,14,16,15,17,29,30,31,32,34,40,41,42,43,195,196,120,121,122,123,124,125,126,127,149,150,151,152,153,154,155,156,168,169,170,171,172,173,174,175,199,200,130,131,132,133,134,135,136,137,140,141,142,143,144,145,146,147,159,160,161,162,165,166,1,119,148,167,118,129,128,139,138,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586270490, 0);
INSERT INTO `yin_admin_log` VALUES (166, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Fgroup%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/group?ref=addtabs\",\"__token__\":\"644b49d938849e9c0a571b3d2cdfbffa\",\"username\":\"admin\",\"captcha\":\"nuf4\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586273436, 0);
INSERT INTO `yin_admin_log` VALUES (167, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586273442, 0);
INSERT INTO `yin_admin_log` VALUES (168, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"19ead8c4b3521d46dbfe34bfddbc3a83\",\"row\":{\"rules\":\"1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,195,196,199,200,201,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586273449, 0);
INSERT INTO `yin_admin_log` VALUES (169, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"0d5f5c4e6d09f8e63fc93c6a4648a200\",\"username\":\"boss1\",\"captcha\":\"st7k\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586273982, 0);
INSERT INTO `yin_admin_log` VALUES (170, 0, 'Unknown', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fexpress%2Fexpress%3Fref%3Daddtabs', '', '{\"url\":\"\\/aLYPMkZvnQ.php\\/express\\/express?ref=addtabs\",\"__token__\":\"b281d542096f8bf64a77ca13aa00bd8e\",\"username\":\"admin\",\"captcha\":\"2h68\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586277249, 0);
INSERT INTO `yin_admin_log` VALUES (171, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fexpress%2Fexpress%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/express\\/express?ref=addtabs\",\"__token__\":\"62c3018239078aec45de5f2b5e09b1e3\",\"username\":\"admin\",\"captcha\":\"jaaq\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586277254, 0);
INSERT INTO `yin_admin_log` VALUES (172, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"cb524ace5211dc33d5a63be35445bf5f\",\"username\":\"boss1\",\"captcha\":\"8qj3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586277968, 0);
INSERT INTO `yin_admin_log` VALUES (173, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278000, 0);
INSERT INTO `yin_admin_log` VALUES (174, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278024, 0);
INSERT INTO `yin_admin_log` VALUES (175, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278033, 0);
INSERT INTO `yin_admin_log` VALUES (176, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"3a31bea3876d93f252d00fb2594af24e\",\"row\":{\"rules\":\"1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,195,196,199,200,201,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278079, 0);
INSERT INTO `yin_admin_log` VALUES (177, 1, 'admin', '/aLYPMkZvnQ.php/index/login?url=%2FaLYPMkZvnQ.php%2Fauth%2Frule%3Fref%3Daddtabs', '登录', '{\"url\":\"\\/aLYPMkZvnQ.php\\/auth\\/rule?ref=addtabs\",\"__token__\":\"3aae6e242f543e499a625c08812974cb\",\"username\":\"admin\",\"captcha\":\"mmjy\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278227, 0);
INSERT INTO `yin_admin_log` VALUES (178, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/roletree', '权限管理 角色组', '{\"id\":\"2\",\"pid\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278354, 0);
INSERT INTO `yin_admin_log` VALUES (179, 1, 'admin', '/aLYPMkZvnQ.php/auth/group/edit/ids/2?dialog=1', '权限管理 角色组 编辑', '{\"dialog\":\"1\",\"__token__\":\"46488edc09d13a638ec19bfa77e911e2\",\"row\":{\"rules\":\"1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,195,196,199,200,201,202,8,2,9,5,158,157\",\"pid\":\"1\",\"name\":\"\\u8001\\u677f\\u7ec4\",\"status\":\"normal\"},\"ids\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36', 1586278359, 0);
INSERT INTO `yin_admin_log` VALUES (180, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"628eda078fa377aa806de418eecfd339\",\"username\":\"boss1\",\"captcha\":\"4pgm\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278371, 0);
INSERT INTO `yin_admin_log` VALUES (181, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278567, 0);
INSERT INTO `yin_admin_log` VALUES (182, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/21a46a89ef6f59d999f1db3a3a81eb7b.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278568, 0);
INSERT INTO `yin_admin_log` VALUES (183, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278578, 0);
INSERT INTO `yin_admin_log` VALUES (184, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/21a46a89ef6f59d999f1db3a3a81eb7b.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278579, 0);
INSERT INTO `yin_admin_log` VALUES (185, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278632, 0);
INSERT INTO `yin_admin_log` VALUES (186, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/656f4496c6003b9c78a838f4317ea3d0.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278632, 0);
INSERT INTO `yin_admin_log` VALUES (187, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278963, 0);
INSERT INTO `yin_admin_log` VALUES (188, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/3680ab78b97ec912f4d89ca3049ffd29.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586278963, 0);
INSERT INTO `yin_admin_log` VALUES (189, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586279057, 0);
INSERT INTO `yin_admin_log` VALUES (190, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/0e466e8fb457b1f42f928fc2af61116a.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586279058, 0);
INSERT INTO `yin_admin_log` VALUES (191, 2, 'boss1', '/aLYPMkZvnQ.php/index/login?sn=Ym9zczE%3D', '登录', '{\"sn\":\"Ym9zczE=\",\"__token__\":\"c6ea03abb761a9349cd82330be84d331\",\"username\":\"boss1\",\"captcha\":\"wmmc\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586318032, 0);
INSERT INTO `yin_admin_log` VALUES (192, 2, 'boss1', '/aLYPMkZvnQ.php/ajax/upload', '', '{\"name\":\"example.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586318349, 0);
INSERT INTO `yin_admin_log` VALUES (193, 2, 'boss1', '/aLYPMkZvnQ.php/express/express?addtabs=1', '快递管理 快递信息管理 查看', '{\"addtabs\":\"1\",\"file\":\"\\/uploads\\/20200408\\/0e466e8fb457b1f42f928fc2af61116a.xlsx\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0', 1586318350, 0);

-- ----------------------------
-- Table structure for yin_attachment
-- ----------------------------
DROP TABLE IF EXISTS `yin_attachment`;
CREATE TABLE `yin_attachment`  (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员ID',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片帧数',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件大小',
  `mimetype` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建日期',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `uploadtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传时间',
  `storage` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_attachment
-- ----------------------------
INSERT INTO `yin_attachment` VALUES (1, 1, 0, '/assets/img/qrcode.png', '150', '150', 'png', 0, 21859, 'image/png', '', 1499681848, 1499681848, 1499681848, 'local', '17163603d0263e4838b9387ff2cd4877e8b018f6');
INSERT INTO `yin_attachment` VALUES (2, 1, 0, '/uploads/20200331/95a595646334deb565ca18d00672cfb4.xlsx', '', '', 'xlsx', 0, 9430, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '{\"name\":\"example.xlsx\"}', 1585635569, 1585635569, 1585635569, 'local', '856300f7be0aea92fd629d7a6163b7072a2768f2');
INSERT INTO `yin_attachment` VALUES (3, 1, 0, '/uploads/20200331/2cb883566a7d43614af3d72835edd42a.html', '', '', 'html', 0, 54401, 'text/html', '{\"name\":\"shoes.html\"}', 1585636090, 1585636090, 1585636090, 'local', '6d7900ed07b4bb7e19ab6216d40398316d1d8bbc');
INSERT INTO `yin_attachment` VALUES (4, 2, 0, '/uploads/20200408/21a46a89ef6f59d999f1db3a3a81eb7b.xlsx', '', '', 'xlsx', 0, 9451, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '{\"name\":\"example.xlsx\"}', 1586278567, 1586278567, 1586278567, 'local', '4ae51e88bd19136150600c08b9846ca3f7c3887f');
INSERT INTO `yin_attachment` VALUES (5, 2, 0, '/uploads/20200408/656f4496c6003b9c78a838f4317ea3d0.xlsx', '', '', 'xlsx', 0, 9516, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '{\"name\":\"example.xlsx\"}', 1586278631, 1586278631, 1586278631, 'local', 'b0b1a8227f58c4f66c7d58f8449e90c2f0ed8df2');
INSERT INTO `yin_attachment` VALUES (6, 2, 0, '/uploads/20200408/3680ab78b97ec912f4d89ca3049ffd29.xlsx', '', '', 'xlsx', 0, 9539, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '{\"name\":\"example.xlsx\"}', 1586278963, 1586278963, 1586278963, 'local', '4ba3398959ce30db3af3886c89334d810d253010');
INSERT INTO `yin_attachment` VALUES (7, 2, 0, '/uploads/20200408/0e466e8fb457b1f42f928fc2af61116a.xlsx', '', '', 'xlsx', 0, 9576, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '{\"name\":\"example.xlsx\"}', 1586279057, 1586279057, 1586279057, 'local', '6d6f6acb2c690b585656c88a5af2337b0c3c3a08');

-- ----------------------------
-- Table structure for yin_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `yin_auth_group`;
CREATE TABLE `yin_auth_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父组别',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则ID',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_auth_group
-- ----------------------------
INSERT INTO `yin_auth_group` VALUES (1, 0, 'Admin group', '*', 'normal', 1490883540, 149088354, 0);
INSERT INTO `yin_auth_group` VALUES (2, 1, '老板组', '1,13,14,15,16,17,29,30,31,32,34,40,41,42,43,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,159,160,161,162,165,166,167,168,169,170,171,172,173,174,175,195,196,199,200,201,202,8,2,9,5,158,157', 'normal', 1490883540, 1586278359, 0);
INSERT INTO `yin_auth_group` VALUES (3, 2, '组长', '1,2,8,13,14,15,16,17,29,30,31,32,34,118,119,120,128,129,130,138,139,140', 'normal', 1490883540, 1586278359, 0);
INSERT INTO `yin_auth_group` VALUES (5, 3, '业务员', '1,2,8,13,14,16,17,29,30,31,32,34,118,119,120,128,129,130,138,139,140', 'normal', 1490883540, 1586278359, 0);

-- ----------------------------
-- Table structure for yin_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `yin_auth_group_access`;
CREATE TABLE `yin_auth_group_access`  (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `group_id` int(10) UNSIGNED NOT NULL COMMENT '级别ID',
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '权限分组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_auth_group_access
-- ----------------------------
INSERT INTO `yin_auth_group_access` VALUES (1, 1);
INSERT INTO `yin_auth_group_access` VALUES (2, 2);
INSERT INTO `yin_auth_group_access` VALUES (3, 3);
INSERT INTO `yin_auth_group_access` VALUES (4, 3);
INSERT INTO `yin_auth_group_access` VALUES (5, 5);
INSERT INTO `yin_auth_group_access` VALUES (7, 5);

-- ----------------------------
-- Table structure for yin_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `yin_auth_rule`;
CREATE TABLE `yin_auth_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为菜单',
  `weigh` int(10) NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  INDEX `weigh`(`weigh`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 203 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '节点表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_auth_rule
-- ----------------------------
INSERT INTO `yin_auth_rule` VALUES (1, 'file', 0, 'dashboard', 'Dashboard', 'fa fa-dashboard', '', 'Dashboard tips', 1, 143, 'normal', 1497429920, 1497429920, 0);
INSERT INTO `yin_auth_rule` VALUES (2, 'file', 0, 'general', 'General', 'fa fa-cogs', '', '', 1, 137, 'normal', 1497429920, 1497430169, 0);
INSERT INTO `yin_auth_rule` VALUES (3, 'file', 0, 'category', 'Category', 'fa fa-leaf', '', 'Category tips', 0, 119, 'normal', 1497429920, 1585382172, 0);
INSERT INTO `yin_auth_rule` VALUES (4, 'file', 0, 'addon', 'Addon', 'fa fa-rocket', '', 'Addon tips', 0, 0, 'normal', 1502035509, 1585382152, 0);
INSERT INTO `yin_auth_rule` VALUES (5, 'file', 0, 'auth', 'Auth', 'fa fa-group', '', '', 1, 99, 'normal', 1497429920, 1497430092, 0);
INSERT INTO `yin_auth_rule` VALUES (6, 'file', 2, 'general/config', 'Config', 'fa fa-cog', '', 'Config tips', 1, 60, 'normal', 1497429920, 1497430683, 0);
INSERT INTO `yin_auth_rule` VALUES (7, 'file', 2, 'general/attachment', 'Attachment', 'fa fa-file-image-o', '', 'Attachment tips', 1, 53, 'normal', 1497429920, 1497430699, 0);
INSERT INTO `yin_auth_rule` VALUES (8, 'file', 2, 'general/profile', 'Profile', 'fa fa-user', '', '', 1, 34, 'normal', 1497429920, 1497429920, 0);
INSERT INTO `yin_auth_rule` VALUES (9, 'file', 5, 'auth/admin', 'Admin', 'fa fa-user', '', 'Admin tips', 1, 118, 'normal', 1497429920, 1497430320, 0);
INSERT INTO `yin_auth_rule` VALUES (10, 'file', 5, 'auth/adminlog', 'Admin log', 'fa fa-list-alt', '', 'Admin log tips', 1, 113, 'normal', 1497429920, 1497430307, 0);
INSERT INTO `yin_auth_rule` VALUES (11, 'file', 5, 'auth/group', 'Group', 'fa fa-group', '', 'Group tips', 1, 109, 'normal', 1497429920, 1497429920, 0);
INSERT INTO `yin_auth_rule` VALUES (12, 'file', 5, 'auth/rule', 'Rule', 'fa fa-bars', '', 'Rule tips', 1, 104, 'normal', 1497429920, 1497430581, 0);
INSERT INTO `yin_auth_rule` VALUES (13, 'file', 1, 'dashboard/index', '查看', 'fa fa-circle-o', '', '', 0, 136, 'normal', 1497429920, 1586278013, 0);
INSERT INTO `yin_auth_rule` VALUES (14, 'file', 1, 'dashboard/add', '添加', 'fa fa-circle-o', '', '', 0, 135, 'normal', 1497429920, 1586278013, 0);
INSERT INTO `yin_auth_rule` VALUES (15, 'file', 1, 'dashboard/del', '删除', 'fa fa-circle-o', '', '', 0, 133, 'normal', 1497429920, 1586278013, 0);
INSERT INTO `yin_auth_rule` VALUES (16, 'file', 1, 'dashboard/edit', '编辑', 'fa fa-circle-o', '', '', 0, 134, 'normal', 1497429920, 1586278013, 0);
INSERT INTO `yin_auth_rule` VALUES (17, 'file', 1, 'dashboard/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 132, 'normal', 1497429920, 1586278013, 0);
INSERT INTO `yin_auth_rule` VALUES (18, 'file', 6, 'general/config/index', '查看', 'fa fa-circle-o', '', '', 0, 52, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (19, 'file', 6, 'general/config/add', '添加', 'fa fa-circle-o', '', '', 0, 51, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (20, 'file', 6, 'general/config/edit', '编辑', 'fa fa-circle-o', '', '', 0, 50, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (21, 'file', 6, 'general/config/del', '删除', 'fa fa-circle-o', '', '', 0, 49, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (22, 'file', 6, 'general/config/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 48, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (23, 'file', 7, 'general/attachment/index', '查看', 'fa fa-circle-o', '', 'Attachment tips', 0, 59, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (24, 'file', 7, 'general/attachment/select', '选择附件', 'fa fa-circle-o', '', '', 0, 58, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (25, 'file', 7, 'general/attachment/add', '添加', 'fa fa-circle-o', '', '', 0, 57, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (26, 'file', 7, 'general/attachment/edit', '编辑', 'fa fa-circle-o', '', '', 0, 56, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (27, 'file', 7, 'general/attachment/del', '删除附件', 'fa fa-circle-o', '', '', 0, 55, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (28, 'file', 7, 'general/attachment/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 54, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (29, 'file', 8, 'general/profile/index', '查看', 'fa fa-circle-o', '', '', 0, 33, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (30, 'file', 8, 'general/profile/update', '更新个人信息', 'fa fa-circle-o', '', '', 0, 32, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (31, 'file', 8, 'general/profile/add', '添加', 'fa fa-circle-o', '', '', 0, 31, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (32, 'file', 8, 'general/profile/edit', '编辑', 'fa fa-circle-o', '', '', 0, 30, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (33, 'file', 8, 'general/profile/del', '删除', 'fa fa-circle-o', '', '', 0, 29, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (34, 'file', 8, 'general/profile/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 28, 'normal', 1497429920, 1586278014, 0);
INSERT INTO `yin_auth_rule` VALUES (35, 'file', 3, 'category/index', '查看', 'fa fa-circle-o', '', 'Category tips', 0, 142, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (36, 'file', 3, 'category/add', '添加', 'fa fa-circle-o', '', '', 0, 141, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (37, 'file', 3, 'category/edit', '编辑', 'fa fa-circle-o', '', '', 0, 140, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (38, 'file', 3, 'category/del', '删除', 'fa fa-circle-o', '', '', 0, 139, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (39, 'file', 3, 'category/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 138, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (40, 'file', 9, 'auth/admin/index', '查看', 'fa fa-circle-o', '', 'Admin tips', 0, 117, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (41, 'file', 9, 'auth/admin/add', '添加', 'fa fa-circle-o', '', '', 0, 116, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (42, 'file', 9, 'auth/admin/edit', '编辑', 'fa fa-circle-o', '', '', 0, 115, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (43, 'file', 9, 'auth/admin/del', '删除', 'fa fa-circle-o', '', '', 0, 114, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (44, 'file', 10, 'auth/adminlog/index', '查看', 'fa fa-circle-o', '', 'Admin log tips', 0, 112, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (45, 'file', 10, 'auth/adminlog/detail', '详情', 'fa fa-circle-o', '', '', 0, 111, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (46, 'file', 10, 'auth/adminlog/del', '删除', 'fa fa-circle-o', '', '', 0, 110, 'normal', 1497429920, 1586278015, 0);
INSERT INTO `yin_auth_rule` VALUES (47, 'file', 11, 'auth/group/index', '查看', 'fa fa-circle-o', '', 'Group tips', 0, 108, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (48, 'file', 11, 'auth/group/add', '添加', 'fa fa-circle-o', '', '', 0, 107, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (49, 'file', 11, 'auth/group/edit', '编辑', 'fa fa-circle-o', '', '', 0, 106, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (50, 'file', 11, 'auth/group/del', '删除', 'fa fa-circle-o', '', '', 0, 105, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (51, 'file', 12, 'auth/rule/index', '查看', 'fa fa-circle-o', '', 'Rule tips', 0, 103, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (52, 'file', 12, 'auth/rule/add', '添加', 'fa fa-circle-o', '', '', 0, 102, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (53, 'file', 12, 'auth/rule/edit', '编辑', 'fa fa-circle-o', '', '', 0, 101, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (54, 'file', 12, 'auth/rule/del', '删除', 'fa fa-circle-o', '', '', 0, 100, 'normal', 1497429920, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (55, 'file', 4, 'addon/index', 'View', 'fa fa-circle-o', '', 'Addon tips', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (56, 'file', 4, 'addon/add', 'Add', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (57, 'file', 4, 'addon/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (58, 'file', 4, 'addon/del', 'Delete', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (59, 'file', 4, 'addon/downloaded', 'Local addon', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (60, 'file', 4, 'addon/state', 'Update state', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (63, 'file', 4, 'addon/config', 'Setting', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (64, 'file', 4, 'addon/refresh', 'Refresh', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (65, 'file', 4, 'addon/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1502035509, 1502035509, 0);
INSERT INTO `yin_auth_rule` VALUES (66, 'file', 0, 'user', 'User', 'fa fa-list', '', '', 0, 0, 'normal', 1516374729, 1585382164, 0);
INSERT INTO `yin_auth_rule` VALUES (67, 'file', 66, 'user/user', 'User', 'fa fa-user', '', '', 1, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (68, 'file', 67, 'user/user/index', 'View', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (69, 'file', 67, 'user/user/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (70, 'file', 67, 'user/user/add', 'Add', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (71, 'file', 67, 'user/user/del', 'Del', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (72, 'file', 67, 'user/user/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (73, 'file', 66, 'user/group', 'User group', 'fa fa-users', '', '', 1, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (74, 'file', 73, 'user/group/add', 'Add', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (75, 'file', 73, 'user/group/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (76, 'file', 73, 'user/group/index', 'View', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (77, 'file', 73, 'user/group/del', 'Del', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (78, 'file', 73, 'user/group/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (79, 'file', 66, 'user/rule', 'User rule', 'fa fa-circle-o', '', '', 1, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (80, 'file', 79, 'user/rule/index', 'View', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (81, 'file', 79, 'user/rule/del', 'Del', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (82, 'file', 79, 'user/rule/add', 'Add', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (83, 'file', 79, 'user/rule/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (84, 'file', 79, 'user/rule/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1516374729, 1516374729, 0);
INSERT INTO `yin_auth_rule` VALUES (85, 'file', 0, 'xunsearch', 'Xunsearch全文搜索管理', 'fa fa-search', '', '', 0, 0, 'normal', 1585380158, 1585382159, 0);
INSERT INTO `yin_auth_rule` VALUES (86, 'file', 85, 'xunsearch/project', '项目管理', 'fa fa-cog', '', '', 1, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (87, 'file', 86, 'xunsearch/project/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (88, 'file', 86, 'xunsearch/project/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (89, 'file', 86, 'xunsearch/project/edit', '修改', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (90, 'file', 86, 'xunsearch/project/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (91, 'file', 86, 'xunsearch/project/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (92, 'file', 86, 'xunsearch/project/reset', '重置索引', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (93, 'file', 86, 'xunsearch/project/refresh', '生成配置', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (94, 'file', 86, 'xunsearch/project/flush', '强制刷新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (95, 'file', 86, 'xunsearch/project/config', '加载配置', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (96, 'file', 85, 'xunsearch/fields', '字段管理', 'fa fa-list', '', '管理项目索引的字段，如果增加了字段，请确保相应的整合接口有写入该字段数据', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (97, 'file', 96, 'xunsearch/fields/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (98, 'file', 96, 'xunsearch/fields/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (99, 'file', 96, 'xunsearch/fields/edit', '修改', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (100, 'file', 96, 'xunsearch/fields/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (101, 'file', 96, 'xunsearch/fields/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (102, 'file', 85, 'xunsearch/logger', '搜索词管理', 'fa fa-list', '', '如果搜索词没有相应的搜索结果，Xunsearch不会记录该记录值', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (103, 'file', 102, 'xunsearch/logger/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (104, 'file', 102, 'xunsearch/logger/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (105, 'file', 102, 'xunsearch/logger/edit', '修改', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (106, 'file', 102, 'xunsearch/logger/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (107, 'file', 102, 'xunsearch/logger/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585380158, 1585380158, 0);
INSERT INTO `yin_auth_rule` VALUES (108, 'file', 0, 'team', '团队管理', 'fa fa-sitemap', '', '', 1, 0, 'normal', 1585485572, 1586262603, 0);
INSERT INTO `yin_auth_rule` VALUES (109, 'file', 108, 'team/team', '团队管理', 'fa fa-address-book-o', '', '', 1, 0, 'normal', 1585485572, 1586262832, 0);
INSERT INTO `yin_auth_rule` VALUES (110, 'file', 109, 'team/team/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (111, 'file', 109, 'team/team/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (112, 'file', 109, 'team/team/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (113, 'file', 109, 'team/team/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (114, 'file', 109, 'team/team/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (115, 'file', 109, 'team/team/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (116, 'file', 109, 'team/team/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (117, 'file', 109, 'team/team/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485572, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (118, 'file', 0, 'production', '产品管理', 'fa fa-cubes', '', '', 1, 0, 'normal', 1585485885, 1586262661, 0);
INSERT INTO `yin_auth_rule` VALUES (119, 'file', 118, 'production/production', '产品管理', 'fa fa-cube', '', '', 1, 0, 'normal', 1585485885, 1586262863, 0);
INSERT INTO `yin_auth_rule` VALUES (120, 'file', 119, 'production/production/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (121, 'file', 119, 'production/production/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (122, 'file', 119, 'production/production/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (123, 'file', 119, 'production/production/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (124, 'file', 119, 'production/production/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (125, 'file', 119, 'production/production/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (126, 'file', 119, 'production/production/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (127, 'file', 119, 'production/production/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585485885, 1586278016, 0);
INSERT INTO `yin_auth_rule` VALUES (128, 'file', 0, 'order', '订单管理', 'fa fa-first-order', '', '', 1, 0, 'normal', 1585487038, 1586262688, 0);
INSERT INTO `yin_auth_rule` VALUES (129, 'file', 128, 'order/order', '订单管理', 'fa fa-reorder', '', '', 1, 0, 'normal', 1585487038, 1586262927, 0);
INSERT INTO `yin_auth_rule` VALUES (130, 'file', 129, 'order/order/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (131, 'file', 129, 'order/order/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (132, 'file', 129, 'order/order/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586270435, 0);
INSERT INTO `yin_auth_rule` VALUES (133, 'file', 129, 'order/order/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (134, 'file', 129, 'order/order/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (135, 'file', 129, 'order/order/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (136, 'file', 129, 'order/order/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (137, 'file', 129, 'order/order/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487038, 1586278017, 0);
INSERT INTO `yin_auth_rule` VALUES (138, 'file', 0, 'express', '快递管理', 'fa fa-space-shuttle', '', '', 1, 0, 'normal', 1585487195, 1586262778, 0);
INSERT INTO `yin_auth_rule` VALUES (139, 'file', 138, 'express/express', '快递信息管理', 'fa fa-fast-forward', '', '', 1, 0, 'normal', 1585487195, 1586262957, 0);
INSERT INTO `yin_auth_rule` VALUES (140, 'file', 139, 'express/express/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (141, 'file', 139, 'express/express/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (142, 'file', 139, 'express/express/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (143, 'file', 139, 'express/express/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (144, 'file', 139, 'express/express/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (145, 'file', 139, 'express/express/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (146, 'file', 139, 'express/express/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (147, 'file', 139, 'express/express/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585487195, 1586278320, 0);
INSERT INTO `yin_auth_rule` VALUES (148, 'file', 118, 'production/select', '产品文案选择', 'fa fa-file-word-o', '', '', 1, 0, 'normal', 1585628324, 1586262880, NULL);
INSERT INTO `yin_auth_rule` VALUES (149, 'file', 148, 'production/select/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (150, 'file', 148, 'production/select/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (151, 'file', 148, 'production/select/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (152, 'file', 148, 'production/select/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (153, 'file', 148, 'production/select/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (154, 'file', 148, 'production/select/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (155, 'file', 148, 'production/select/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (156, 'file', 148, 'production/select/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585628324, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (157, 'file', 0, 'sysconfig', '系统设置', 'fa fa-cog', '', '', 1, 0, 'normal', 1585712517, 1586262809, NULL);
INSERT INTO `yin_auth_rule` VALUES (158, 'file', 157, 'sysconfig/pay', '支付设置', 'fa fa-weixin', '', '', 1, 0, 'normal', 1585712517, 1586262988, NULL);
INSERT INTO `yin_auth_rule` VALUES (159, 'file', 158, 'sysconfig/pay/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (160, 'file', 158, 'sysconfig/pay/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (161, 'file', 158, 'sysconfig/pay/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (162, 'file', 158, 'sysconfig/pay/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (163, 'file', 158, 'sysconfig/pay/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (164, 'file', 158, 'sysconfig/pay/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (165, 'file', 158, 'sysconfig/pay/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (166, 'file', 158, 'sysconfig/pay/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1585712517, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (167, 'file', 118, 'production/url', '商品链接', 'fa fa-link', '', '', 1, 0, 'normal', 1586163914, 1586262907, NULL);
INSERT INTO `yin_auth_rule` VALUES (168, 'file', 167, 'production/url/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (169, 'file', 167, 'production/url/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586263526, NULL);
INSERT INTO `yin_auth_rule` VALUES (170, 'file', 167, 'production/url/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586263526, NULL);
INSERT INTO `yin_auth_rule` VALUES (171, 'file', 167, 'production/url/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586263526, NULL);
INSERT INTO `yin_auth_rule` VALUES (172, 'file', 167, 'production/url/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586263526, NULL);
INSERT INTO `yin_auth_rule` VALUES (173, 'file', 167, 'production/url/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586265818, NULL);
INSERT INTO `yin_auth_rule` VALUES (174, 'file', 167, 'production/url/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586265818, NULL);
INSERT INTO `yin_auth_rule` VALUES (175, 'file', 167, 'production/url/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586163914, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (176, 'file', 157, 'sysconfig/consumables', '炮灰域名', 'fa fa-bomb', '', '', 1, 0, 'normal', 1586186861, 1586263015, NULL);
INSERT INTO `yin_auth_rule` VALUES (177, 'file', 176, 'sysconfig/consumables/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186861, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (178, 'file', 176, 'sysconfig/consumables/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (179, 'file', 176, 'sysconfig/consumables/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (180, 'file', 176, 'sysconfig/consumables/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (181, 'file', 176, 'sysconfig/consumables/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (182, 'file', 176, 'sysconfig/consumables/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (183, 'file', 176, 'sysconfig/consumables/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (184, 'file', 176, 'sysconfig/consumables/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186862, 1586278018, NULL);
INSERT INTO `yin_auth_rule` VALUES (185, 'file', 157, 'sysconfig/ground', '落地域名', 'fa fa-diamond', '', '', 1, 0, 'normal', 1586186876, 1586263062, NULL);
INSERT INTO `yin_auth_rule` VALUES (186, 'file', 185, 'sysconfig/ground/index', '查看', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (187, 'file', 185, 'sysconfig/ground/recyclebin', '回收站', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (188, 'file', 185, 'sysconfig/ground/add', '添加', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (189, 'file', 185, 'sysconfig/ground/edit', '编辑', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (190, 'file', 185, 'sysconfig/ground/del', '删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (191, 'file', 185, 'sysconfig/ground/destroy', '真实删除', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (192, 'file', 185, 'sysconfig/ground/restore', '还原', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (193, 'file', 185, 'sysconfig/ground/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586186876, 1586278019, NULL);
INSERT INTO `yin_auth_rule` VALUES (194, 'file', 9, 'auth/admin/selectpage', '下拉搜索', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586261611, 1586278015, NULL);
INSERT INTO `yin_auth_rule` VALUES (195, 'file', 9, 'auth/admin/url', '获取登录地址', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586261611, 1586278015, NULL);
INSERT INTO `yin_auth_rule` VALUES (196, 'file', 9, 'auth/admin/build', '生成二维码', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586261611, 1586278015, NULL);
INSERT INTO `yin_auth_rule` VALUES (197, 'file', 10, 'auth/adminlog/selectpage', 'Selectpage', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586263524, 1586278015, NULL);
INSERT INTO `yin_auth_rule` VALUES (198, 'file', 12, 'auth/rule/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586263524, 1586278016, NULL);
INSERT INTO `yin_auth_rule` VALUES (199, 'file', 167, 'production/url/url', '获取登录地址', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586265818, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (200, 'file', 167, 'production/url/build', '生成二维码', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586265818, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (201, 'file', 129, 'order/order/detail', '订单详情', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586273412, 1586278017, NULL);
INSERT INTO `yin_auth_rule` VALUES (202, 'file', 139, 'express/express/import', '导入', 'fa fa-circle-o', '', '', 0, 0, 'normal', 1586278320, 1586278320, NULL);

-- ----------------------------
-- Table structure for yin_cash_time
-- ----------------------------
DROP TABLE IF EXISTS `yin_cash_time`;
CREATE TABLE `yin_cash_time`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `cash_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结算时间',
  `send_sms` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否短信通知 0=否，1=是',
  `sms_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '短信内容',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '结算通知设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_cash_time
-- ----------------------------

-- ----------------------------
-- Table structure for yin_category
-- ----------------------------
DROP TABLE IF EXISTS `yin_category`;
CREATE TABLE `yin_category`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父ID',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '栏目类型',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `flag` set('hot','index','recommend') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `image` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图片',
  `keywords` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `diyname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '自定义名称',
  `createtime` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `deletetime` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  `weigh` int(10) NOT NULL DEFAULT 0 COMMENT '权重',
  `status` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `weigh`(`weigh`, `id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_category
-- ----------------------------
INSERT INTO `yin_category` VALUES (1, 0, 'page', '官方新闻', 'news', 'recommend', '/assets/img/qrcode.png', '', '', 'news', 1495262190, 1495262190, NULL, 1, 'normal');
INSERT INTO `yin_category` VALUES (2, 0, 'page', '移动应用', 'mobileapp', 'hot', '/assets/img/qrcode.png', '', '', 'mobileapp', 1495262244, 1495262244, NULL, 2, 'normal');
INSERT INTO `yin_category` VALUES (3, 2, 'page', '微信公众号', 'wechatpublic', 'index', '/assets/img/qrcode.png', '', '', 'wechatpublic', 1495262288, 1495262288, NULL, 3, 'normal');
INSERT INTO `yin_category` VALUES (4, 2, 'page', 'Android开发', 'android', 'recommend', '/assets/img/qrcode.png', '', '', 'android', 1495262317, 1495262317, NULL, 4, 'normal');
INSERT INTO `yin_category` VALUES (5, 0, 'page', '软件产品', 'software', 'recommend', '/assets/img/qrcode.png', '', '', 'software', 1495262336, 1499681850, NULL, 5, 'normal');
INSERT INTO `yin_category` VALUES (6, 5, 'page', '网站建站', 'website', 'recommend', '/assets/img/qrcode.png', '', '', 'website', 1495262357, 1495262357, NULL, 6, 'normal');
INSERT INTO `yin_category` VALUES (7, 5, 'page', '企业管理软件', 'company', 'index', '/assets/img/qrcode.png', '', '', 'company', 1495262391, 1495262391, NULL, 7, 'normal');
INSERT INTO `yin_category` VALUES (8, 6, 'page', 'PC端', 'website-pc', 'recommend', '/assets/img/qrcode.png', '', '', 'website-pc', 1495262424, 1495262424, NULL, 8, 'normal');
INSERT INTO `yin_category` VALUES (9, 6, 'page', '移动端', 'website-mobile', 'recommend', '/assets/img/qrcode.png', '', '', 'website-mobile', 1495262456, 1495262456, NULL, 9, 'normal');
INSERT INTO `yin_category` VALUES (10, 7, 'page', 'CRM系统 ', 'company-crm', 'recommend', '/assets/img/qrcode.png', '', '', 'company-crm', 1495262487, 1495262487, NULL, 10, 'normal');
INSERT INTO `yin_category` VALUES (11, 7, 'page', 'SASS平台软件', 'company-sass', 'recommend', '/assets/img/qrcode.png', '', '', 'company-sass', 1495262515, 1495262515, NULL, 11, 'normal');
INSERT INTO `yin_category` VALUES (12, 0, 'test', '测试1', 'test1', 'recommend', '/assets/img/qrcode.png', '', '', 'test1', 1497015727, 1497015727, NULL, 12, 'normal');
INSERT INTO `yin_category` VALUES (13, 0, 'test', '测试2', 'test2', 'recommend', '/assets/img/qrcode.png', '', '', 'test2', 1497015738, 1497015738, NULL, 13, 'normal');

-- ----------------------------
-- Table structure for yin_comment
-- ----------------------------
DROP TABLE IF EXISTS `yin_comment`;
CREATE TABLE `yin_comment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '评论人手机号',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户代理',
  `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论内容',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '评论表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_comment
-- ----------------------------

-- ----------------------------
-- Table structure for yin_config
-- ----------------------------
DROP TABLE IF EXISTS `yin_config`;
CREATE TABLE `yin_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变量值',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '扩展属性',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_config
-- ----------------------------
INSERT INTO `yin_config` VALUES (1, 'name', 'basic', 'Site name', '请填写站点名称', 'string', '营销平台', '', 'required', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (2, 'beian', 'basic', 'Beian', '粤ICP备15000000号-1', 'string', '', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (3, 'cdnurl', 'basic', 'Cdn url', '如果静态资源使用第三方云储存请配置该值', 'string', '', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (4, 'version', 'basic', 'Version', '如果静态资源有变动请重新配置该值', 'string', '1.0.1', '', 'required', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (5, 'timezone', 'basic', 'Timezone', '', 'string', 'Asia/Shanghai', '', 'required', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (6, 'forbiddenip', 'basic', 'Forbidden ip', '一行一条记录', 'text', '', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (7, 'languages', 'basic', 'Languages', '', 'array', '{\"backend\":\"zh-cn\",\"frontend\":\"zh-cn\"}', '', 'required', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (8, 'fixedpage', 'basic', 'Fixed page', '请尽量输入左侧菜单栏存在的链接', 'string', 'dashboard', '', 'required', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (9, 'categorytype', 'dictionary', 'Category type', '', 'array', '{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (10, 'configgroup', 'dictionary', 'Config group', '', 'array', '{\"basic\":\"Basic\",\"email\":\"Email\",\"dictionary\":\"Dictionary\",\"user\":\"User\",\"example\":\"Example\"}', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (11, 'mail_type', 'email', 'Mail type', '选择邮件发送方式', 'select', '1', '[\"Please select\",\"SMTP\",\"Mail\"]', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (12, 'mail_smtp_host', 'email', 'Mail smtp host', '错误的配置发送邮件会导致服务器超时', 'string', 'smtp.qq.com', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (13, 'mail_smtp_port', 'email', 'Mail smtp port', '(不加密默认25,SSL默认465,TLS默认587)', 'string', '465', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (14, 'mail_smtp_user', 'email', 'Mail smtp user', '（填写完整用户名）', 'string', '10000', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (15, 'mail_smtp_pass', 'email', 'Mail smtp password', '（填写您的密码）', 'string', 'password', '', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (16, 'mail_verify_type', 'email', 'Mail vertify type', '（SMTP验证方式[推荐SSL]）', 'select', '2', '[\"None\",\"TLS\",\"SSL\"]', '', '', 0, 0, 0);
INSERT INTO `yin_config` VALUES (17, 'mail_from', 'email', 'Mail from', '', 'string', '10000@qq.com', '', '', '', 0, 0, 0);

-- ----------------------------
-- Table structure for yin_consumables_domain
-- ----------------------------
DROP TABLE IF EXISTS `yin_consumables_domain`;
CREATE TABLE `yin_consumables_domain`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '域名链接',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '使用次数',
  `is_forbidden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否被封,0=否,1=是',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '炮灰域名' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_consumables_domain
-- ----------------------------

-- ----------------------------
-- Table structure for yin_domain_check
-- ----------------------------
DROP TABLE IF EXISTS `yin_domain_check`;
CREATE TABLE `yin_domain_check`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '全局返回广告',
  `every_min` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '每隔几秒检测',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否启用0=否，1=是',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '防封检测设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_domain_check
-- ----------------------------

-- ----------------------------
-- Table structure for yin_ems
-- ----------------------------
DROP TABLE IF EXISTS `yin_ems`;
CREATE TABLE `yin_ems`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `event` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '事件',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `times` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '验证次数',
  `ip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '邮箱验证码表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_ems
-- ----------------------------

-- ----------------------------
-- Table structure for yin_express
-- ----------------------------
DROP TABLE IF EXISTS `yin_express`;
CREATE TABLE `yin_express`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单id',
  `order_sn` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单编号',
  `express_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递单号',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `express_com` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递公司',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '快递信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_express
-- ----------------------------

-- ----------------------------
-- Table structure for yin_ground_domain
-- ----------------------------
DROP TABLE IF EXISTS `yin_ground_domain`;
CREATE TABLE `yin_ground_domain`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '域名链接',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '使用次数',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `is_forbidden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否被封,0=否,1=是',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '落地域名' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_ground_domain
-- ----------------------------

-- ----------------------------
-- Table structure for yin_migrations
-- ----------------------------
DROP TABLE IF EXISTS `yin_migrations`;
CREATE TABLE `yin_migrations`  (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `start_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `end_time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_migrations
-- ----------------------------
INSERT INTO `yin_migrations` VALUES (20200330090159, 'AdminAddLevel', '2020-03-30 17:20:43', '2020-03-30 17:20:43', 0);
INSERT INTO `yin_migrations` VALUES (20200331031112, 'ProductionSelect', '2020-03-31 13:01:59', '2020-03-31 13:01:59', 0);
INSERT INTO `yin_migrations` VALUES (20200331053003, 'Production', '2020-03-31 14:35:23', '2020-03-31 14:35:23', 0);
INSERT INTO `yin_migrations` VALUES (20200331083149, 'SysconfigPay', '2020-04-01 11:35:43', '2020-04-01 11:35:43', 0);
INSERT INTO `yin_migrations` VALUES (20200401120158, 'OrderAddAndModify', '2020-04-01 20:17:58', '2020-04-01 20:17:58', 0);
INSERT INTO `yin_migrations` VALUES (20200403045839, 'SysconfigPayModify', '2020-04-03 13:10:24', '2020-04-03 13:10:25', 0);
INSERT INTO `yin_migrations` VALUES (20200405160607, 'SysconfigPayAddField', '2020-04-06 00:20:32', '2020-04-06 00:20:33', 0);
INSERT INTO `yin_migrations` VALUES (20200405165829, 'OrderAddField', '2020-04-06 17:01:10', '2020-04-06 17:01:11', 0);
INSERT INTO `yin_migrations` VALUES (20200406083043, 'ConsumablesDomain', '2020-04-06 17:01:11', '2020-04-06 17:01:11', 0);
INSERT INTO `yin_migrations` VALUES (20200406083955, 'ProductionUrl', '2020-04-06 17:01:11', '2020-04-06 17:01:11', 0);
INSERT INTO `yin_migrations` VALUES (20200406084846, 'GroundDomain', '2020-04-06 17:01:42', '2020-04-06 17:01:42', 0);
INSERT INTO `yin_migrations` VALUES (20200406175059, 'OrderModifyandAdd', '2020-04-07 01:55:47', '2020-04-07 01:55:48', 0);

-- ----------------------------
-- Table structure for yin_order
-- ----------------------------
DROP TABLE IF EXISTS `yin_order`;
CREATE TABLE `yin_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单编号',
  `price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '订单金额',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客户姓名',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客户电话',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货地址',
  `production_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品ID',
  `production_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品名称',
  `goods_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品信息',
  `num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业务员ID',
  `admin_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '业务员姓名',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级领导人ID',
  `pay_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付方式,0=微信，1=支付宝',
  `pay_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付状态,0=未支付，1=已支付',
  `express_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '快递Id',
  `express_com` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递名称',
  `express_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递单号',
  `transaction_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付平台订单号',
  `openid` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '顾客openid',
  `nonce_str` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '回调随机字串',
  `pay_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付通道ID',
  `pay_url` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名',
  `order_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '订单状态,0=正常出货中,1=退货,2=补货,3=退款,4=退货退款',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单备注',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_order
-- ----------------------------
INSERT INTO `yin_order` VALUES (1, 135, '', '', 79.90, '罗露', '15207335533', '江苏省无锡市滨湖区地板革但是发射点发卡了就是分厘卡士大夫考', 6, '投诉售后电话4007807606', 'pattern=54-sex=男款-attr=40码', 1, 2, '老板11', 0, 2, 1, 0, '', '', '', '', '', 0, '', 0, '', 1585746043, 1585746043, NULL);
INSERT INTO `yin_order` VALUES (2, 2, '团队2', '', 79.99, '雨林之后', '15212341234', '上海市上海市嘉定区泰国榴莲哈夫曼哈根斯但是立刻就范德萨立刻解放', 1, '投诉售后电话4007807606', 'pattern=55;sex=男款;attr=40码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 0, '', 0, '', 1585749943, 1585749943, NULL);
INSERT INTO `yin_order` VALUES (3, 2, '团队2', '2020040122224520000200002368', 79.99, '雨林', '15208121234', '安徽省-铜陵市-新城区-泰国榴莲哈夫曼哈根斯但是立刻就范德萨立刻解放', 1, '投诉售后电话4007807606', 'pattern=52;sex=男款;attr=41码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585750965, 1585750965, NULL);
INSERT INTO `yin_order` VALUES (4, 2, '团队2', '2020040122250430000200007426', 79.99, '小溪', '15107301212', '湖南省-永州市-冷水滩区-泰国榴莲哈夫曼哈根斯但是立刻就范德萨立刻解放', 1, '投诉售后电话4007807606', 'pattern=55;sex=女款;attr=38码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585751104, 1585751104, NULL);
INSERT INTO `yin_order` VALUES (5, 2, '团队2', '2020040122275900003000028733', 79.99, '小美', '15507211212', '辽宁省-朝阳市-凌源市-泰国榴莲哈夫曼哈根斯但是立刻就范德萨立刻解放', 1, '投诉售后电话4007807606', 'pattern=52;sex=女款;attr=36码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585751279, 1585751279, NULL);
INSERT INTO `yin_order` VALUES (6, 2, '团队2', '2020040123392100003000028223', 79.99, '小丽', '16604561234', '湖南省-湘潭市-湘乡市-泰国榴莲哈夫曼哈根斯但是立刻就范德萨立刻解放', 1, '投诉售后电话4007807606', 'pattern=53;sex=女款;attr=38码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585755561, 1585755561, NULL);
INSERT INTO `yin_order` VALUES (15, 2, '团队2', '2020040123582600003000022971', 79.99, '小兴', '15104711520', '吉林省-四平市-梨树县-是可见的拉科夫可见度撒了发的撒反对法反对', 1, '投诉售后电话4007807606', 'pattern=54;sex=男款;attr=40码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585756706, 1585756706, NULL);
INSERT INTO `yin_order` VALUES (16, 2, '团队2', '2020040200003200003000025137', 79.99, '小兴', '15104711520', '吉林省-四平市-梨树县-是可见的拉科夫可见度撒了发的撒反对法反对', 1, '投诉售后电话4007807606', 'pattern=54;sex=男款;attr=40码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585756832, 1585756832, NULL);
INSERT INTO `yin_order` VALUES (17, 2, '团队2', '2020040200034500003000026979', 79.99, '小志', '15512345678', '江西省-上饶市-弋阳县-是可见的拉科夫可见度撒了发的撒反对法反对', 1, '投诉售后电话4007807606', 'pattern=53;sex=男款;attr=41码', 1, 3, '组长1', 2, 2, 1, 0, '', '', '', '', '', 2, '', 0, '', 1585757025, 1585757025, NULL);
INSERT INTO `yin_order` VALUES (18, 2, '团队2', '2020040314143400003000025680', 79.99, '小果', '15207335533', '湖北省-武汉市-汉南区-考虑到拉开圣诞节发动机发生', 1, '投诉售后电话4007807606', 'pattern=53;sex=女款;attr=37码', 1, 3, '组长1', 2, 0, 1, 0, '', '', '4200000495202004060198644235', '', '', 3, '', 0, '', 1585894474, 1586195161, NULL);

-- ----------------------------
-- Table structure for yin_order_filter
-- ----------------------------
DROP TABLE IF EXISTS `yin_order_filter`;
CREATE TABLE `yin_order_filter`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `production_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品ID',
  `production_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '产品名称',
  `filter_area` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '已过滤地区',
  `filter_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '过滤原因',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '下单过滤设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_order_filter
-- ----------------------------

-- ----------------------------
-- Table structure for yin_plat_ad
-- ----------------------------
DROP TABLE IF EXISTS `yin_plat_ad`;
CREATE TABLE `yin_plat_ad`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `return_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '全局返回广告',
  `is_use` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否启用0=否，1=是',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '全局广告设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_plat_ad
-- ----------------------------

-- ----------------------------
-- Table structure for yin_production
-- ----------------------------
DROP TABLE IF EXISTS `yin_production`;
CREATE TABLE `yin_production`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品名称',
  `sales_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '销售价',
  `discount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '优惠券',
  `true_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实价',
  `phone1` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客服电话1',
  `phone2` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客服电话2',
  `qr_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信客服二维码',
  `modulefile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文案模板',
  `special_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '特征码',
  `tongji` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '统计代码',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否上架，0=上架，1=下架',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '使用次数',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '产品文案库' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_production
-- ----------------------------
INSERT INTO `yin_production` VALUES (1, '鞋子', 799.99, 720.00, 79.99, '120', '119', '\'\'', '/uploads/20200331/2cb883566a7d43614af3d72835edd42a.html', 'dsfsdfdsfdsf', 'sfdsfdsfdsfdfd', 0, 0, 1585636586, 1585636586, NULL);
INSERT INTO `yin_production` VALUES (2, '鞋子1212', 888.00, 800.00, 88.00, '119', '110', '\'\'', '/uploads/20200331/2cb883566a7d43614af3d72835edd42a.html', 'drteteyrsfdsf', 'qrewewtr', 0, 0, 1585637836, 1585637836, NULL);

-- ----------------------------
-- Table structure for yin_production_select
-- ----------------------------
DROP TABLE IF EXISTS `yin_production_select`;
CREATE TABLE `yin_production_select`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `sales_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '销售价',
  `discount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '优惠券',
  `true_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实价',
  `production_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品ID',
  `production_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '产品名称',
  `phone1` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客服电话1',
  `phone2` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '客服电话2',
  `special_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '特征码',
  `tongji` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '统计代码',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '产品文案选择' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_production_select
-- ----------------------------
INSERT INTO `yin_production_select` VALUES (1, 1, '团队1', 789.00, 700.00, 89.00, 1, '鞋子', '123', '111', '1231223312', '21232312323231', 1585640856, 1585641784, NULL);
INSERT INTO `yin_production_select` VALUES (2, 0, '平台测试', 123.00, 100.00, 23.00, 1, '鞋子', '120', '119', '123123123', '45456456456', 1586244927, 1586245224, NULL);

-- ----------------------------
-- Table structure for yin_production_url
-- ----------------------------
DROP TABLE IF EXISTS `yin_production_url`;
CREATE TABLE `yin_production_url`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `production_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
  `production_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `url` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '推广链接',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '访问次数',
  `order_done` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '成单数量',
  `is_forbidden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否被封,0=否,1=是',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品链接' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_production_url
-- ----------------------------

-- ----------------------------
-- Table structure for yin_service
-- ----------------------------
DROP TABLE IF EXISTS `yin_service`;
CREATE TABLE `yin_service`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `order_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID',
  `order_sn` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单编号',
  `why` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '售后原因',
  `process` tinyint(1) NOT NULL DEFAULT 1 COMMENT '处理进度,0=已完成,1=正在处理中,2=拒绝处理,3=退货',
  `reply` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '回复内容',
  `order_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买时间',
  `return` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退货时间',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '售后电话',
  `domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '售后域名',
  `return_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '退货收货人',
  `return_phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收货人电话',
  `return_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '退货售后地址',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '售后表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_service
-- ----------------------------

-- ----------------------------
-- Table structure for yin_sms
-- ----------------------------
DROP TABLE IF EXISTS `yin_sms`;
CREATE TABLE `yin_sms`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `event` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '事件',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `times` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '验证次数',
  `ip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '短信验证码表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_sms
-- ----------------------------

-- ----------------------------
-- Table structure for yin_sysconfig
-- ----------------------------
DROP TABLE IF EXISTS `yin_sysconfig`;
CREATE TABLE `yin_sysconfig`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `in_domain` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '入口域名',
  `to_domain` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '落地域名',
  `fast_domain` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '快站域名',
  `auto_check` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '落地域名自动检测。0=关，1=开',
  `server_ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '本服务器IP',
  `one_ip` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '同一IP每日最多下单量',
  `pay_win` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付框弹出方式,0=立即弹出，1=用户点击确认支付按钮后弹出',
  `no_shipping` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '不包邮地区',
  `ship_fee` decimal(10, 2) UNSIGNED NOT NULL COMMENT '邮费',
  `count_tips` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '倒计时提示语',
  `count_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '倒计时',
  `is_voice` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新消息提示 0=关，1=开',
  `is_money` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '红包模式 0=关，1=开',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '平台设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_sysconfig
-- ----------------------------

-- ----------------------------
-- Table structure for yin_sysconfig_pay
-- ----------------------------
DROP TABLE IF EXISTS `yin_sysconfig_pay`;
CREATE TABLE `yin_sysconfig_pay`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `pay_domain1` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名1',
  `pay_domain2` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名2',
  `pay_domain3` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名3',
  `pay_domain4` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名4',
  `pay_domain5` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付域名5',
  `token` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '公众号token',
  `encodingaeskey` varchar(43) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '消息加密',
  `ssl_cer` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付证书',
  `ssl_key` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付证书密钥',
  `pay_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付名称',
  `app_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '开发者ID',
  `app_secret` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '开发者密钥',
  `mch_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户号',
  `mch_key` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付密钥',
  `mchv3_key` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'apiv3密钥',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '增加时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '支付设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_sysconfig_pay
-- ----------------------------
INSERT INTO `yin_sysconfig_pay` VALUES (1, 0, '', 'fdaqfwefqwef.com', 'qtewiuyroiqw.com', 'iquyewoiqouewqoi.com', '', '', '', '', '', '', '平台级支付账号', 'qwetyuiopasdfgjkl', 'etuiodfjklcvbnmjkefshkdsa', '3456789345678', 'dsafiwiqpoeurjfewlkuewqhfkshfwekf', 'woiqroiwqrqjpowrjqwpfewpojqpofjqwpojfqopfjqpofjqpo', 1585715679, 1585715679, NULL);
INSERT INTO `yin_sysconfig_pay` VALUES (2, 2, '团队2', 'twieqhrje.com', 'woiqrew.com', 'qweorusdf.com', '', '', '', '', '', '', '团队1-老板1的微信支付', 'fghsdwqytiurhwj', '345ftewuj7tfguytrfgytrdcvyredcvbnjuytfvb', '234567865434567', 'rtyuiofdsdfhjknbvcxsertyujvcdrtyu', 'rtyuikmnbvcdrtyuikjhgfrtyujnbvdtyhvcftyhbvfhb', 1585720579, 1585720579, NULL);
INSERT INTO `yin_sysconfig_pay` VALUES (3, 1, '团队1', 'http://pay.ckjdsak.cn/', 'http://pay.ckjdsak.cn/', 'http://pay.yulinzhihou.xyz/', '', '', '', '', '', '', 'boss1团队收款号', 'wx90588380da4a2bb0', '5e1df5e5002bfc5e190a74e0b438e7a6', '1583492131', '7e8763b61b23b4c42526e1055c2bbfb1', ' \'\'', 1585836515, 1585884803, NULL);

-- ----------------------------
-- Table structure for yin_team
-- ----------------------------
DROP TABLE IF EXISTS `yin_team`;
CREATE TABLE `yin_team`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '老板ID',
  `admin_username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '老板名称',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '老板手机号',
  `team_productions` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队产品编号',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '团队管理' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_team
-- ----------------------------
INSERT INTO `yin_team` VALUES (1, '团队1', 1, '老板1', '18812341234', '', 1585500035, 1585500035, NULL);
INSERT INTO `yin_team` VALUES (2, '团队2', 0, '老板2', '15207331234', '', 1585552703, 1585552703, NULL);
INSERT INTO `yin_team` VALUES (3, '团队3', 0, '老板3', '15207331235', '', 1585553496, 1585553496, NULL);
INSERT INTO `yin_team` VALUES (4, 'team3', 0, '老板3', '187123456', '', 1585561185, 1585561190, NULL);

-- ----------------------------
-- Table structure for yin_visit_filter
-- ----------------------------
DROP TABLE IF EXISTS `yin_visit_filter`;
CREATE TABLE `yin_visit_filter`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `team_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '团队ID',
  `team_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '团队名称',
  `app_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'IP识别地区接口',
  `filter_area` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '屏蔽地区',
  `is_filter` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启过滤,0=否，1=是',
  `jump_to` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '屏蔽后跳转',
  `createtime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deletetime` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '访问过滤设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_visit_filter
-- ----------------------------

-- ----------------------------
-- Table structure for yin_xunsearch_fields
-- ----------------------------
DROP TABLE IF EXISTS `yin_xunsearch_fields`;
CREATE TABLE `yin_xunsearch_fields`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NULL DEFAULT 0 COMMENT '项目ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '字段名称',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '字段标题',
  `type` enum('string','numeric','date','id','title','body') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  `index` enum('none','self','mixed','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '索引方式',
  `tokenizer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分词器',
  `cutlen` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '摘要结果截取长度',
  `weight` int(10) UNSIGNED NULL DEFAULT 1 COMMENT '混合区检索时的概率权重',
  `phrase` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'no' COMMENT '是否支持精确检索',
  `non_bool` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'no' COMMENT '强制指定是否为布尔索引',
  `extra` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否附属字段',
  `sortable` tinyint(1) NULL DEFAULT 1 COMMENT '是否允许排序',
  `createtime` int(10) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Xunsearch字段列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_xunsearch_fields
-- ----------------------------

-- ----------------------------
-- Table structure for yin_xunsearch_project
-- ----------------------------
DROP TABLE IF EXISTS `yin_xunsearch_project`;
CREATE TABLE `yin_xunsearch_project`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '项目名称',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '项目标题',
  `charset` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '编码',
  `serverindex` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '索引服务端',
  `serversearch` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '搜索服务端',
  `logo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'Logo',
  `indextpl` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '搜索页模板',
  `listtpl` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '列表页模板',
  `isaddon` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否插件',
  `isfuzzy` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否模糊搜索',
  `issynonyms` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否同义词搜索',
  `isfrontend` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否开启前台搜索',
  `isindexhotwords` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否首页热门搜索',
  `ishotwords` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否列表页热门搜索',
  `isrelatedwords` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否列表页相关搜索',
  `pagesize` int(10) UNSIGNED NULL DEFAULT 10 COMMENT '搜索分页大小',
  `createtime` int(10) NULL DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'Xunsearch配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yin_xunsearch_project
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
