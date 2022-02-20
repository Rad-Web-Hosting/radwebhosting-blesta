<?php
namespace ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Calls;
use ModulesGarden\DomainsReseller\Registrar\radwebhosting\Core\Call;

/**
 * Description of GetNameServers
 *
 * @author inbs
 */
class ToggleIdProtect extends Call
{
    public $action = "domains/:domain/protectid";
    
    public $type = parent::TYPE_POST;
}