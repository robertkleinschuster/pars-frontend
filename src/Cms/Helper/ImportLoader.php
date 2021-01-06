<?php


namespace Pars\Frontend\Cms\Helper;


use Pars\Model\Import\ImportBeanFinder;

class ImportLoader
{
    protected ImportBeanFinder $finder;

    protected array $data = [];

    /**
     * ImportLoader constructor.
     * @param ImportBeanFinder $finder
     */
    public function __construct(ImportBeanFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke(int $article_ID)
    {
        if (!isset($this->data[$article_ID])) {
            $this->data[$article_ID] = $this->finder->setArticle_ID($article_ID)->getBeanList()->column('Import_Data');
        }
        return $this->data[$article_ID];
    }
}
