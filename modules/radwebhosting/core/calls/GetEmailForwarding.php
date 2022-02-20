<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of GetEmailForwarding
 *
 * @author inbs
 */
class GetEmailForwarding extends Call
{
    public $action = "domains/:domain/email";
    
    public $type = parent::TYPE_GET;
}