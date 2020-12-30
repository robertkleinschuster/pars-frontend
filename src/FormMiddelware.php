<?php


namespace Pars\Frontend;


use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Database\DatabaseMiddleware;
use Pars\Core\Localization\LocaleInterface;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
use Pars\Model\Cms\Paragraph\CmsParagraphBean;
use Pars\Model\Cms\Paragraph\CmsParagraphBeanFinder;
use Pars\Model\Cms\Paragraph\CmsParagraphBeanProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormMiddelware implements MiddlewareInterface
{
    /**
     * @param ContainerInterface $container
     * @return FormMiddelware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new FormMiddelware();
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $adapter = $request->getAttribute(DatabaseMiddleware::ADAPTER_ATTRIBUTE);
            $locale = $request->getAttribute(LocaleInterface::class);
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            if ($adapter instanceof Adapter && $locale instanceof LocaleInterface) {
                if (isset($request->getParsedBody()['poll'])) {
                    $poll = $request->getParsedBody()['poll'];
                    if (isset($request->getParsedBody()['name'])) {
                        $name = $request->getParsedBody()['name'];
                    } else {
                        $name = '';
                    }
                    $session->set('poll_name', $name);
                    $paragraphFinder = new CmsParagraphBeanFinder($adapter);
                    $paragraphFinder->setArticle_Code($poll);
                    if ($paragraphFinder->count() === 1) {
                        $bean = $paragraphFinder->getBean();
                        if ($bean instanceof CmsParagraphBean) {
                            if (!$bean->getArticle_Data()->exists('poll')) {
                                $bean->getArticle_Data()->set('poll', 1);
                            } else {
                                $number = $bean->getArticle_Data()->get('poll');
                                $bean->getArticle_Data()->set('poll', $number + 1);
                            }
                            if (!$bean->getArticle_Data()->exists('poll_names')) {
                                $bean->getArticle_Data()->set('poll_names', $name);
                            } else {
                                $names = $bean->getArticle_Data()->get('poll_names');
                                if (!empty($names)) {
                                    $names=  $names . ', ' . $name;
                                } else {
                                    $names = $name;
                                }
                                $bean->getArticle_Data()->set('poll_names', $names);
                            }
                            $paragraphProcessor = new CmsParagraphBeanProcessor($adapter);
                            $beanList = $paragraphFinder->getBeanFactory()->getEmptyBeanList();
                            $beanList->push($bean);
                            $paragraphProcessor->setBeanList($beanList);
                            $paragraphProcessor->save();
                            $code = $request->getAttribute('code', '/');
                            $pageFinder = new CmsPageBeanFinder($adapter);
                            $pageFinder->setCmsPageState_Code('active');
                            $pageFinder->setArticleTranslation_Code($code);
                            if ($pageFinder->findByLocaleWithFallback($locale->getLocale_Code(), 'de_AT') === 1) {
                                $bean = $pageFinder->getBean();
                                if ($bean->getArticle_Data()->exists('vote_once')
                                    && $bean->getArticle_Data()->get('vote_once') == 'true'
                                ) {
                                    $session->set('voted'. $bean->get('Article_Code'), true);
                                }
                            }
                            return new RedirectResponse($request->getUri());
                        }
                    }
                }
            }
        }
        return $handler->handle($request);
    }

}
