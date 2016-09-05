# moephp

可能是最萌的php框架

## 特点

* 轻量
* 伪MVC模式
* 结构简洁
* 可扩展
* 支持路由模式

## 概念

面向过程的php框架库

## 用法

下载框架包，解压到网站根目录，或者clone一份到本地

```shell
git clone https://github.com/aliezc/moephp.git
```

### 主文件配置

编辑index.php，修改常量设置

## API

### moe_route($rule, $phpfile)

创建路由规则，如`'/page/$page'`，如果匹配则执行后面的文件

### moe_pathinf()

返回PATH_INFO

### moe_pathargs($rule)

返回路由参数，同moe_route的规则

### moe_url($url)

根据设置格式化地址

### moe_pdo()

根据配置返回PDO对象

### moe_headers($arr)

输出headers，数组

### moe_json($arr)

输出json

### moe_session($k, $v=null)

获取或设置session，需要先执行session_start()

### moe_render($file, $args)

渲染试图模板，文件名省略'.htm'

### query_exists($arg)

检查$_GET是否包含某个参数，参数可以是字符串或数组

### moe_query($sql, $pre)

执行一个sql查询，第二个参数为预处理参数

### moe_stmt($sql, $pre)

同moe_query，但这个返回PDOStatement对象

### moe_one($sql, $pre)

同moe_query，但返回第一条结果

### moe_select($table, $pre, $select, $where, $order, $limit)

根据对象执行sql查询

### moe_sone($table, $pre, $select, $where, $order, $limit)

同moe_select，但只返回第一条数据

### moe_insert($table, $pre)

插入一条数据，第二个参数为对应字段的预处理数组

### moe_delete($table, $where, $pre)

删除数据

### moe_update($table, $where, $newdata, $pre)

更新数据

### moe_prepare($sql)

准备一个请求，返回PDOStatement

### moe_exec($stmt, $pre)

执行一个准备好的请求

### moe_rowcount($table, $where, $pre)

返回数据表行数

### moe_getkv($k, $table='baseinfo', $key='name', $value='value')

使用key-value模式查询数据

### moe_setkv($k, $v, $table='baseinfo', $key='name', $value='value')

使用key-value模式插入数据

## License

MIT