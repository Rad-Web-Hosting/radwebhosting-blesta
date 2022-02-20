<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of CheckAvailability
 *
 * @author inbs
 */
class CheckAvailability extends Call
{
    public $action = "domains/lookup";
    
    public $type = parent::TYPE_POST;
}