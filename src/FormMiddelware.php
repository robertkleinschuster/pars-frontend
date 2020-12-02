<?php


namespace Pars\Frontend;


use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Pars\Core\Database\DatabaseMiddleware;
use Pars\Core\Localization\LocaleInterface;
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
                    if ($session instanceof SessionInterface) {
                        if ($session->get('voted')) {
                            return new RedirectResponse($request->getUri());
                        }
                    }
                    $poll = $request->getParsedBody()['poll'];
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
                            $paragraphProcessor = new CmsParagraphBeanProcessor($adapter);
                            $beanList = $paragraphFinder->getBeanFactory()->getEmptyBeanList();
                            $beanList->push($bean);
                            $paragraphProcessor->setBeanList($beanList);
                            $paragraphProcessor->save();
                            if ($session instanceof SessionInterface) {
                              #  $session->set('voted', true);
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
