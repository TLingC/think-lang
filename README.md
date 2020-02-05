# think-lang
[![](https://img.shields.io/packagist/v/tlingc/think-lang.svg)](https://packagist.org/packages/tlingc/think-lang)
[![](https://img.shields.io/packagist/dt/tlingc/think-lang.svg)](https://packagist.org/packages/tlingc/think-lang)
[![](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE.md)

ThinkPHP 6.0 多语言优化扩展包

## 特色
1. 支持通过如 `mywebsite.com/zh-hans/` 的二级目录访问对应语言页面。
2. 支持每种语言的语言包单独成目录，目录下可设有二级目录。

## 安装
```
composer require tlingc/think-lang
```

## 使用

### 基础配置
请参照 [官方文档](https://www.kancloud.cn/manual/thinkphp6_0/1037637) 进行相关配置。**同时注意如下与官方文档的不同点。**

#### 开启和加载语言包
中间件名称为：
```php
'tlingc\lang\middleware\LoadLangPack',
```
由于多语言通过二级目录访问，`使用Cookie保存语言` 功能无效。

#### 语言文件定义
自动加载的应用语言文件：

```php
// 单应用模式
app\lang\当前语言.php
app\lang\当前语言\*.php
app\lang\当前语言\*\*.php

// 多应用模式
app\应用\lang\当前语言.php
app\应用\lang\当前语言\*.php
app\应用\lang\当前语言\*\*.php
```

请注意此扩展包没有对语言包解析行为进行修改，语言文件的文件名不会影响语言分组，在多个文件中存在相同定义时会导致被覆盖。

### 路由设置
使用二级目录访问对应语言页面，必须使用路由定义，同时建议开启 `强制路由` 模式。
```php
use think\facade\Config;

Route::view('/', 'index/index');

$langs = Config::get('lang.allow_lang_list');
foreach($langs as $lang){
	Route::rule($lang . '/', 'index/index');
	Route::rule($lang . '/welcome', 'index/welcome');
}
```

### 重写 `url` 助手函数
在应用公共文件 `common.php` 中加入。
```php
use think\facade\Request;
use think\facade\Lang;
use think\facade\Route;
use think\route\Url as UrlBuild;

function url(string $url = '', array $vars = [], $suffix = true, $domain = false, $lang = true, $replace = false): UrlBuild
{
	if (!$lang) {
		if($replace) {
			$explode = explode('/', Request::url(), 3);
			$url = $url . $explode[2];
		}
		return Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
	}
	$lang = Lang::getLangSet();
	return Route::buildUrl('/' . $lang . $url, $vars)->suffix($suffix)->domain($domain);
}
```
对比官方提供的助手函数，增加了 `$lang` 及 `$replace` 参数。

普通跳转，生成url时会自动带上前方的语言名称。
```php
url('/welcome')
```

如需仅替换url中的语言名称（如在语言选择器中使用），把 `$replace` 参数置为 `true` 即可。

## TODO
- [ ] 整合路由定义方法。
- [ ] 整合重写`url`参数。

## 协议
MIT
