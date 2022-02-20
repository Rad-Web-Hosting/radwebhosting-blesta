<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of GetNameServers
 *
 * @author inbs
 */
class TransferSync extends Call
{
    public $action = "domains/:domain/transfersync";
    
    public $type = parent::TYPE_POST;
}