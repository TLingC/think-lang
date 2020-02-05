<?php
declare(strict_types = 1);

namespace tlingc\lang\middleware;

use Closure;
use think\App;
use think\Config;
use think\Lang;
use think\Request;
use think\Response;

class LoadLangPack
{
    protected $app;

    protected $lang;

    protected $config;

    protected $appPath;

    public function __construct(App $app, Lang $lang, Config $config)
    {
        $this->app  = $app;
        $this->lang = $lang;
        $this->config = $config;
        $this->appPath = $this->app->getAppPath();
    }

    public function handle($request, Closure $next)
    {
        $path = explode('/', $request->pathinfo());

        if (empty($path[0])) {
            $langset = $this->lang->detect($request);
            return redirect('/' . $langset . '/');
        } else {
            $langset = $path[0];
        }

        // 加载框架默认语言包
        $this->lang->load([
            $this->app->getThinkPath() . 'lang' . DIRECTORY_SEPARATOR . $langset . '.php',
        ]);

        $this->lang->setLangSet($langset);

        // 加载应用语言包
        $files = [];
        $files = array_merge(glob($this->appPath . 'lang' . DIRECTORY_SEPARATOR . $langset . '.*'),
                                glob($this->appPath . 'lang' . DIRECTORY_SEPARATOR . $langset . DIRECTORY_SEPARATOR . '*'),
                                glob($this->appPath . 'lang' . DIRECTORY_SEPARATOR . $langset . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*'));
        $this->lang->load($files);

        // 加载扩展（自定义）语言包
        $list = $this->config->get('lang.extend_list', []);

        if (isset($list[$langset])) {
            $this->lang->load($list[$langset]);
        }

        return $next($request);
    }
}