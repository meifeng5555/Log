# 基础信息
* 文件名：Log.php
* 类名：Log
* 作者：meijinfeng
* 功能：读写日志
* 邮箱：605590351@qq.com

# 成员属性
> * type 日志存储类型
> * size 日志存储上限
> * validTypeArr 日志存储类型集合
> * maxSize 日志存储最大上限
> * warningP 日志报警参数

# 类的方法
> * setSize($value) 设置存储上限
> * setType($value) 设置存储类型
> * getSize() 获取存储上限
> * getType() 获取存储类型
> * getValidTypeArr() 获取存储类型集
> * getMaxSize() 获取存储最大上限
> * write($filename, $data, $type = "Log", $append = true) 写日志
> * descAll($filename) 读取全部日志
> * descByTime($filename, $start, $end) 根据日期时间读取日志
> * descByTimeAndType($filename, $start, $end, $type) 根据日期时间和日志类型读取日志
