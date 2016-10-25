<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\IssueCategory as IssueCategoryForm;
use Althingi\Lib\ServiceIssueCategoryAwareInterface;
use Althingi\Service\IssueCategory;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;


class IssueCategoryController extends AbstractRestfulController implements
    ServiceIssueCategoryAwareInterface
{
    /**
     * @var \Althingi\Service\IssueCategory
     */
    private $issueCategoryService;

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        $form = (new IssueCategoryForm())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'category_id' => $categoryId]
            ));

        if ($form->isValid()) {
            $this->issueCategoryService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param IssueCategory $issueCategory
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
    }
}
