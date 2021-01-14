<?php


namespace Pars\Frontend\Cms\Helper;


use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
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
        'frontend.brand',
        'frontend.keywords',
        'frontend.author',
        'frontend.domain',
        'frontend.charset',
        'frontend.favicon',
        'frontend.color',
        'frontend.timezone',
        'frontend.update',
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
            $finder = new ConfigBeanFinder($this->adapter);
            $finder->setConfig_Code($this->codes);
            $this->data = $finder->getBeanList()->column('Config_Value', 'Config_Code');
        }
        if ($key === null) {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }


}
