<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Model\Cms\Paragraph\CmsParagraphBeanFinder;

class ParagraphModel extends BaseModel
{
    /**
     * @param string|null $code
     * @return \Niceshops\Bean\Type\Base\BeanInterface|null
     */
    public function getParagraph(?string $code = null, int $id = null)
    {
        try {
            if ($code == null) {
                $code = $this->getCode();
            }
            $paragraphFinder = new CmsParagraphBeanFinder($this->getAdapter());
            $paragraphFinder->setCmsParagraphState_Code('active');
            $paragraphFinder->setArticleTranslation_Active(true);
            if ($id === null) {
                $paragraphFinder->setArticleTranslation_Code($code);
            } else {
                $paragraphFinder->setCmsParagraph_ID($id);
            }
            if ($paragraphFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), $this->getConfig()->get('locale.default')) === 1) {
                return $paragraphFinder->getBean();
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error finding requested paragraph.', ['exception' => $exception]);
        }
        return null;
    }
}
