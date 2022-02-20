<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of TransferDomain
 *
 * @author inbs
 */
class SaveContactDetails extends Call
{
    public $action = "domains/:domain/contact";
    
    public $type = parent::TYPE_POST;
}
