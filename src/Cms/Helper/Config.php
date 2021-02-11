<?php


namespace Pars\Frontend\Cms\Helper;


use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Pars\Core\Cache\ParsCache;
use Pars\Model\Config\ConfigBeanFinder;

class Config implements AdapterAwareInterface
{
    use AdapterAwareTrait;

    /**
     * @var array|null
     */
    protected ?array $data = null;

    /**
     * config codes to load for frontend
     * @var array|string[]
     */
    protected array $codes = [
        'asset.domain',
        'asset.key',
        'locale.default',
        'frontend.brand',
        'frontend.keywords',
        'frontend.author',
        'frontend.domain',
        'frontend.charset',
        'frontend.favicon',
        'frontend.cache',
        'frontend.logo',
        'frontend.color',
        'frontend.timezone',
        'frontend.update',
        'frontend.google-key',
        'frontend.google-maps-key',
        'frontend.data-privacy-email',
    ];

    /**
     * Config constructor.
     */
    public function __construct(Adapter $adapter)
    {
        $this->setDbAdapter($adapter);
    }

    /**
     * @param string|null $key
     * @return array|mixed|null
     */
    public function get(string $key = null)
    {
        if ($this->data == null) {
            $cache = new ParsCache('frontend-config');
            if ($cache->has($key)) {
                return $cache->get($key);
            }
            $finder = new ConfigBeanFinder($this->adapter);
            $finder->setConfig_Code($this->codes);
            $this->data = $finder->getBeanList()->column('Config_Value', 'Config_Code');
            $cache->setMultiple($this->data, 3600);
        }
        if ($key === null) {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }


}
