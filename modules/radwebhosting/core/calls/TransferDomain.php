<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of TransferDomain
 *
 * @author Paweł Złamaniec <pawel.zl@modulesgarden.com>
 */
class TransferDomain extends Call
{
    public $action = "order/domains/transfer";
    
    public $type = parent::TYPE_POST;
}