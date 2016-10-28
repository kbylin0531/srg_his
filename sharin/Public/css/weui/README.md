WeUI 为微信 Web 服务量身设计  
====

[![Build Status](https://travis-ci.org/weui/weui.svg?branch=master)](https://travis-ci.org/weui/weui)
[![npm version](https://img.shields.io/npm/v/weui.svg)](https://www.npmjs.org/package/weui)
[![Gitter](https://badges.gitter.im/weui/weui.svg)](https://gitter.im/weui/weui?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## 概述

WeUI 是一套同微信原生视觉体验一致的基础样式库，由微信官方设计团队为微信 Web 开发量身设计，可以令用户的使用感知更加统一。包含`button`、`cell`、`dialog`、 `progress`、 `toast`、`article`、`actionsheet`、`icon`等各式元素。

## 视觉标准

[WeUI-Sketch](https://github.com/weui/weui-sketch)

## 手机预览

请用微信扫码

![https://weui.io](https://cloud.githubusercontent.com/assets/4652816/15662614/178efd46-2725-11e6-8952-09d7836e968d.png)

[https://weui.io](https://weui.io)

## 文档

WeUI 说明文档参考 [Wiki](https://github.com/weui/weui/wiki)

## License
The MIT License(http://opensource.org/licenses/MIT)

请自由地享受和参与开源

## 贡献

如果你有好的意见或建议，欢迎给我们提issue或pull request，为提升微信web体验贡献力量




## 我的文档
getting started
快速上手

获取 WeUI

WeUI 是一套与微信原生 UI 一致的 UI 库，核心文件是 weui.css，只需要获取到该文件，然后在页面中引入，即可使用 WeUI 的组件。有以下几种获取方式：

方式一（推荐）

微信官方、BootCDN 和 cdnjs 为 WeUI 提供了 CDN 链接，推荐使用，链接如下：

来源	地址
微信官方	//res.wx.qq.com/open/libs/weui/1.0.0/weui.css
微信官方	//res.wx.qq.com/open/libs/weui/0.4.2/weui.css
BootCDN	//cdn.bootcss.com/weui/0.4.2/style/weui.css
cdnjs	//cdnjs.cloudflare.com/ajax/libs/weui/0.4.2/style/weui.css
其中，1.0.0是目前 WeUI 最新的版本号，代码命名有较大的改变，因此保留0.4.2的CDN。

以上链接，均支持 http 和 https 协议，均包含未压缩版 weui.css 和压缩版 weui.min.css 。

方式二（bower）

可以通过 bower 进行下载，命令如下：

bower install --save weui
方式三（npm）

也可以通过 npm 进行下载，命令如下：

npm install --save weui
注意： bower、npm 依赖于 Node.js，请确保你的机器安装 Node.js 环境，安装方式参见 https://nodejs.org

方式四

可以在 https://github.com/weui/weui/releases 处，直接下载最新发布的版本。github 提供了 zip 和 tar.gz 两种格式的包，选择其中一种下载，解压后引用 dist/style/weui.css 文件即可。

方式五

也可以在 WeUI 的 github 主页，右上角的“Download ZIP”按钮，点击下载仓库中最新的代码，解压后使用方法同方式四。

注意： 该方式获取的是 WeUI 最新的、未经发布的代码，可能不稳定，不推荐通过此方式获取 WeUI 用于生产环境。

使用

通过以上方式获取到 WeUI 后，在页面中引入后即可使用。以 WeUI 的按钮为例：

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
        <title>WeUI</title>
        <!-- 引入 WeUI -->
        <link rel="stylesheet" href="path/to/weui/dist/style/weui.min.css"/>
    </head>
    <body>
        <!-- 使用 -->
        <a href="javascript:;" class="weui-btn weui-btn_primary">绿色按钮</a>
    </body>
</html>
更多的上手示例，请参考 http://codepen.io/progrape/pens/tags/2/?selected_tag=weui