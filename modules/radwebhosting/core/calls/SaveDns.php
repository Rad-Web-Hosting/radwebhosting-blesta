<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of TransferDomain
 *
 * @author inbs
 */
class SaveDns extends Call
{
    public $action = "domains/:domain/dns";
    
    public $type = parent::TYPE_POST;
}