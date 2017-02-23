<?php

namespace Pim\Bundle\ExcelInitBundle\Tasklet;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Oro\Bundle\UserBundle\Entity\Repository\GroupRepository;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Connector\Step\TaskletInterface;
use PimEnterprise\Bundle\SecurityBundle\Manager\LocaleAccessManager;
use PimEnterprise\Component\Security\Attributes;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LocalesAccessTasklet implements StepExecutionAwareInterface, TaskletInterface
{
    /** @var StepExecution */
    protected $stepExecution;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var LocaleAccessManager */
    protected $localeAccessManager;

    /** @var GroupRepository */
    protected $groupRepository;

    /**
     * @param LocaleRepositoryInterface $localeRepository
     * @param GroupRepository           $groupRepository
     * @param LocaleAccessManager       $localeAccessManager
     */
    public function __construct(
        LocaleRepositoryInterface $localeRepository,
        GroupRepository $groupRepository,
        LocaleAccessManager $localeAccessManager
    )
    {
        $this->localeRepository = $localeRepository;
        $this->localeAccessManager = $localeAccessManager;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param StepExecution $stepExecution
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }

    /**
     * Grant EDIT_ITEMS access to all locales
     */
    public function execute()
    {
        $locales = $this->localeRepository->getActivatedLocales();
        $groupAll = $this->groupRepository->findOneBy(['name' => 'All']);

        foreach ($locales as $locale) {
            $this->localeAccessManager->grantAccess($locale, $groupAll, Attributes::EDIT_ITEMS);
        }
    }
}
