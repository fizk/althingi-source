<?php

namespace Althingi\Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\{
    JsonResponse,
    EmptyResponse
};
use Althingi\Form;
use Althingi\Injector\ServiceSuperCategoryAwareInterface;
use Althingi\Service\SuperCategory;
use Althingi\Utils\ErrorFormResponse;
use Althingi\Router\{
    RestControllerInterface,
    RestControllerTrait
};

class SuperCategoryController implements
    RestControllerInterface,
    ServiceSuperCategoryAwareInterface
{
    use RestControllerTrait;
    private SuperCategory $superCategoryService;

    /**
     * @output \Althingi\Model\SuperCategory
     * @200 Success
     * @404 Resource not found
     */
    public function get(ServerRequest $request): ResponseInterface
    {
        $superCategory = $this->superCategoryService->get(
            $request->getAttribute('super_category_id')
        );
        return $superCategory
            ? new JsonResponse($superCategory)
            : new EmptyResponse(404);
    }

    /**
     * @output \Althingi\Model\SuperCategory[]
     * @206 Success
     */
    public function getList(ServerRequest $request): ResponseInterface
    {
        $superCategories = $this->superCategoryService->fetch();
        return new JsonResponse($superCategories, 206);
    }

    /**
     * @input \Althingi\Form\SuperCategory
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put(ServerRequest $request): ResponseInterface
    {
        $form = new Form\SuperCategory();
        $form->bindValues(array_merge($request->getParsedBody(), ['super_category_id' => $request->getAttribute('super_category_id')]));
        if ($form->isValid()) {
            $affectedRows = $this->superCategoryService->save($form->getObject());
            return new EmptyResponse($affectedRows === 1 ? 201 : 205);
        }

        return new ErrorFormResponse($form);
    }

    /**
     * @input \Althingi\Form\SuperCategory
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch(ServerRequest $request): ResponseInterface
    {
        if (($superCategory = $this->superCategoryService->get(
            $request->getAttribute('super_category_id')
        )) != null) {
            $form = new Form\SuperCategory();
            $form->bind($superCategory);
            $form->setData($request->getParsedBody());

            if ($form->isValid()) {
                $this->superCategoryService->update($form->getData());
                return new EmptyResponse(205);
            }

            return new ErrorFormResponse($form);
        }

        return new EmptyResponse(404);
    }

    public function setSuperCategoryService(SuperCategory $superCategory): self
    {
        $this->superCategoryService = $superCategory;
        return $this;
    }
}
