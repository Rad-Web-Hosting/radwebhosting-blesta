<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of GetAvailableTlds
 *
 * @author inbs
 */
class GetAvailableTlds extends Call
{
    public $action = "tlds";
    
    public $type = parent::TYPE_GET;
}