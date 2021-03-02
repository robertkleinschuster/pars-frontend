<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Frontend\Cms\Form\FormFactory;
use Pars\Model\Cms\Block\CmsBlockBeanFinder;

class BlockModel extends BaseModel
{
    /**
     * @param string|null $code
     * @return \Niceshops\Bean\Type\Base\BeanInterface|null
     */
    public function getBlock(?string $code = null, int $id = null)
    {
        try {
            if ($code == null) {
                $code = $this->getCode();
            }
            $blockFinder = new CmsBlockBeanFinder($this->getAdapter());
            $blockFinder->setCmsBlockState_Code('active');
            $blockFinder->setArticleTranslation_Active(true);
            if ($id === null) {
                $blockFinder->setArticleTranslation_Code($code);
            } else {
                $blockFinder->setCmsBlock_ID($id);
            }
            if ($blockFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), $this->getConfig()->get('locale.default')) === 1) {
                return $blockFinder->getBean();
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error finding requested block.', ['exception' => $exception]);
        }
        return null;
    }

    public function getForm()
    {
        $block = $this->getBlock();
        $factory = new FormFactory();
        return $factory->createFormForBlock(
            $block,
            $this->getAdapter(),
            $this->getSession(),
            $this->getGuard(),
            $this->getTranslator()
        );
    }
}
