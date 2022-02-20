<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of TransferDomain
 *
 * @author inbs
 */
class ModifyNameServer extends Call
{
    public $action = "domains/:domain/nameservers/modify";
    
    public $type = parent::TYPE_POST;
}