# moephp

可能是最萌的php框架

## 特点

* 轻量
* MVC模式
* 结构简洁
* 可扩展
* 支持路由模式
* 萌啊 (｡・`ω´･)

## 概念

下载框架包，解压到网站根目录，或者clone一份到本地

```shell
git clone https://github.com/aliezc/moephp.git
```

### 主文件配置

编辑index.php，修改常量设置

**DBCONN**

PDO连接字符串，可参阅[PDO文档](http://php.net/manual/zh/pdo.connections.php)

eg. `mysql:host=127.0.0.1;dbname=test` 表示连接Mysql数据库，服务器地址是本地，数据库名是test

**DBUSER**

PDO连接用户名，用于连接数据库的用户名

eg. `root`  使用root用户连接数据库

**DBPWD**

PDO数据库密码

**DBUTF8**

是否使用utf8编码，默认是true

**URLMODE**

地址模式，定义服务器框架使用何种地址模式，默认为PATH_INFO模式

0 - PATH_INFO模式

本框架默认模式，也是大部分框架使用的模式，配置简单，不用写rewrite规则，Apache和Nginx都能很好兼容，地址看起来也不丑= =

eg. http://example.com/index.php/User/info/update

1 - Rewrite模式

使用Rewrite重写地址请求实现伪静态，需要写Nginx或Apache的规则，地址显示比较好看

eg. http://example.com/User/info/update

2 - 传统模式

使用地址请求参数，兼容性好，比较丑，而且不利于SEO

eg. http://example.com/?m=User&s=info&a=update

**APPPATH**

应用目录，用于存放控制器脚本

eg. app

**LIBPATH**

第三方库存放路径，如果是单文件的库直接放在该路径下，如果是多文件，放在子目录下，并主入口文件为index.php

eg. lib

**HOST**

域名，调试时可以用localhost

eg. example.com

**INDEX**

主入口文件，本框架使用单文件入口（也可以多入口开发），该文件接收所有请求

eg. index.php

**SCHEME**

协议，默认http:

http:

**PUBLIC**

公共文件路径，存放公共文件，例如上传的文件或者编译的文件

eg. public

### 目录结构

```
./                                    根目录
    app/                            应用控制器
        Index/                    模块
            view/                    模块对应的视图
            index.php            控制器
    moe/                        框架主目录
    static/                        静态文件目录
    lib/                            第三方库
    public/                    公共目录
    .htaccess                Apache配置
```

### 控制器结构

控制器请求通过"模块 - 子模块 - 操作"的方式结构访问，**模块**为应用目录下的子目录，**子模块**为模块目录下的脚本，脚本有一个类，**操作**为子模块类里的公有静态方法

#### 模块

模块在应用目录下，一般以大写字母开头，默认模块名为`Index`

#### 子模块

子模块为模块下的脚本，一个模块可以有多个子模块，子模块为脚本文件名，子模块至少有一个对外类，对外类的类名为模块名_子模块名，例如`Index_index`默认子模块为`index`

#### 操作

操作为子模块对外类的一个公有静态方法，可以有多个操作，默认操作为`index`

结构表示为`/model/submodel/action`

全部默认，即*/Index/index/index*是主页

### 视图

视图文件夹位于每个模型下view目录内

视图分为主模板和子模板

#### 主模板

命名规则：`模板名.htm`

#### 子模板

命名规则：`主模板.子模板.htm`

## 开始开发

### 设计数据库

###  编写页面模板

将页面模板放到对应视图的位置

### 编写控制器代码

## API

### MOE::init()

初始化框架，在主文件调用

```php
MOE::init();
```

### MOE::url($url)

根据地址模式生成对应的地址

参数为PATH_INFO模式的地址

```php
MOE::url('/User/info/update');   => http://localhost/index.php/User/info/update
```

### MOE::query($sql, $params, $fetch)

执行一个带查询结果的SQL语句

* sql语句
* 预处理参数
* 数据获取模式

```php
$arr = MOE::query('select * from user');

$arr = MOE::query('select * from user where id=:id', array(":id" => 1));

$arr = MOE::query('select * from user where id=:id', array(":id" => 1), PDO::FETCH_BOTH);
```

### MOE::exec($sql, $params)

执行一个SQL语句，一般用于插入、更新、删除

* sql语句
* 预处理参数

```php
MOE::exec('delete from user where id=:id', array(":id" => 1));
```

### MOE::rand_str($length)

生成一个随机字符串，一般用于验证码

* 长度，默认4

```php
echo MOE::rand_str();
```

### MOE::md5_filename($id);

生成上传文件的md5文件名

* 文件的name

```php
// 比如$_FILES['a']

MOE::md5_filename('a');
```

### MOE::route($method, $path, $func);

添加路由规则
